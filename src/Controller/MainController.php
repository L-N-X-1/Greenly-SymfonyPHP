<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Module;
use App\Entity\Produit;
use App\Entity\Sponsor;
use App\Entity\Event;
use App\Entity\Commande;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'Page d\'accueil',
        ]);
    }

    #[Route('/dons', name: 'app_dons')]
    public function dons(): Response
    {
        return $this->render('main/dons.html.twig');
    }

    #[Route('/associations', name: 'app_associations')]
    public function associations(EntityManagerInterface $entityManager): Response
    {
        $associations = $entityManager->getRepository(User::class)->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_ASSOCIATION%')
            ->getQuery()
            ->getResult();

        return $this->render('main/associations.html.twig', [
            'associations' => $associations,
        ]);
    }

    #[Route('/evenements', name: 'app_evenements')]
    public function evenements(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('main/evenements.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/sponsors', name: 'app_sponsors')]
    public function sponsors(EntityManagerInterface $entityManager): Response
    {
        $sponsors = $entityManager->getRepository(Sponsor::class)->findAll();

        return $this->render('main/sponsors.html.twig', [
            'sponsors' => $sponsors,
        ]);
    }

    #[Route('/produits', name: 'app_produits')]
    public function produits(EntityManagerInterface $entityManager): Response
    {
        $produits = $entityManager->getRepository(Produit::class)->findAll();

        return $this->render('main/produits.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/commandes', name: 'app_commandes')]
    public function commandes(EntityManagerInterface $entityManager): Response
    {
        $commandes = $entityManager->getRepository(Commande::class)->findAll();

        return $this->render('main/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }
    
    //#[Route('/formations', name: 'app_formations')]
    public function formations(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager->getRepository(Formation::class)->findAll();

        return $this->render('main/formations.html.twig', [
            'formations' => $formations
        ]);
    }

    //#[Route('/modules', name: 'app_modules')]
    public function modules(EntityManagerInterface $entityManager): Response
    {
        $modules = $entityManager->getRepository(Module::class)->findAll();

        return $this->render('main/modules.html.twig', [
            'modules' => $modules
        ]);
    }
}
