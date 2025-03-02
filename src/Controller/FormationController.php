<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Module;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
            $module = $formation->getModule();
    
            if (!$module) {
                $this->addFlash('error', 'Veuillez sélectionner un module pour la formation.');
                return $this->render('admin/formation/ajouter.html.twig', [
                    'form' => $form->createView(),
                    'modules' => $modules,
                ]);
            }
    
            // Récupération de la latitude et longitude depuis le formulaire
            $latitude = $request->request->get('latitude');
            $longitude = $request->request->get('longitude');
    
            if (!$latitude || !$longitude) {
                $this->addFlash('error', 'Veuillez sélectionner un emplacement sur la carte.');
                return $this->render('admin/formation/ajouter.html.twig', [
                    'form' => $form->createView(),
                    'modules' => $modules,
                ]);
            }
    
            // Vérification de la durée des formations dans le module
            $formations = $module->getFormations();
            $dureeFormation = array_sum(array_map(fn($formation) => $formation->getDureeFormation(), $formations->toArray()));
    
            if ($dureeFormation + $formation->getDureeFormation() > $module->getNbHeures()) {
               $this->addFlash('error', 'La durée totale des formations dépasse la durée maximale du module.');
    
                return $this->render('admin/formation/ajouter.html.twig', [
                    'form' => $form->createView(),
                    'modules' => $modules,
                ]);
            }
    
            // Sauvegarde de la formation
            $entityManager->persist($formation);
            $entityManager->flush();
    
            // 🔹 Ajout de la sauvegarde des coordonnées AVANT la redirection 🔹
            $filePath = $this->getParameter('kernel.project_dir') . '/public/coordinates.json';
            $coordinates = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
    
            $coordinates[$formation->getId()] = [
                'lat' => $latitude,
                'lng' => $longitude,
            ];
    
            file_put_contents($filePath, json_encode($coordinates, JSON_PRETTY_PRINT));
    
            // Redirection après l'ajout de la formation
            return $this->redirectToRoute('admin_formations_modules');
        }
    
        return $this->render('admin/formation/ajouter.html.twig', [
            'form' => $form->createView(),
            'modules' => $modules,
            'afficherCarte' => in_array($formation->getModeFormation(), ['Présentiel', 'Hybride']),
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
    public function modifierFormation(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupération de la formation
        $formation = $entityManager->getRepository(Formation::class)->find($id);
    
        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée');
        }
    
        // Lire le fichier JSON pour récupérer les coordonnées actuelles
        $filePath = $this->getParameter('kernel.project_dir') . '/public/coordinates.json';
        $coordinates = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
    
        // Charger les coordonnées existantes ou mettre une valeur par défaut
        $latitude = $coordinates[$id]['lat'] ?? 35.505;
        $longitude = $coordinates[$id]['lng'] ?? 9.734;
    
        // Création du formulaire
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération de la nouvelle latitude et longitude depuis le formulaire
            $newLatitude = $request->request->get('latitude', $latitude);
            $newLongitude = $request->request->get('longitude', $longitude);
    
            if (!$newLatitude || !$newLongitude) {
                $this->addFlash('error', 'Veuillez sélectionner un emplacement sur la carte.');
                return $this->render('admin/formation/modifier.html.twig', [
                    'form' => $form->createView(),
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);
            }
    
            // Sauvegarde des modifications dans la base de données
            $entityManager->flush();
    
            // Mise à jour des coordonnées dans le fichier JSON
            $coordinates[$id] = [
                'lat' => $newLatitude,
                'lng' => $newLongitude,
            ];
            file_put_contents($filePath, json_encode($coordinates, JSON_PRETTY_PRINT));
    
            $this->addFlash('success', 'Formation modifiée avec succès.');
            return $this->redirectToRoute('admin_formations_modules');
        }
    
        return $this->render('admin/formation/modifier.html.twig', [
            'form' => $form->createView(),
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }
    
    #[Route('/formation/ressources', name: 'formation_ressources')]
public function ressources(): Response
{
    return $this->render('admin/formation/ressources.html.twig');
}
#[Route('/formations/{id}', name: 'formation_details')]
public function details(EntityManagerInterface $entityManager, int $id): Response
{
    $formation = $entityManager->getRepository(Formation::class)->find($id);

    if (!$formation) {
        throw $this->createNotFoundException('Formation non trouvée');
    }

    // Lire le fichier JSON contenant les coordonnées
    $filePath = $this->getParameter('kernel.project_dir') . '/public/coordinates.json';
    $coordinates = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];

    // Récupérer les coordonnées selon l'ID ou mettre une valeur par défaut
    $latitude = $coordinates[$id]['lat'] ?? 35.505;
    $longitude = $coordinates[$id]['lng'] ?? 9.734;

    return $this->render('main/formation_details.html.twig', [
        'formation' => $formation,
        'latitude' => $latitude,
        'longitude' => $longitude,
    ]);
}
#[Route('/api/formations/search', name: 'search_formations')]
public function searchFormations(Request $request, FormationRepository $formationRepository): JsonResponse
{
    $query = $request->query->get('q', '');
    $formations = $formationRepository->createQueryBuilder('f')
        ->where('f.nomFormation LIKE :query')
        ->setParameter('query', "%$query%")
        ->getQuery()
        ->getResult();

    return $this->json($formations);
}

}

