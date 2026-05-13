<?php

namespace App\Support\Context;

use Modules\User\Models\User;

final class TenantContext
{
    public function __construct(
        private ?int $organizationId = null,
        private ?User $user = null,
    ) {
    }

    public function setOrganizationId(?int $organizationId): void
    {
        $this->organizationId = $organizationId;
    }

    public function organizationId(): ?int
    {
        return $this->organizationId;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function user(): ?User
    {
        return $this->user;
    }
}
