<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $fillable = ['name', 'rfid_uid', 'face_descriptor', 'whatsapp_number', 'is_active', 'is_admin'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_admin' => 'boolean',
    ];
}
