<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        "nit",
        "name",
        "email",
        "phone",
        "address",
        "photo",
        "status"
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
