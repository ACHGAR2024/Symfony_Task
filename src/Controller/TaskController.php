<?php

namespace App\Controller;

use DateTime;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/task')]
class TaskController extends AbstractController
{

    #[Route('/task/index', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    #[Route('/', name: 'app_task_homeindex', methods: ['GET'])]
    public function homeindex(TaskRepository $taskRepository): Response
    {

        return $this->render('task/homeindex.html.twig', [
            'tasksTodo' => $taskRepository->findBy(['status' => 'new']),
            'tasksInProgress' => $taskRepository->findBy(['status' => 'in_progress']),
            'tasksDone' => $taskRepository->findBy(['status' => 'done']),
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/update-title/{id}', name: 'app_task_update_title', methods: ['POST'])]
    public function updateTitle(Request $request, Task $task, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $data = json_decode($request->getContent(), true);
        if (isset($data['title'])) {
            $task->setTitle($data['title']);
            $entityManager->flush();
            return new JsonResponse('Titre mis a jour avec succes');
        }

        return new JsonResponse('Erreur lors de la mise a jour du titre', 400);
    }

    #[Route('/update-description/{id}', name: 'app_task_update_description', methods: ['POST'])]
    public function updateDescription(Request $request, Task $task, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $data = json_decode($request->getContent(), true);
        if (isset($data['description'])) {
            $task->setDescription($data['description']);
            $entityManager->flush();
            return new JsonResponse('Description mise a jour avec succes');
        }

        return new JsonResponse('Erreur lors de la mise à jour de la description', 400);
    }
    #[Route("/update-date/{id}", name: "app_task_update_date", methods: ["POST"])]
    public function updateDate(Task $task, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $data = json_decode($request->getContent(), true);
        if (isset($data['date'])) {
            $dueDate = DateTime::createFromFormat('Y-m-d\TH:i', $data['date']);
            if (!$dueDate) {
                return new JsonResponse(['message' => 'Format de date invalide.'], 400);
            }
            $task->setDueDate($dueDate);
            $entityManager->flush();
            return new JsonResponse(['message' => 'Date mise à jour avec succès.']);
        }
        return new JsonResponse(['message' => 'Date non fournie.'], 400);
    }

    #[Route('/update-status/{id}/{status}', name: 'app_task_update_status', methods: ['POST'])]

    public function updateStatus(Task $task, string $status, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if (!in_array($status, ['new', 'in_progress', 'done'])) {
            return new JsonResponse(['message' => 'Statut invalide.'], 400);
        }

        $task->setStatus($status);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Statut mis à jour avec succès.']);
    }




}