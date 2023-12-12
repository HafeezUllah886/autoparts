<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class purchase extends Model
{
    use HasFactory;
    protected $fillable = (
        [
            'vendor',
            'walking',
            'paidFrom',
            'isPaid',
            'date',
            'desc',
            'amount',
            'ref'
        ]
    );

    public function vendor_account(){
        return $this->belongsTo(account::class, 'vendor', 'id');
    }

    public function account(){
        return $this->belongsTo(account::class, 'paidFrom', 'id');
    }

    public function details(){
        return $this->hasMany(purchase_details::class,'bill_id');
    }
}
