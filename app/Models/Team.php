<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    //
    protected $fillable = [
        'name',
        'profile_pic',
        'team_leader_id'
    ];

    public function scopeVisibleTo($query, $user)
    {
        if ($user->role === 'admin') {
            return $query;
        } elseif ($user->role === 'team_leader') {
            return $query->where('team_leader_id', $user->id);
        } else {
            return $query->whereHas('members', fn($q) => $q->where('id', $user->id));
        }
    }


    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'team_leader_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
