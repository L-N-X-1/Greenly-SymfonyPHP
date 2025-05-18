<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\ProduitType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Knp\Component\Pager\PaginatorInterface;


class ProduitController extends AbstractController
{
    #[Route('/admin/produits', name: 'produit_index', methods: ['GET'])]
    public function index(Request $request,ProduitRepository $produitRepository,CommandeRepository $commandeRepository,PaginatorInterface $paginator): Response
    {
        $queryProduits = $produitRepository->createQueryBuilder('p');
        $paginationProduits = $paginator->paginate(
            $queryProduits, 
            $request->query->getInt('page', 1),
            10 
        );
    
        $queryCommandes = $commandeRepository->createQueryBuilder('c');
        $paginationCommandes = $paginator->paginate(
            $queryCommandes, 
            $request->query->getInt('page', 1),
            10 
        );
    
        return $this->render('produit/show.html.twig', [
            'paginationProduits' => $paginationProduits,
            'paginationCommandes' => $paginationCommandes,
        ]);
    }
    

    #[Route('/gestion/addformproduit', name: 'gestion_produit_addform')]
    public function addFormProduit(ManagerRegistry $doctrine, Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');
            return $this->redirectToRoute('gestion_produit_addform');
        }

        return $this->render('produit/index.html.twig', [
            'formadd' => $form->createView(),
        ]);
    }
    #[Route('/admin/produit/{id}', name: 'produit_show', methods: ['GET'])]//afficher un seul produit
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }
    #[Route('/gestion/produits', name: 'liste_produits')]
    public function indexx(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();
        
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }
    #[Route('/admin/produit/{id}/edit', name: 'produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doctrine->getManager()->flush();
            $this->addFlash('success', 'Produit mis à jour avec succès !');

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('produit/edit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    
    #[Route('/admin/produit/{id}', name: 'produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $em = $doctrine->getManager();
            $em->remove($produit);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé avec succès !');
        }

        return $this->redirectToRoute('produit_index');
    }
    #[Route('/admin/produits/search', name: 'produit_search', methods: ['GET'])]
    public function search(Request $request, ProduitRepository $produitRepository): JsonResponse
    {
        $searchTerm = $request->query->get('q', '');

        $queryBuilder = $produitRepository->createQueryBuilder('p')
            ->where('p.nom LIKE :search OR p.categorie LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->setMaxResults(10);

        $produits = $queryBuilder->getQuery()->getResult();

        $responseArray = [];
        foreach ($produits as $produit) {
            $responseArray[] = [
                'id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'prix' => $produit->getPrix(),
                'description' => $produit->getDescription(),
                'date' => $produit->getDate() ? $produit->getDate()->format('Y-m-d') : 'N/A',
                'categorie' => $produit->getCategorie(),
            ];
        }

        return $this->json($responseArray);
    }
    #[Route('/admin/produits/statistiques', name: 'produit_statistiques', methods: ['GET'])]
    public function statistiques(ProduitRepository $produitRepository): Response
    {
        $totalProduits = $produitRepository->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $stats = $produitRepository->createQueryBuilder('p')
            ->select('p.categorie AS categorie, COUNT(p.id) AS nombre')
            ->groupBy('p.categorie')
            ->getQuery()
            ->getResult();

        // Calcul des pourcentages
        foreach ($stats as &$stat) {
            $stat['pourcentage'] = $totalProduits > 0 ? round(($stat['nombre'] / $totalProduits) * 100, 2) : 0;
        }

        return $this->render('produit/statistiques.html.twig', [
            'stats' => $stats,
        ]);
    }

}
