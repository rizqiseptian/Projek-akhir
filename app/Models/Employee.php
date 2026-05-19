<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $fillable = ['name', 'rfid_uid', 'face_descriptor', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
