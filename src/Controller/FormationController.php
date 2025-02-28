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
    public function ajouterFormation(Request $request): Response
    {
        
        $formation = new Formation();
        
        
        $form = $this->createForm(FormationType::class, $formation);
    
        
        $form->handleRequest($request);
    
        
        $formations = $this->entityManager->getRepository(Formation::class)->findAll();
    
        
        $modules = $this->entityManager->getRepository(Module::class)->findAll();
    
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->persist($formation);
            $this->entityManager->flush();
    
            
            return $this->redirectToRoute('admin_formations_modules');
        }
    
        
        return $this->render('admin/formation/ajouter.html.twig', [
            'form' => $form->createView(),
            'formations' => $formations, 
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

