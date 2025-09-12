<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class base extends Model
{
    //
    protected $table = 'bases';
    protected $fillable = ['base_name', 'circle_id'];

    public function employeeVotesd(): HasMany
    {
        return $this->hasMany(employeeVote::class, 'base_id');
    }
    public function cirlce()
    {
        return $this->hasMany(circle::class, 'id', 'circle_id');
    }
    // In your Base model (app/Models/Base.php)
    // public function circles()
    // {
    //     return $this->belongsTo(Circle::class,  'circle_id', 'id');
    // }

    public function circles(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function employeeVotes(): HasMany
    {
        return $this->hasMany(EmployeeVote::class);
    }
}
