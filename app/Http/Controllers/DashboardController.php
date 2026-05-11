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
        //dd($user);

        $absensiHariIni = Attendance::with('member')
                            ->where('date', $hariIni)
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
            Artisan::call('absen:tarik', ['ip' => '192.168.1.10']); // Sesuaikan IP jika perlu

            return redirect()->back()->with('success', 'Sinkronisasi data dari mesin X100-C berhasil dilakukan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menarik data: Pastikan mesin menyala dan terhubung ke jaringan.');
        }
    }

    // Menampilkan halaman form input manual
    public function formKehadiranManual()
    {
        // Hanya mengambil data siswa untuk diinputkan oleh guru
        $siswa = \App\Models\Member::where('kategori', 'siswa')
                                   ->orderBy('departemen')
                                   ->orderBy('nama')
                                   ->get();

        return view('dashboard.input-manual', compact('siswa'));
    }

    // Memproses data yang dikirim dari form
    public function simpanKehadiranManual(Request $request)
    {
        // Validasi data yang dikirim
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'tanggal'   => 'required|date',
            'status'    => 'required|in:Hadir,Sakit,Izin,Alpa,Terlambat',
            'jam_masuk' => 'nullable|date_format:H:i',
        ]);

        // Gunakan updateOrCreate agar jika data hari itu sudah ada, sistem akan menimpanya
        // (mencegah error duplikasi data)
        \App\Models\Attendance::updateOrCreate(
            [
                'member_id' => $request->member_id,
                'tanggal'   => $request->tanggal,
            ],
            [
                'jam_masuk' => $request->jam_masuk,
                'status'    => $request->status,
            ]
        );

        return redirect()->back()->with('success', 'Data kehadiran siswa berhasil disimpan!');
    }

    public function tarikUserMesin()
    {
        try {
            // Sesuaikan IP dengan IP mesin X100-C Bapak
            $zk = new \Rats\Zkteco\Lib\ZKTeco('192.168.1.10');

            if ($zk->connect()) {
                // Menarik semua data user dari memori mesin
                $users = $zk->getUser();

                // TAMBAHKAN BARIS INI UNTUK MENGINTIP DATA MENTAH
                //dd($users);
                
                $jumlahBaru = 0;

                foreach ($users as $u) {
                    // $u['userid'] adalah ID/PIN yang diketik di mesin
                    // $u['name'] adalah Nama di mesin (kadang kosong jika hanya daftar jari)

                    $namaUser = !empty($u['name']) ? $u['name'] : 'User Mesin ' . $u['userid'];

                    // Cek apakah user dengan fingerprint_id ini sudah ada di database Laravel
                    $member = \App\Models\Member::where('fingerprint_id', $u['userid'])->first();

                    if (!$member) {
                        // Jika belum ada, masukkan sebagai data baru
                        \App\Models\Member::create([
                            'nama' => $namaUser,
                            'nomor_induk' => 'UID-' . $u['userid'], // Nomor induk sementara
                            'kategori' => 'siswa', // Default kita anggap siswa dahulu
                            'departemen' => 'Belum Diatur', // Harus di-update manual nanti
                            'fingerprint_id' => $u['userid'],
                        ]);
                        $jumlahBaru++;
                    }
                }

                $zk->disconnect();
                return redirect()->back()->with('success', "Sinkronisasi berhasil! Ada $jumlahBaru user baru yang ditarik dari mesin.");
            } else {
                return redirect()->back()->with('error', 'Gagal terhubung ke mesin X100-C. Cek kabel LAN dan IP Address.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cekKoneksiMesin()
    {
        try {
            $ip = '192.168.1.10'; // Sesuaikan IP mesin di bengkel
            $zk = new \Rats\Zkteco\Lib\ZKTeco($ip);

            if ($zk->connect()) {
                // Tarik sedikit informasi mesin untuk membuktikan koneksi stabil
                $versiMesin = $zk->version();
                $zk->disconnect();

                return redirect()->back()->with('success', "Koneksi ke mesin X100-C ($ip) BERHASIL! (Firmware: $versiMesin)");
            } else {
                return redirect()->back()->with('error', "GAGAL terhubung ke mesin X100-C ($ip). Periksa sambungan kabel LAN dan pastikan mesin menyala.");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Terjadi kesalahan jaringan: " . $e->getMessage());
        }
    }

    // Menampilkan Form Tambah User
    public function formTambahUser()
    {
        return view('dashboard.tambah-user');
    }

    // Memproses Data ke Database dan ke Mesin Fingerprint
    public function simpanUser(Request $request)
    {
        // 1. Validasi inputan form
        $request->validate([
            'nama'           => 'required|string|max:24', // Mesin max 24 karakter
            'nomor_induk'    => 'required|string',
            'kategori'       => 'required|in:siswa,guru',
            'departemen'     => 'required|string',
            'fingerprint_id' => 'required|integer|unique:members,fingerprint_id',
        ]);

        // 2. Simpan data ke Database Laravel
        $member = \App\Models\Member::create([
            'nama'           => $request->nama,
            'nomor_induk'    => $request->nomor_induk,
            'kategori'       => $request->kategori,
            'departemen'     => $request->departemen,
            'fingerprint_id' => $request->fingerprint_id,
        ]);

        // 3. Kirim (Push) data nama dan ID ke Mesin X100-C
        try {
            // Gunakan IP mesin yang sudah kita pastikan benar sebelumnya
            $zk = new \Rats\Zkteco\Lib\ZKTeco('192.168.1.10'); 
            
            if ($zk->connect()) {
                // Format library ZKTeco: setUser(UID, UserID, Nama, Password, Role)
                // UID (Internal Mesin) kita samakan dengan ID Database agar sinkron
                // Role 0 = User Biasa (Siswa/Guru), Role 14 = Admin Mesin
                
                $zk->setUser(
                    $member->id,                 // UID Internal
                    $request->fingerprint_id,    // ID PIN di layar mesin
                    $request->nama,              // Nama yang tampil di mesin
                    '',                          // Password (dikosongkan)
                    0                            // Role (0 = Normal User)
                ); 
                
                $zk->disconnect();
                
                return redirect()->back()->with('success', 'Luar Biasa! Data berhasil disimpan di Database DAN nama siswa sudah terkirim ke mesin Fingerprint.');
            } else {
                return redirect()->back()->with('error', 'Data tersimpan di Database, TAPI mesin tidak merespons. Pastikan mesin menyala.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data tersimpan di Database, tapi error ke mesin: ' . $e->getMessage());
        }
    }

    public function formAbsensiManual()
    {
        $siswa = \App\Models\Member::all();
        return view('dashboard.input-absensi', compact('siswa'));
    }

    public function simpanAbsensiManual(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'tanggal'   => 'required|date',
            'jam_masuk' => 'required',
            'jam_pulang'=> 'nullable',
            'status'    => 'required'
        ]);

        // 1. Ambil data member untuk mendapatkan fingerprint_id
        $member = \App\Models\Member::find($request->member_id);

        // 2. Simpan/Update ke Database Laravel
        $absen = \App\Models\Attendance::updateOrCreate(
            ['member_id' => $request->member_id, 'tanggal' => $request->tanggal],
            ['jam_masuk' => $request->jam_masuk, 'jam_pulang' => $request->jam_pulang, 'status' => $request->status]
        );

        // 3. Kirim (Push) ke Mesin Fingerprint
        try {
            $zk = new \Rats\Zkteco\Lib\ZKTeco('192.168.1.10');
            if ($zk->connect()) {
                // Format: setAttendance(user_id, state, timestamp)
                // State 0 biasanya untuk Masuk, State 1 untuk Pulang
                
                // Kirim data jam masuk ke mesin
                $zk->setAttendance($member->fingerprint_id, 0, $request->tanggal . ' ' . $request->jam_masuk . ':00');
                
                // Jika jam pulang diisi, kirim juga ke mesin
                if ($request->jam_pulang) {
                    $zk->setAttendance($member->fingerprint_id, 1, $request->tanggal . ' ' . $request->jam_pulang . ':00');
                }

                $zk->disconnect();
                return redirect()->back()->with('success', 'Data berhasil disimpan di Database dan disinkronkan ke Mesin.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('success', 'Data tersimpan di Database, namun gagal mengirim ke mesin (Mesin Offline).');
        }
    }
}
