<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeVote extends Model
{
    use HasFactory;
    protected $table = 'employee_votes';
    protected $fillable = [
        'fullName',
        'mobile',
        'address',
        'card_number',
        'unit_office',
        'base',
        'is_election',
        'note',
        'datetime',
        'user_id'
    ];

    protected $casts = [
        'is_election' => 'boolean',
        'datetime' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function base()
    {
        return $this->belongsTo(base::class, 'base_id', 'id');
    }
}
