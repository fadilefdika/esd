<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Vendor extends Authenticatable
{
    use Notifiable;

    protected $table = 'vendors';

    protected $fillable = [
        'fullname',
        'username',
        'password_hash',
    ];

    protected $hidden = [
        'password_hash',
    ];

    // Override accessor for password to use our custom hash column
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}
