<?php
declare(strict_types=1);

namespace App\Application\Query;

final readonly class GetTaskQuery
{
    public function __construct(
        public readonly string $id
    ) {
    }
}