<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Module;
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
    public function associations(): Response
    {
        return $this->render('main/associations.html.twig');
    }

    #[Route('/evenements', name: 'app_evenements')]
    public function evenements(): Response
    {
        return $this->render('main/evenements.html.twig');
    }

    #[Route('/sponsors', name: 'app_sponsors')]
    public function sponsors(): Response
    {
        return $this->render('main/sponsors.html.twig');
    }

    #[Route('/produits', name: 'app_produits')]
    public function produits(): Response
    {
        return $this->render('main/produits.html.twig');
    }

    #[Route('/commandes', name: 'app_commandes')]
    public function commandes(): Response
    {
        return $this->render('main/commandes.html.twig');
    }
    
    #[Route('/formations', name: 'app_formations')]
    public function formations(EntityManagerInterface $entityManager): Response
    {
        $formations = $entityManager->getRepository(Formation::class)->findAll();

        return $this->render('main/formations.html.twig', [
            'formations' => $formations
        ]);
    }

    #[Route('/modules', name: 'app_modules')]
    public function modules(EntityManagerInterface $entityManager): Response
    {
        $modules = $entityManager->getRepository(Module::class)->findAll();

        return $this->render('main/modules.html.twig', [
            'modules' => $modules
        ]);
    }

    #[Route('/inscription', name: 'app_inscription')]
    public function inscription(): Response
    {
        return $this->render('main/inscription.html.twig');
    }

    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('main/login.html.twig');
    }

   

    #[Route('/formations/{id}', name: 'formation_details')]
    public function details(EntityManagerInterface $entityManager, int $id): Response
    {
        $formation = $entityManager->getRepository(Formation::class)->find($id);

        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée');
        }

        return $this->render('main/formation_details.html.twig', [
            'formation' => $formation
        ]);
    }
    #[Route('/module/{id<\d+>}', name: 'module_details')]
    public function moduleDetails(int $id, EntityManagerInterface $entityManager): Response
    {
        $module = $entityManager->getRepository(Module::class)->find($id);
    
        if (!$module) {
            throw $this->createNotFoundException('Module non trouvé');
        }
    
        // Récupérer les formations associées au module
        $formations = $module->getFormations();
    
        // Calcul de la durée totale des formations
        $dureeFormation = array_sum(array_map(fn($formation) => $formation->getDureeFormation(), $formations->toArray()));
    
        $totalFormations = count($formations);
    
        return $this->render('main/module_details.html.twig', [
            'module' => $module,
            'formations' => $formations,
            'dureeFormation' => $dureeFormation,
            'totalFormations' => $totalFormations,
        ]);
    }
    
}
