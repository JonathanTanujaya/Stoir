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

   public function getMigrations($steps)
{
    return [];
}


    public function getLast(): array
    {
        return [];
    }

    public function getMigrationsByBatch($batch): array
    {
        return [];
    }

    public function log($file, $batch): void
    {
        // Do nothing - don't log migrations
    }

    public function delete($migration): void
    {
        // Do nothing
    }

    public function getNextBatchNumber(): int
    {
        return 1;
    }

    public function getMigrationBatches(): array
    {
        return [];
    }

    public function deleteRepository(): void
    {
        // Do nothing
    }

    public function setSource($name): void
    {
        // Do nothing
    }
}
