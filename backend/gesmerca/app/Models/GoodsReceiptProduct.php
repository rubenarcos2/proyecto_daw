<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //'id',
        'quantity',
        'price',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //Relationships Many to Many
    public function suppliers(){
        return $this->belongsToMany(Supplier::class, 'goods_receipt_product', 'idsupplier', 'idproduct')->withPivot('quantity', 'price')->withTimestamps();
    }

    //Relationships Many to Many
    public function products(){
        return $this->belongsToMany(Product::class, 'goods_receipt_product', 'idsupplier', 'idproduct')->withPivot('quantity', 'price')->withTimestamps();
    }
}
