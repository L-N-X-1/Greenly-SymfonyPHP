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
use Symfony\Component\Routing\Annotation\Route; // Remarque : Utilise la bonne annotation si tu es sur Symfony 5+

final class ModuleController extends AbstractController
{
    private $entityManager;

    // Injecter EntityManagerInterface dans le constructeur
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
        // Créer une nouvelle instance de Module
        $module = new Module();
        
        // Créer le formulaire avec le Type de formulaire ModuleType
        $form = $this->createForm(ModuleType::class, $module);
    
        // Traiter la requête HTTP et vérifier si le formulaire est soumis et valide
        $form->handleRequest($request);
    
        // Récupérer les formations de la base de données
        $formations = $this->entityManager->getRepository(Formation::class)->findAll();
        
        // Récupérer les modules de la base de données
        $modules = $this->entityManager->getRepository(Module::class)->findAll();
    
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($module);
            $this->entityManager->flush();
            return $this->redirectToRoute('admin_formations_modules');
        }
    
        // Afficher la vue avec le formulaire, les formations et les modules
        return $this->render('admin/module/ajouter.html.twig', [
            'form' => $form->createView(),
            'formations' => $formations,
            'modules' => $modules,  // Ajouter ici la variable modules
        ]);
    }
    #[Route('/module/modifier/{id}', name: 'modifier_module')]
    public function modifierModule(Request $request, int $id): Response
    {
        // Récupérer le module à modifier
        $module = $this->entityManager->getRepository(Module::class)->find($id);
    
        // Si le module n'existe pas, rediriger vers la liste des modules
        if (!$module) {
            $this->addFlash('error', 'Module non trouvé');
            return $this->redirectToRoute('app_module');
        }
    
        // Créer le formulaire avec les données existantes
        $form = $this->createForm(ModuleType::class, $module);
    
        // Traiter la requête HTTP et vérifier si le formulaire est soumis et valide
        $form->handleRequest($request);
    
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les modifications dans la base de données
            $this->entityManager->flush();
    
            // Rediriger vers la liste des formations et modules
            return $this->redirectToRoute('admin_formations_modules');
        }
    
        // Afficher la vue avec le formulaire pour modifier le module
        return $this->render('admin/module/modifier.html.twig', [
            'form' => $form->createView(),
            'module' => $module,  // Passer le module à la vue
        ]);
    }
    
    #[Route('/module/supprimer/{id}', name: 'supprimer_module')]
    public function supprimerModule(int $id): RedirectResponse
    {
        dump($id); // Vérifier l'ID passé
        $module = $this->entityManager->getRepository(Module::class)->find($id);
        dump($module); // Vérifier quel module est récupéré
    
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