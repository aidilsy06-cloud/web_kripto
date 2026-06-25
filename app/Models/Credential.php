<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $fillable = [
        'user_id',
        'platform_name',
        'platform_url',
        'username',
        'password_encrypted',
        'password_iv',
        'notes_encrypted',
        'notes_iv',
        'strength',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
