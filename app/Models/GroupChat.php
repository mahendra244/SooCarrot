<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'group_id',
        'message'
    ];

    public function userData()
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }
}
