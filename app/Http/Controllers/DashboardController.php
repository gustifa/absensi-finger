<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Artisan; // Tambahkan ini di bagian atas file

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $hariIni = now()->toDateString();

        $absensiHariIni = Attendance::with('member')
                            ->where('tanggal', $hariIni)
                            ->orderBy('jam_masuk', 'desc')
                            ->get();

        if ($user->role === 'piket') {
            return view('dashboard.piket', compact('absensiHariIni'));
        }
        elseif (in_array($user->role, ['wakil', 'kepsek'])) {
            $totalHadir = $absensiHariIni->where('status', 'Hadir')->count();
            $totalTerlambat = $absensiHariIni->where('status', 'Terlambat')->count();
            return view('dashboard.pimpinan', compact('absensiHariIni', 'totalHadir', 'totalTerlambat'));
        }

        // Default view untuk guru
        return view('dashboard.guru', compact('absensiHariIni'));
    }

    public function tarikManual()
    {
        try {
            // Memanggil command penarikan data secara manual dari sistem
            Artisan::call('absen:tarik', ['ip' => '192.168.1.201']); // Sesuaikan IP jika perlu

            return redirect()->back()->with('success', 'Sinkronisasi data dari mesin X100-C berhasil dilakukan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menarik data: Pastikan mesin menyala dan terhubung ke jaringan.');
        }
    }
}
