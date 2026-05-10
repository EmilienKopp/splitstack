<?php

namespace App\Domain\TimeTracking\Exceptions;

use App\Domain\Shared\ValueObjects\ID;
use Exception;

class AlreadyClockedInException extends Exception
{
    public function __construct(?ID $projectId = null)
    {
        parent::__construct('Already clocked in');
        if ($projectId) {
            $this->message .= " for project ID: {$projectId}";
        }
    }

    public static function forProject(ID $projectId): self
    {
        return new self($projectId);
    }
}
