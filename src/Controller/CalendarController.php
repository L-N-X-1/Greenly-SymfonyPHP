<?php

namespace App\Controller;

use App\Service\GoogleCalendarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    private GoogleCalendarService $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    #[Route('/calendar', name: 'calendar_events')]
    public function index(): JsonResponse
    {
        try {
            if (!$this->googleCalendarService->isAuthenticated()) {
                return $this->json(['error' => 'Not authenticated with Google Calendar'], 401);
            }
            
            $events = $this->googleCalendarService->getEvents();
            return $this->json($events);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
    
    #[Route('/calendar/view', name: 'calendar_view')]
    public function view(): Response
    {
        try {
            if (!$this->googleCalendarService->isAuthenticated()) {
                return $this->render('calendar/index.html.twig', [
                    'events' => [],
                    'authenticated' => false
                ]);
            }
            
            $events = $this->googleCalendarService->getEvents();
            return $this->render('calendar/index.html.twig', [
                'events' => $events,
                'authenticated' => true
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error: ' . $e->getMessage());
            return $this->render('calendar/index.html.twig', [
                'events' => [],
                'authenticated' => false
            ]);
        }
    }
}