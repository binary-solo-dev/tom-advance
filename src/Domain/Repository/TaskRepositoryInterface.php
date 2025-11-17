<?php
declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Model\Task;

interface TaskRepositoryInterface
{
    public function save(Task $task): void;
    public function findById(string $id): ?Task;
    public function findAll(): array;
    public function findByTitle(string $title): ?Task;
    public function delete(Task $task): void;
    public function existsByTitle(string $title): bool;
}