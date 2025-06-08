<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faktur extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded  = [];

    protected $table = 'faktur';

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function detail(){
        return $this->hasMany(DetailFaktur::class, 'faktur_id');
    }
}
