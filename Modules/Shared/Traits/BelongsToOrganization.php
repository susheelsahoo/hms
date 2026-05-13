<?php

namespace Modules\Shared\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Organization\Models\Organization;

trait BelongsToOrganization
{
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
