<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Service\GoogleCalendarService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/event')]
final class EventController extends AbstractController
{
    private GoogleCalendarService $googleCalendarService;

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    #[Route(name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $EventRepository): Response
    {
        return $this->render('main/evenements.html.twig', [
            'events' => $EventRepository->findNonAcheves(),
        ]);
    }

    #[Route('/new', name: 'app_event_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('admin_evenements_sponsors', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_evenements_sponsors', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/pages/event_edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/like', name: 'app_event_like', methods: ['POST'])]
    public function likeEvent(Event $event, EntityManagerInterface $entityManager): Response
    {
        try {
            $event->incrementLikes();
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'likes' => $event->getLikes()
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'An error occurred while processing your request'
            ], 500);
        }
    }

    #[Route('/{id}/dislike', name: 'app_event_dislike', methods: ['POST'])]
    public function dislikeEvent(Event $event, EntityManagerInterface $entityManager): Response
    {
        try {
            $event->incrementDislikes();
            $entityManager->flush();
            
            return $this->json([
                'success' => true,
                'dislikes' => $event->getDislikes()
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'error' => 'An error occurred while processing your request'
            ], 500);
        }
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_evenements_sponsors', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/add-to-calendar', name: 'app_event_add_to_calendar', methods: ['GET'])]
    public function addToGoogleCalendar(Request $request, Event $event): Response
    {
        if (!$this->googleCalendarService->isAuthenticated()) {
            // Store the event ID in session so we can retrieve it after authentication
            $request->getSession()->set('pending_event_id', $event->getId());
            return $this->redirectToRoute('google_auth');
        }

        try {
            $this->googleCalendarService->addEvent(
                $event->getEventName(),
                $event->getEventDescription(),
                $event->getEventDate(),
                $event->getEventLocation()
            );

            $this->addFlash('success', 'Event was successfully added to your Google Calendar!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to add event to Google Calendar: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_event_show', ['id' => $event->getId()]);
    }
}
