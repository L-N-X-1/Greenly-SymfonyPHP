<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Module;
use App\Form\FormationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class FormationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Injecter EntityManagerInterface via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/formation', name: 'app_formation')]
    public function index(): Response
    {
        return $this->render('formation/index.html.twig', [
            'controller_name' => 'FormationController',
        ]);
    }

    #[Route('/formation/ajouter', name: 'ajouter_formation')]
    public function ajouterFormation(Request $request): Response
    {
        // Créer une nouvelle instance de Formation
        $formation = new Formation();
        
        // Créer le formulaire avec le Type de formulaire FormationType
        $form = $this->createForm(FormationType::class, $formation);
    
        // Traiter la requête HTTP et vérifier si le formulaire est soumis et valide
        $form->handleRequest($request);
    
        // Récupérer toutes les formations (ou celles liées à un module spécifique si nécessaire)
        $formations = $this->entityManager->getRepository(Formation::class)->findAll();
    
        // Récupérer tous les modules (pour les afficher dans la vue)
        $modules = $this->entityManager->getRepository(Module::class)->findAll();
    
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder la formation dans la base de données
            $this->entityManager->persist($formation);
            $this->entityManager->flush();
    
            // Rediriger vers une autre page (par exemple, la liste des formations ou une confirmation)
            return $this->redirectToRoute('admin_formations_modules');
        }
    
        // Afficher la vue avec le formulaire, les formations et les modules
        return $this->render('admin/formation/ajouter.html.twig', [
            'form' => $form->createView(),
            'formations' => $formations, // Passer la variable formations à la vue
            'modules' => $modules,      // Passer la variable modules à la vue
        ]);
    }
    #[Route('/formation/supprimer/{id}', name: 'supprimer_formation')]
    public function supprimerFormation(int $id): RedirectResponse
    {
        // Trouver la formation à supprimer
        $formation = $this->entityManager->getRepository(Formation::class)->find($id);

        if ($formation) {
            // Supprimer la formation
            $this->entityManager->remove($formation);
            $this->entityManager->flush();

            $this->addFlash('success', 'Formation supprimée avec succès');
        } 

        // Rediriger vers la page des formations après suppression
        return $this->redirectToRoute('admin_formations_modules');
    }
    #[Route('/formation/modifier/{id}', name: 'modifier_formation')]
    public function modifierFormation(Request $request, int $id): Response
    {
        // Trouver la formation à modifier
        $formation = $this->entityManager->getRepository(Formation::class)->find($id);

        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée');
        }

        // Créer le formulaire pour modifier la formation
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, mettre à jour la formation
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();  // Effectuer la mise à jour dans la base de données

            // Rediriger vers la page des formations après modification
            $this->addFlash('success', 'Formation modifiée avec succès');
            return $this->redirectToRoute('admin_formations_modules');
        }

        return $this->render('admin/formation/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

