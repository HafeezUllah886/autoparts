<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sale_draft extends Model
{
    use HasFactory;
    protected $fillable = (
        [
            'product_id',
            'price',
            'qty',
        ]
    );

    public function product(){
        return $this->belongsTo(products::class, 'product_id', 'id');
    }
}