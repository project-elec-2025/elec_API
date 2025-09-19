<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Person extends Model
{
    use HasFactory;
    protected $table = "people";
    protected $fillable = [
        'employee_id',
        'name',
        'number_family',
        'relation',
        'type_election',
        'note',
        'user_id'
    ];

    /**
     * Get the employee that owns the person.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(EmployeeVote::class);
    }

    /**
     * Get the user that owns the person.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to filter by type_election.
     */
    public function scopeByTypeElection($query, $type)
    {
        return $query->where('type_election', $type);
    }

    /**
     * Scope a query to filter by relation.
     */
    public function scopeByRelation($query, $relation)
    {
        return $query->where('relation', $relation);
    }

    /**
     * Scope a query to filter by employee.
     */
    public function scopeByEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
