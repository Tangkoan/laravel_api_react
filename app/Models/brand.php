<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;

class brand extends Model
{
    //
    protected $guarded = []; 


    public function product(){
        return $this->hasMany(Product::class);
    }
}
