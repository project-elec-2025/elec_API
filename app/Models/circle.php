<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class circle extends Model
{
    //
    protected $table = 'circles';
    protected $fillable = ['circle_name'];

    public function bases(): HasMany
    {
        return $this->hasMany(Base::class);
    }
}
