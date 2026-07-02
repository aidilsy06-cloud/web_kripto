<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $fillable = [
        'user_id',
        'platform_name_encrypted',
        'platform_name_iv',
        'platform_url_encrypted',
        'platform_url_iv',
        'username_encrypted',
        'username_iv',
        'password_encrypted',
        'password_iv',
        'notes_encrypted',
        'notes_iv',
        'strength',
    ];

    // Temporary decrypted properties for easy read access in templates/JS
    public ?string $platform_name = null;
    public ?string $platform_url = null;
    public ?string $username = null;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Merge temporary decrypted values into array representation
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['platform_name'] = $this->platform_name;
        $array['platform_url'] = $this->platform_url;
        $array['username'] = $this->username;
        return $array;
    }
}
