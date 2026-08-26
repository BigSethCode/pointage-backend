<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Organisation extends Model
{
    protected $fillable = ['nom', 'slug'];

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    public function collaborateurs(): HasMany
    {
        return $this->hasMany(Collaborateur::class);
    }

    public function pointages(): HasManyThrough
    {
        return $this->hasManyThrough(Pointage::class, Collaborateur::class);
    }
}
