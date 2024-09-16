<?php

namespace App\Models;

use App\Traits\QueryScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WishList extends Model
{
    use HasFactory,QueryScopes,SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_variant_id'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function productVariant(){
        return $this->belongsTo(ProductVariant::class);
    }
}
