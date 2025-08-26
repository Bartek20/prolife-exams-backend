<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slide extends Model
{
    public function presentation(): BelongsTo {
        return $this->belongsTo(Presentation::class, 'presentation_uuid', 'uuid');
    }
}
