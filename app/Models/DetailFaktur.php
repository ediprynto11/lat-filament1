<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailFaktur extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'detail';

    public function barang() {
        return $this->belongsTo(Barang::class);
    }

    public function faktur(){
        return $this->belongsTo(Faktur::class, 'id');
    }
}
