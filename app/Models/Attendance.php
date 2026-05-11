<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // Tambahkan baris ini
    protected $fillable = [
        'member_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status',
    ];

    // Jika Bapak memiliki relasi ke Member, biarkan saja kode relasinya di bawah ini
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
