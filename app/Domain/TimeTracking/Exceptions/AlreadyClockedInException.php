<?php

namespace App\Domain\TimeTracking\Exceptions;

use Exception;

class AlreadyClockedInException extends Exception
{
    public function __construct(int|string|null $projectId = null)
    {
        parent::__construct('Already clocked in');
        if ($projectId) {
            $this->message .= " for project ID: {$projectId}";
        }
    }

    public static function forProject(int|string $projectId): self
    {
        return new self($projectId);
    }
}
