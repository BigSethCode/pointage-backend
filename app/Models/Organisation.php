<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    protected $fillable = ['nom', 'slug'];

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }
}
