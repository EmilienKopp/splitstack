<?php

namespace App\Models;

use App\Attributes\ExportRelationship;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class Report extends Model
{
    use UsesTenantConnection;

    protected $guarded = ['id'];

    #[ExportRelationship(User::class)]
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
