<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class Profile extends Model
{
    //
    protected $guarded = []; 

    // ភ្ជាប់ទំនាក់ទំនង Table បែរ Eloquent ORM ប៉ុន្ដែ User Models ក៏ត្រូវភ្ជាប់ដែរ
    public function user(){
        return $this->belongsTO(User::class);
    }
}
