<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pointage extends Model
{
    protected $fillable = ['collaborateur_id', 'date', 'contenu_fait', 'blocage'];

    public function collaborateur(): BelongsTo
    {
        return $this->belongsTo(Collaborateur::class);
    }
}
