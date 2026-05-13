<?php

namespace Modules\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Shared\Traits\BelongsToOrganization;
use Modules\Shared\Traits\HasMetadata;

abstract class TenantModel extends Model
{
    use BelongsToOrganization;
    use HasMetadata;
    use SoftDeletes;
}
