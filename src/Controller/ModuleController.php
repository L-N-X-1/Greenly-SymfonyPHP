<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Module;
use App\Form\ModuleType;
use Doctrine\ORM\EntityManagerInterface; // Ajouter cette ligne
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; 

final class ModuleController extends AbstractController
{
    private $entityManager;

    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/module', name: 'app_module')]
    public function index(): Response
    {
        return $this->render('module/index.html.twig', [
            'controller_name' => 'ModuleController',
        ]);
    }

    #[Route('/module/ajouter', name: 'ajouter_module')]
    public function ajouterModule(Request $request): Response
    {
        
        $module = new Module();
        
        
        $form = $this->createForm(ModuleType::class, $module);
    
        
        $form->handleRequest($request);
    
        
        $formations = $this->entityManager->getRepository(Formation::class)->findAll();
        
        
        $modules = $this->entityManager->getRepository(Module::class)->findAll();
    
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($module);
            $this->entityManager->flush();
            return $this->redirectToRoute('admin_formations_modules');
        }
    
        
        return $this->render('admin/module/ajouter.html.twig', [
            'form' => $form->createView(),
            'formations' => $formations,
            'modules' => $modules,  
        ]);
    }
    #[Route('/module/modifier/{id}', name: 'modifier_module')]
    public function modifierModule(Request $request, int $id): Response
    {
        
        $module = $this->entityManager->getRepository(Module::class)->find($id);
    
        
        if (!$module) {
            $this->addFlash('error', 'Module non trouvé');
            return $this->redirectToRoute('app_module');
        }
    
       
        $form = $this->createForm(ModuleType::class, $module);
    
        
        $form->handleRequest($request);
    
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->flush();
    
            
            return $this->redirectToRoute('admin_formations_modules');
        }
    
        
        return $this->render('admin/module/modifier.html.twig', [
            'form' => $form->createView(),
            'module' => $module,  
        ]);
    }
    
    #[Route('/module/supprimer/{id}', name: 'supprimer_module')]
    public function supprimerModule(int $id): RedirectResponse
    {
        dump($id); 
        $module = $this->entityManager->getRepository(Module::class)->find($id);
        dump($module); 
    
        if ($module) {
            $this->entityManager->remove($module);
            $this->entityManager->flush();
    
            $this->addFlash('success', 'Module supprimé avec succès');
        } else {
            $this->addFlash('error', 'Module non trouvé');
        }
    
        return $this->redirectToRoute('admin_formations_modules');
    }
}