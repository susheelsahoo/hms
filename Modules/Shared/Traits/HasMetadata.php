<?php

namespace Modules\Shared\Traits;

trait HasMetadata
{
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'metadata' => 'array',
        ]);
    }
}
