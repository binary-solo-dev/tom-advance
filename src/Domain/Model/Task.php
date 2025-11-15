<?php
declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\StatusException;
use App\Domain\ValueObject\TaskStatus;
use Ramsey\Uuid\Uuid;

final class Task
{
    private string $id;
    private string $title;
    private ?string $description;
    private TaskStatus $status;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    public function __construct(string $title, ?string $description = null)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->title = $title;
        $this->description = $description;
        $this->status = TaskStatus::TODO;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function changeStatus(TaskStatus $newStatus): void
    {
        if ($this->status->isDone()) {
            throw StatusException::becauseTaskAlreadyHasTheStatusDone();
        }

        if ($newStatus->isDone() && !$this->status->isInProgress()) {
            throw StatusException::becauseTaskCannotFinishWhenNotInProgress();
        }

        $this->status = $newStatus;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }
}