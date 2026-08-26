<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collaborateur extends Model
{
    protected $fillable = ['organisation_id', 'nom', 'email', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function pointages(): HasMany
    {
        return $this->hasMany(Pointage::class);
    }
}
