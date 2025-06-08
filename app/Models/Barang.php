<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    
    protected $table = 'barangs';
    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'stok',
        'harga',
    ];

    public function detail() {
        return $this->hasMany(DetailFaktur::class);
    }

}
