<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function warehouseKeepers()
    {
        return $this->hasMany(Keeper::class);
    }

    public function drugs()
    {
        return $this->belongsToMany(Drug::class,'warehouse_drug')->withPivot('amount');
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }
}
