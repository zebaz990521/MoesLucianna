<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    //

    protected $fillable = [
        "customer_id",
        "user_id",
        "document_type_id",
        "total_price",
        "sale_datetime",
        "status",
        "payment_method",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
