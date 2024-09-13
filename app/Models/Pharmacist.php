<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacist extends Authenticatable
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone_number',
        'password',
        'api_token',
        'pharmacy_id',
    ];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
