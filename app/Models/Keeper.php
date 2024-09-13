<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keeper extends Authenticatable
{
    use HasFactory;
    protected $table = 'keepers';
    protected $fillable = [
        'name',
        'phone_number',
        'password',
        'api_token',
        'warehouse_id',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
