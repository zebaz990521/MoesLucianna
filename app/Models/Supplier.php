<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    //

    use HasFactory;
    protected $fillable = [
        "rut",
        "name",
        "email",
        "phone",
        "address",
        "photo",
        "status"
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
