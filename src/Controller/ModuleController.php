<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use Doctrine\ORM\EntityManagerInterface; // Ajouter cette ligne
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route; 
use Smalot\PdfParser\Parser;
use Knp\Snappy\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;

final class ModuleController extends AbstractController
{
    private $entityManager;

    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/modules', name: 'app_modules')]
    public function modules(EntityManagerInterface $entityManager): Response
    {
        $modules = $entityManager->getRepository(Module::class)->findAll();

        return $this->render('main/modules.html.twig', [
            'modules' => $modules
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
    #[Route('/quiz/{id<\d+>}', name: 'app_quiz')]
    public function quiz(int $id, ModuleRepository $moduleRepository): Response
    {
        $module = $moduleRepository->find($id);
    
        if (!$module) {
            throw $this->createNotFoundException("Module non trouvé");
        }
    
        // Normalisation stricte en minuscules avec suppression des espaces
        $categorie = mb_strtolower(trim($module->getCategorie()), 'UTF-8');
    
        $questions = $this->loadQuestions();
    
        // 🔍 Debugging (à supprimer en production)
        dump([
            "🔍 Catégorie demandée" => $categorie,
            "🔍 Catégories disponibles" => array_keys($questions)
        ]);
    
        // Vérification si la catégorie existe
        if (!isset($questions[$categorie])) {
            dump("⚠️ Catégorie non trouvée :", $categorie);
            return $this->render('admin/module/quiz.html.twig', [
                'categorie' => ucfirst($categorie),
                'questions' => [],
                'erreur' => "La catégorie '$categorie' n'existe pas."
            ]);
        }
    
        dump([
            "✔️ Catégorie trouvée" => $categorie,
            "🔍 Questions disponibles pour cette catégorie" => $questions[$categorie]
        ]);
    
        // Récupération des questions disponibles pour la catégorie
        $questionsDisponibles = $questions[$categorie];
    
        dump("✅ Questions trouvées :", $questionsDisponibles);
    
        return $this->render('admin/module/quiz.html.twig', [
            'categorie' => ucfirst($categorie),
            'questions' => $questionsDisponibles
        ]);
    }
    
#[Route('/quiz/{categorie}', name: 'quiz_module')]
public function quizByCategory(string $categorie, ModuleRepository $moduleRepository): Response
{
    $questions = $this->loadQuestions();

    // Normaliser la catégorie en minuscule et enlever les espaces
    $categorie = strtolower(trim($categorie));

    // Vérifier si la catégorie existe dans les questions
    if (!isset($questions[$categorie])) {
        $questionsDisponibles = [];
    } else {
        $questionsDisponibles = $questions[$categorie];
    }

    return $this->render('admin/module/quiz.html.twig', [
        'categorie' => ucfirst($categorie),
        'questions' => $questionsDisponibles
    ]);
}

private function loadQuestions(): array
{
    $filePath = $this->getParameter('kernel.project_dir') . '/public/data/questions.json';

    if (!file_exists($filePath) || !is_readable($filePath)) {
        throw new NotFoundHttpException("Le fichier de questions est introuvable ou illisible.");
    }

    $jsonContent = file_get_contents($filePath);
    $questions = json_decode($jsonContent, true);

    if (!is_array($questions)) {
        throw new \RuntimeException("Le fichier JSON est mal formaté.");
    }

    return $questions;
}

#[Route('/quiz/result/{categorie}', name: 'quiz_result', methods: ['POST','GET'])]
public function quizResult(Request $request, string $categorie): Response
{
    $questions = $this->loadQuestions();

    $categorie = strtolower(trim($categorie));

    if (!isset($questions[$categorie])) {
        throw $this->createNotFoundException("Aucune question trouvée pour cette catégorie.");
    }

    $questionsDisponibles = $questions[$categorie];

    $reponsesUtilisateur = $request->request->all();
    $score = 0;
    $total = count($questionsDisponibles);

    foreach ($questionsDisponibles as $index => $question) {
        // Comparer la réponse de l'utilisateur (en num) avec la bonne réponse (en num)
        if (isset($reponsesUtilisateur["q$index"]) && intval($reponsesUtilisateur["q$index"]) === $question['reponse']) {
            $score++;
        }
    }

    return $this->render('admin/module/result.html.twig', [
        'categorie' => ucfirst($categorie),
        'score' => $score,
        'total' => $total
    ]);
}


#[Route('/quiz/result/{categorie}/pdf', name: 'quiz_result_pdf')]
public function quizResultPdf(Request $request, string $categorie): Response
{
    $questions = $this->loadQuestions();
    $categorie = strtolower(trim($categorie));

    if (!isset($questions[$categorie])) {
        throw $this->createNotFoundException("Aucune question trouvée pour cette catégorie.");
    }

    $questionsDisponibles = $questions[$categorie];
    $reponsesUtilisateur = $request->query->all();
    $score = 0;
    $total = count($questionsDisponibles);

    foreach ($questionsDisponibles as $index => $question) {
        if (isset($reponsesUtilisateur["q$index"]) && intval($reponsesUtilisateur["q$index"]) === $question['reponse']) {
            $score++;
        }
    }

    // Générer la vue HTML
    $html = $this->renderView('admin/module/result_pdf.html.twig', [
        'categorie' => ucfirst($categorie),
        'questions' => $questionsDisponibles,
        'reponsesUtilisateur' => $reponsesUtilisateur,
        'score' => $score,
        'total' => $total
    ]);

    // Configuration de DomPDF
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf = new Dompdf($options);
    
    // Charger le HTML dans DomPDF
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Retourner le PDF en réponse HTTP
    return new Response($dompdf->output(), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="resultat_quiz.pdf"'
    ]);
}
#[Route('/game/{id}', name: 'app_game')]
public function game(int $id): Response
{
    // Logique pour charger le jeu avec l'ID du module
    return $this->render('admin/module/game/index.html.twig', [
        'moduleId' => $id
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
    #[Route('/extract-pdf', name: 'extract_pdf', methods: ['POST'])]
    public function extractPdfText(Request $request): Response
    {
        $file = $request->files->get('pdf_file');

        if (!$file) {
            return new Response('Aucun fichier fourni.', Response::HTTP_BAD_REQUEST);
        }

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($file->getPathname());
            $text = $pdf->getText();

            return new Response(nl2br(htmlspecialchars($text)));
        } catch (\Exception $e) {
            return new Response('Erreur lors de l’extraction du texte : ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/api/modules/search', name: 'search_modules')]
    public function searchModules(Request $request, ModuleRepository $moduleRepository): JsonResponse
    {
        $query = $request->query->get('q', '');
        $modules = $moduleRepository->createQueryBuilder('m')
            ->where('m.nom_module LIKE :query')
            ->setParameter('query', "%$query%")
            ->getQuery()
            ->getResult();
    
        $data = [];
        foreach ($modules as $module) {
            $data[] = [
                'id' => $module->getId(),
                'nomModule' => $module->getNomModule(),
                'descriptionModule' => $module->getDescriptionModule(),
                'NbHeures' => $module->getNbHeures(),
                'formations' => array_map(fn($f) => ['id' => $f->getId()], $module->getFormations()->toArray()),
            ];
        }
        return $this->json($data);
    }
    #[Route('/module/{id}/video', name: 'module_video')]
public function moduleVideo(int $id, EntityManagerInterface $entityManager): Response
{
    $module = $entityManager->getRepository(Module::class)->find($id);

    if (!$module) {
        throw $this->createNotFoundException('Module non trouvé');
    }

    // Liste des vidéos associées aux catégories
    $videos = [
        'plastique' => 'https://www.youtube.com/embed/IMGI0l66xxM',
        'verre' => 'https://www.youtube.com/embed/h_unmxyJmY8',
        'électronique' => 'https://www.youtube.com/embed/uKgNbwK15qk',
        'papier' => 'https://www.youtube.com/embed/HWJWziz6-oo',
        'métaux' => 'https://www.youtube.com/embed/143XL96maCo',
        'vetements' => 'https://www.youtube.com/embed/1OBhP-RPA-g',
        'meubles' => 'https://www.youtube.com/embed/C2BbCM04QiI',
    ];

    // Normaliser la catégorie
    $categorie = strtolower(trim($module->getCategorie()));

    // Vérifier si une vidéo correspond à la catégorie
    $videoUrl = $videos[$categorie] ?? null;

    return $this->render('admin/module/module_video.html.twig', [
        'module' => $module,
        'videoUrl' => $videoUrl,
    ]);
}


}
