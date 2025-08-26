<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Presentation extends Model
{
    use HasUuids;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    public function slides(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(Slide::class, 'presentation_uuid', 'uuid')
            ->orderBy('index');
    }
}
