<?php

namespace App\Domain\Organization\Entities;

use App\Domain\Shared\BaseEntity;

/**
 * Parent entity — load via TeamRepository.
 * Owns TeamMemberEntity and TeamInvitationEntity.
 * Invariant: a user can only appear once per team.
 */
class TeamEntity extends BaseEntity
{
    /** @var TeamMemberEntity[] */
    private array $members = [];

    /** @var TeamInvitationEntity[] */
    private array $invitations = [];

    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly bool $is_personal,
    ) {}

    public function addMember(TeamMemberEntity $member): void
    {
        foreach ($this->members as $existing) {
            if ($existing->user_id === $member->user_id) {
                throw new \DomainException("User {$member->user_id} is already a member of this team.");
            }
        }

        $this->members[] = $member;
    }

    public function addInvitation(TeamInvitationEntity $invitation): void
    {
        $this->invitations[] = $invitation;
    }

    /** @return TeamMemberEntity[] */
    public function members(): array
    {
        return $this->members;
    }

    /** @return TeamInvitationEntity[] */
    public function invitations(): array
    {
        return $this->invitations;
    }
}
