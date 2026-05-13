<?php

namespace Modules\Shared\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function find(int|string $id): ?Model;
}
