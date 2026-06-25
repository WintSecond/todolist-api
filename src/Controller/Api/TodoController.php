<?php

namespace App\Controller\Api;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/todos')]
class TodoController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer
    ) {
    }

    // Read (список) — GET /api/todos
    #[Route('', methods: ['GET'])]
    public function list(TodoRepository $repository): JsonResponse
    {
        $todos = $repository->findAll();

        return new JsonResponse(
            $this->serializer->serialize($todos, 'json'),
            200,
            [],
            true
        );
    }

    // Read (одна задача) — GET /api/todos/{id}
    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TodoRepository $repository): JsonResponse
    {
        $todo = $repository->find($id);

        if (!$todo) {
            return new JsonResponse(['error' => 'Todo not found'], 404);
        }

        return new JsonResponse(
            $this->serializer->serialize($todo, 'json'),
            200,
            [],
            true
        );
    }

    // Create — POST /api/todos
    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];

        if (empty($payload['title'])) {
            return new JsonResponse(['error' => 'Field "title" is required'], 400);
        }

        $todo = new Todo();
        $todo->setTitle($payload['title']);
        $todo->setDescription($payload['description'] ?? null);
        $todo->setCompleted((bool) ($payload['completed'] ?? false));

        $this->em->persist($todo);
        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize($todo, 'json'),
            201,
            [],
            true
        );
    }

    // Update — PUT /api/todos/{id}
    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, TodoRepository $repository): JsonResponse
    {
        $todo = $repository->find($id);

        if (!$todo) {
            return new JsonResponse(['error' => 'Todo not found'], 404);
        }

        $payload = json_decode($request->getContent(), true) ?? [];

        if (isset($payload['title'])) {
            $todo->setTitle($payload['title']);
        }
        if (isset($payload['description'])) {
            $todo->setDescription($payload['description']);
        }
        if (isset($payload['completed'])) {
            $todo->setCompleted((bool) $payload['completed']);
        }

        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize($todo, 'json'),
            200,
            [],
            true
        );
    }

    // Delete — DELETE /api/todos/{id}
    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, TodoRepository $repository): JsonResponse
    {
        $todo = $repository->find($id);

        if (!$todo) {
            return new JsonResponse(['error' => 'Todo not found'], 404);
        }

        $this->em->remove($todo);
        $this->em->flush();

        return new JsonResponse(null, 204);
    }
}
