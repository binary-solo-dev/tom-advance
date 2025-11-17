<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Model\Task;
use App\Domain\Repository\TaskRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class TaskRepository implements TaskRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    public function save(Task $task): void
    {
        $this->entityManager->persist($task);
        $this->entityManager->flush();
    }

    public function findById(string $id): ?Task
    {
        return $this->entityManager->find(Task::class, $id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Task::class)->findAll();
    }

    public function findByTitle(string $title): ?Task
    {
        return $this->entityManager->getRepository(Task::class)->findOneBy(['title' => $title]);
    }

    public function delete(Task $task): void
    {
        $this->entityManager->remove($task);
        $this->entityManager->flush();
    }

    public function existsByTitle(string $title): bool
    {
        return $this->entityManager->getRepository(Task::class)->count(['title' => $title]) > 0;
    }
}