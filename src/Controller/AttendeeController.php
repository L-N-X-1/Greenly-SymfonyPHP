<?php

namespace App\Controller;

use App\Entity\Attendee;
use App\Form\AttendeeType;
use App\Repository\AttendeeRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/attendee')]
final class AttendeeController extends AbstractController
{
    #[Route(name: 'app_attendee_index', methods: ['GET'])]
    public function index(AttendeeRepository $attendeeRepository): Response
    {
        return $this->render('attendee/index.html.twig', [
            'attendees' => $attendeeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_attendee_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        $attendee = new Attendee();
        
        // Get the event ID from the request and set it if available
        if ($request->query->has('event')) {
            $eventId = $request->query->get('event');
            $event = $entityManager->getRepository(\App\Entity\Event::class)->find($eventId);
            if ($event) {
                $attendee->setEvent($event);
            }
        }
        
        $form = $this->createForm(AttendeeType::class, $attendee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($attendee);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes maintenant inscrit à l\'événement!');
            
            // Render the events page directly
            return $this->render('main/evenements.html.twig', [
                'events' => $eventRepository->findAll(),
            ]);
        }

        return $this->render('attendee/new.html.twig', [
            'attendee' => $attendee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_attendee_show', methods: ['GET'])]
    public function show(Attendee $attendee): Response
    {
        return $this->render('attendee/show.html.twig', [
            'attendee' => $attendee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_attendee_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Attendee $attendee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AttendeeType::class, $attendee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_evenements_sponsors', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin\pages\attendee_edit.html.twig', [
            'attendee' => $attendee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_attendee_delete', methods: ['POST'])]
    public function delete(Request $request, Attendee $attendee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$attendee->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($attendee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_evenements_sponsors', [], Response::HTTP_SEE_OTHER);
    }
}
