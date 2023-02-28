<?php

namespace App\Controller;

use App\Entity\TimelineStep;
use App\Form\TimelineStepType;
use App\Repository\TimelineStepRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/timeline-step')]
class TimelineStepController extends AbstractController
{
    #[Route('/', name: 'app_timeline_step_index', methods: ['GET'])]
    public function index(TimelineStepRepository $timelineStepRepository): Response
    {
        return $this->render('timeline_step/index.html.twig', [
            'timeline_steps' => $timelineStepRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_timeline_step_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TimelineStepRepository $timelineStepRepository): Response
    {
        $timelineStep = new TimelineStep();
        $form = $this->createForm(TimelineStepType::class, $timelineStep);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $timelineStepRepository->save($timelineStep, true);

            return $this->redirectToRoute('app_timeline_step_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('timeline_step/new.html.twig', [
            'timeline_step' => $timelineStep,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_timeline_step_show', methods: ['GET'])]
    public function show(TimelineStep $timelineStep): Response
    {
        return $this->render('timeline_step/show.html.twig', [
            'timeline_step' => $timelineStep,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_timeline_step_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TimelineStep $timelineStep, TimelineStepRepository $timelineStepRepository): Response
    {
        $form = $this->createForm(TimelineStepType::class, $timelineStep);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $timelineStepRepository->save($timelineStep, true);

            return $this->redirectToRoute('app_timeline_step_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('timeline_step/edit.html.twig', [
            'timeline_step' => $timelineStep,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_timeline_step_delete', methods: ['POST'])]
    public function delete(Request $request, TimelineStep $timelineStep, TimelineStepRepository $timelineStepRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timelineStep->getId(), $request->request->get('_token'))) {
            $timelineStepRepository->remove($timelineStep, true);
        }

        return $this->redirectToRoute('app_timeline_step_index', [], Response::HTTP_SEE_OTHER);
    }
}
