<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    use HasFactory;

    protected $guarded=[];
    protected $fillable = [
        'scintific_name',
        'commercial_name',
        'category',
        'company',
        'amount',
        'expiration_date',
        'price',
    ];

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class,'warehouse_drug')->withPivot('amount');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
