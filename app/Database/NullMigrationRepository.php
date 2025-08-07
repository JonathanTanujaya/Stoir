<?php

namespace App\Database;

use Illuminate\Database\Migrations\MigrationRepositoryInterface;

class NullMigrationRepository implements MigrationRepositoryInterface
{
    public function repositoryExists(): bool
    {
        return true; // Always return true to avoid migration checks
    }

    public function createRepository(): void
    {
        // Do nothing
    }

    public function getRan(): array
    {
        return []; // Return empty array - no migrations to check
    }

    public function getMigrations(int $steps): array
    {
        return [];
    }

    public function getLast(): array
    {
        return [];
    }

    public function getMigrationsByBatch(array $batches): array
    {
        return [];
    }

    public function log(string $file, int $batch): void
    {
        // Do nothing - don't log migrations
    }

    public function delete(object $migration): void
    {
        // Do nothing
    }

    public function getNextBatchNumber(): int
    {
        return 1;
    }
}
