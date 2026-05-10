<?php

namespace App\Domain\TimeTracking\Exceptions;

use Exception;

class NoActiveClockEntry extends Exception
{
    public function __construct(int|string|null $projectId = null)
    {
        parent::__construct('No active clock entry found for the user');
        if ($projectId) {
            $this->message .= " for project ID: {$projectId}";
        }
    }

    public static function forProject(int|string $projectId): self
    {
        return new self($projectId);
    }
}
