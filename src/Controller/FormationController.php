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
public function ajouterFormation(Request $request, EntityManagerInterface $entityManager): Response
{
    $formation = new Formation();
    $form = $this->createForm(FormationType::class, $formation);
    $form->handleRequest($request);

    // Récupérer tous les modules
    $modules = $entityManager->getRepository(Module::class)->findAll();

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer le module sélectionné pour cette formation
        $module = $formation->getModule();

        // Si aucun module n'est sélectionné, afficher un message d'erreur
        if (!$module) {
            $this->addFlash('error', 'Veuillez sélectionner un module pour la formation.');
            return $this->render('admin/formation/ajouter.html.twig', [
                'form' => $form->createView(),
                'modules' => $modules,
            ]);
        }

        // Calcul de la durée totale des formations déjà existantes dans ce module
        $formations = $module->getFormations();
        $dureeFormation = array_sum(array_map(fn($formation) => $formation->getDureeFormation(), $formations->toArray()));

        // Vérifier si la durée totale dépasse la capacité du module
        if ($dureeFormation + $formation->getDureeFormation() > $module->getNbHeures()) {
            // Afficher une notification ou un message d'erreur
            $this->addFlash('error', 'La durée totale des formations dépasse la durée maximale du module. Veuillez modifier la durée de la formation ou la déplacer vers un autre module.');

            // Retourner à la même page sans enregistrer la formation
            return $this->render('admin/formation/ajouter.html.twig', [
                'form' => $form->createView(),
                'modules' => $modules,
            ]);
        }

        // Si tout est valide, enregistrer la formation
        $entityManager->persist($formation);
        $entityManager->flush();

        // Rediriger vers une autre page (exemple: liste des formations)
        return $this->redirectToRoute('admin_formations_modules');
    }

    // Si le formulaire n'est pas soumis ou n'est pas valide, afficher la page de formulaire
    return $this->render('admin/formation/ajouter.html.twig', [
        'form' => $form->createView(),
        'modules' => $modules,
    ]);
}


    #[Route('/formation/supprimer/{id}', name: 'supprimer_formation')]
    public function supprimerFormation(int $id): RedirectResponse
    {
        
        $formation = $this->entityManager->getRepository(Formation::class)->find($id);

        if ($formation) {
            
            $this->entityManager->remove($formation);
            $this->entityManager->flush();

            $this->addFlash('success', 'Formation supprimée avec succès');
        } 

        
        return $this->redirectToRoute('admin_formations_modules');
    }
    #[Route('/formation/modifier/{id}', name: 'modifier_formation')]
    public function modifierFormation(Request $request, int $id): Response
    {
        
        $formation = $this->entityManager->getRepository(Formation::class)->find($id);

        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée');
        }

        
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();  

            
            $this->addFlash('success', 'Formation modifiée avec succès');
            return $this->redirectToRoute('admin_formations_modules');
        }

        return $this->render('admin/formation/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/formation/ressources', name: 'formation_ressources')]
public function ressources(): Response
{
    return $this->render('admin/formation/ressources.html.twig');
}

}

