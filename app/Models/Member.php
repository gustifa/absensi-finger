<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    // Tambahkan baris ini untuk mengizinkan Laravel menyimpan data ke kolom-kolom ini
    protected $fillable = [
        'nama',
        'nomor_induk',
        'kategori',
        'departemen',
        'fingerprint_id',
    ];
}
