<?php

namespace App\Domain\TimeTracking\Exceptions;

use App\Domain\Shared\ValueObjects\ID;
use Exception;

class NoActiveClockEntry extends Exception
{
    public function __construct(?ID $projectId = null)
    {
        parent::__construct('No active clock entry found for the user.');
        if ($projectId) {
            $this->message .= " for project ID: {$projectId}";
        }
    }

    public static function forProject(ID $projectId): self
    {
        return new self($projectId);
    }
}
