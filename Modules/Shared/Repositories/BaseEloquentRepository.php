<?php

namespace Modules\Shared\Repositories;

use Illuminate\Database\Eloquent\Model;
use Modules\Shared\Interfaces\RepositoryInterface;

abstract class BaseEloquentRepository implements RepositoryInterface
{
    public function __construct(protected readonly Model $model)
    {
    }

    public function find(int|string $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }
}
