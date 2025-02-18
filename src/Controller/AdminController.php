<?php

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Repository\ModuleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/pages/dashboard.html.twig');
    }
    #[Route('/admin/utilisateurs', name: 'admin_utilisateurs')]
    public function utilisateurs(): Response
    {
        return $this->render('admin/pages/utilisateurs.html.twig');
    }

    #[Route('/admin/produits-commandes', name: 'admin_produits_commandes')]
    public function produitsCommandes(): Response
    {
        return $this->render('admin/pages/produits_commandes.html.twig');
    }

    #[Route('/admin/formations-modules', name: 'admin_formations_modules')]#[Route('/admin/formations-modules', name: 'admin_formations_modules')]
    public function formationsModules(FormationRepository $formationRepository, ModuleRepository $moduleRepository, Request $request): Response
    {
        // Nombre d'éléments par page
        $limit = 10;
    
        // Pagination des modules
        $currentPageModules = max(1, $request->query->getInt('module_page', 1));
        $offsetModules = ($currentPageModules - 1) * $limit;
    
        $modules = $moduleRepository->createQueryBuilder('m')
            ->setFirstResult($offsetModules)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    
        $totalModules = $moduleRepository->count([]);
        $totalPagesModules = ceil($totalModules / $limit);
    
        // Pagination des formations
        $currentPageFormations = max(1, $request->query->getInt('formation_page', 1));
        $offsetFormations = ($currentPageFormations - 1) * $limit;
    
        $formations = $formationRepository->createQueryBuilder('f')
            ->setFirstResult($offsetFormations)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    
        $totalFormations = $formationRepository->count([]);
        $totalPagesFormations = ceil($totalFormations / $limit);
    
        return $this->render('admin/pages/formations_modules.html.twig', [
            'formations' => $formations,
            'modules' => $modules,
            'currentPageModules' => $currentPageModules,
            'totalPagesModules' => $totalPagesModules,
            'currentPageFormations' => $currentPageFormations,
            'totalPagesFormations' => $totalPagesFormations,
        ]);
    }
    


    #[Route('/admin/dons-associations', name: 'admin_dons_associations')]
    public function donsAssociations(): Response
    {
        return $this->render('admin/pages/dons_associations.html.twig');
    }

    #[Route('/admin/evenements-sponsors', name: 'admin_evenements_sponsors')]
    public function evenementsSponsors(): Response
    {
        return $this->render('admin/pages/evenements_sponsors.html.twig');
    }
    #[Route('/admin/billing', name: 'admin_billing')]
    public function billing(): Response
    {
        return $this->render('admin/pages/billing.html.twig');
    }
    #[Route('/admin/icons', name: 'admin_icons')]
    public function icons(): Response
    {
        return $this->render('admin/pages/icons.html.twig');
    }
    #[Route('/admin/landing', name: 'admin_landing')]
    public function landing(): Response
    {
        return $this->render('admin/pages/landing.html.twig');
    }
    #[Route('/admin/map', name: 'admin_map')]
    public function map(): Response
    {
        return $this->render('admin/pages/map.html.twig');
    }
    #[Route('/admin/notifications', name: 'admin_notifications')]
    public function notifications(): Response
    {
        return $this->render('admin/pages/notifications.html.twig');
    }
    #[Route('/admin/profile', name: 'admin_profile')]
    public function profile(): Response
    {
        return $this->render('admin/pages/profile.html.twig');
    }
    #[Route('/admin/rtl', name: 'admin_rtl')]
    public function rtl(): Response
    {
        return $this->render('admin/pages/rtl.html.twig');
    }
    #[Route('/admin/sign-in', name: 'admin_sign-in')]
    public function sign(): Response
    {
        return $this->render('admin/pages/sign-in.html.twig');
    }
    #[Route('/admin/sign-up', name: 'admin_sign-up')]
    public function up(): Response
    {
        return $this->render('admin/pages/sign-up.html.twig');
    }
    #[Route('/admin/template', name: 'admin_template')]
    public function template(): Response
    {
        return $this->render('admin/pages/template.html.twig');
    }
    #[Route('/admin/typography', name: 'admin_typography')]
    public function typography(): Response
    {
        return $this->render('admin/pages/typography.html.twig');
    }
    #[Route('/admin/virtual-reality', name: 'admin_virtual-reality')]
    public function virtual(): Response
    {
        return $this->render('admin/pages/virtual-reality.html.twig');
    }
}
