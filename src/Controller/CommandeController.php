<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Form\CommandeupdateType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


class CommandeController extends AbstractController
{
    #[Route('/gestion/commandes', name: 'commande_index', methods: ['GET'])]
    public function index(Request $request, ProduitRepository $ProduitRepository, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $ProduitRepository->createQueryBuilder('c');

        $pagination = $paginator->paginate(
            $queryBuilder, 
            $request->query->getInt('page', 1),
            10 
        );

        return $this->render('commande/show.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/gestion/commandes/meuble', name: 'commande_meuble', methods: ['GET'])]
    public function meuble(Request $request, ProduitRepository $ProduitRepository, PaginatorInterface $paginator): Response
    {
        // Filtrer les produits qui appartiennent à la catégorie "meuble"
        $queryBuilder = $ProduitRepository->createQueryBuilder('p')
            ->where('p.categorie = :categorie')
            ->setParameter('categorie', 'meuble');
            
        $pagination = $paginator->paginate(
            $queryBuilder, 
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('commande/show.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/gestion/commandes/plastique', name: 'commande_plastique', methods: ['GET'])]
    public function plastique(Request $request, ProduitRepository $ProduitRepository, PaginatorInterface $paginator): Response
    {
        // Filtrer les produits qui appartiennent à la catégorie "plastique"
        $queryBuilder = $ProduitRepository->createQueryBuilder('p')
            ->where('p.categorie = :categorie')
            ->setParameter('categorie', 'plastique');
            
        $pagination = $paginator->paginate(
            $queryBuilder, 
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('commande/show.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/gestion/commandes/vetements', name: 'commande_vetements', methods: ['GET'])]
    public function vetements(Request $request, ProduitRepository $ProduitRepository, PaginatorInterface $paginator): Response
    {
        // Filtrer les produits qui appartiennent à la catégorie "Vêtement"
        $queryBuilder = $ProduitRepository->createQueryBuilder('p')
            ->where('p.categorie = :categorie')
            ->setParameter('categorie', 'Vêtement');
            
        $pagination = $paginator->paginate(
            $queryBuilder, 
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('commande/show.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    #[Route('/admin/commande', name: 'commande_indexx', methods: ['GET'])]
    public function indexx(Request $request,ProduitRepository $produitRepository,CommandeRepository $commandeRepository,PaginatorInterface $paginator): Response
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
    

    #[Route('/gestion/addformcommande/{id}', name: 'gestion_commande_addform')]
    public function addFormCommande(ManagerRegistry $doctrine, Request $request, ProduitRepository $produitRepository, int $id): Response
    {
        $em = $doctrine->getManager();

        $produit = $produitRepository->find($id);

        if (!$produit) {
            $this->addFlash('danger', 'Produit non trouvé.');
            return $this->redirectToRoute('liste_produits'); 
        }

        $commande = new Commande();
        $commande->setProduit($produit); 
        $commande->setMontant($produit->getPrix()); 
        $commande->setDate(new \DateTime()); 
        $commande->setStatut('non traité'); 

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($commande);
            $em->flush();

            $this->addFlash('success', 'Commande ajoutée avec succès !');
            return $this->redirectToRoute('commande_index', ['id' => $id]);
        }

        return $this->render('commande/index.html.twig', [
            'formadd' => $form->createView(),
            'produit' => $produit, 
        ]);
    }
    #[Route('/admin/commande/{id}', name: 'commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/admin/commande/{id}/edit', name: 'commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, ManagerRegistry $doctrine): Response
    {
        $form = $this->createForm(CommandeUpdateType::class, $commande);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
    
            if ($commande->getStatut() === 'livré') {
                $produit = $commande->getProduit();
                if ($produit !== null) {
                    
                    $commandes = $doctrine->getRepository(Commande::class)->findBy(['produit' => $produit]);
                    foreach ($commandes as $c) {
                        $entityManager->remove($c);
                    }
                    $entityManager->remove($produit);
                }
            }
    
            $entityManager->flush();
            $this->addFlash('success', 'Statut de la commande mis à jour avec succès !');
    
            return $this->redirectToRoute('produit_index');
        }
    
        return $this->render('commande/edit.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande,
        ]);
    }
    #[Route('/admin/commande/{id}', name: 'commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, ManagerRegistry $doctrine): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            $em = $doctrine->getManager();
            $em->remove($commande);
            $em->flush();
            $this->addFlash('success', 'Commande supprimée avec succès !');
        }

        return $this->redirectToRoute('commande_index');
    }

    #[Route('/gestion/commandes/sort', name: 'commande_sort', methods: ['GET'])]
    public function sort(Request $request, ProduitRepository $produitRepository, PaginatorInterface $paginator): Response
    {
        $categorie = $request->query->get('categorie', null); // Récupérer la catégorie sélectionnée
        
        // Vérifier si la catégorie est valide
        $queryBuilder = $produitRepository->createQueryBuilder('p')
            ->orderBy('p.prix', 'ASC'); 

        if ($categorie)
        {
            $queryBuilder->where('p.categorie = :categorie')
                ->setParameter('categorie', $categorie);
        }
        
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );
        
        return $this->render('commande/show.html.twig', [
            'pagination' => $pagination,
            'currentCategorie' => $categorie, 
        ]);
    }
    #[Route('/admin/commande/suivi', name: 'commande_suivi', methods: ['GET'])]
    public function suivi(Request $request,CommandeRepository $commandeRepository,PaginatorInterface $paginator): Response
    {
        $queryBuilder = $commandeRepository->createQueryBuilder('c')
            ->select('c.id, c.id_u, c.montant, c.statut, c.date, c.numerot');
            
        $paginationCommandes = $paginator->paginate(
            $queryBuilder->getQuery(), 
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('commande/suivi.html.twig', [
            'paginationCommandes' => $paginationCommandes,
        ]);
    }

}