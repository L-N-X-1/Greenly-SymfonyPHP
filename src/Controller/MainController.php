<?php

namespace App\Controller;

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
    #[Route('/transactions', name: 'app_transactions')]
    public function transactions(): Response
    {
        return $this->render('main/transactions.html.twig');
    }

    #[Route('/associations', name: 'app_associations')]
    public function associations(): Response
    {
        return $this->render('main/associations.html.twig');
    }

    #[Route('/evenements', name: 'app_evenements')]
    public function evenements(): Response
    {
        return $this->render('main/evenements.html.twig');
    }

    #[Route('/produits', name: 'app_produits')]
    public function contact(): Response
    {
        return $this->render('main/produits.html.twig');
    }
}
