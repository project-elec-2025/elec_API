<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class base extends Model
{
    //
    protected $table = 'bases';
    protected $fillable = ['base_name', 'circle_id'];

    public function employeeVotes(): HasMany
    {
        return $this->hasMany(employeeVote::class, 'base_id');
    }

    public function cirlce()
    {
        return $this->hasOne(circle::class,  'id', 'circle_id');
    }
}
