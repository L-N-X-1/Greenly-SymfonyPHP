<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Service\GoogleCalendarService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleAuthController extends AbstractController
{
    private GoogleCalendarService $googleCalendarService;
    private EventRepository $eventRepository;

    public function __construct(
        GoogleCalendarService $googleCalendarService,
        EventRepository $eventRepository
    ) {
        $this->googleCalendarService = $googleCalendarService;
        $this->eventRepository = $eventRepository;
    }

    #[Route('/google/auth', name: 'google_auth')]
    public function auth(): Response
    {
        if ($this->googleCalendarService->isAuthenticated()) {
            return $this->redirectToRoute('calendar_view');
        }

        $authUrl = $this->googleCalendarService->getAuthUrl();
        return $this->redirect($authUrl);
    }

    #[Route('/oauth2callback', name: 'oauth2callback')]
    public function callback(Request $request): Response
    {
        $code = $request->query->get('code');
        $error = $request->query->get('error');
        
        if ($error) {
            $this->addFlash('error', 'Google Calendar authorization failed: ' . $error);
            return $this->render('calendar/auth_callback.html.twig');
        }
        
        if (empty($code)) {
            $this->addFlash('error', 'No authorization code was provided');
            return $this->render('calendar/auth_callback.html.twig');
        }
        
        try {
            $this->googleCalendarService->handleAuthCode($code);
            $this->addFlash('success', 'Successfully authenticated with Google Calendar!');

            // Check if there's a pending event to add
            $pendingEventId = $request->getSession()->get('pending_event_id');
            if ($pendingEventId) {
                $event = $this->eventRepository->find($pendingEventId);
                if ($event) {
                    try {
                        $this->googleCalendarService->addEvent(
                            $event->getEventName(),
                            $event->getEventDescription(),
                            $event->getEventDate(),
                            $event->getEventLocation()
                        );
                        $this->addFlash('success', 'Event was successfully added to your Google Calendar!');
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Failed to add event to calendar: ' . $e->getMessage());
                    }
                }
                $request->getSession()->remove('pending_event_id');
            }

            return $this->render('calendar/auth_callback.html.twig');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Authorization error: ' . $e->getMessage());
            return $this->render('calendar/auth_callback.html.twig');
        }
    }
}