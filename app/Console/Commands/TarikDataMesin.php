<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\Attendance;
use App\Models\Member;
use Carbon\Carbon;
class TarikDataMesin extends Command
{
<<<<<<< HEAD
   // Nama perintah yang dipanggil oleh tombol atau terminal
    protected $signature = 'absen:tarik {ip=192.168.1.10}';

    // Deskripsi perintah
    protected $description = 'Menarik log absensi dari mesin fingerprint X100-C';

=======
    /**
     * Execute the console command.
     */
    protected $signature = 'absen:tarik {ip=192.168.1.10}';
    protected $description = 'Tarik data masuk & pulang dari mesin fingerprint';
>>>>>>> 3e77bc9c080b9e85a980f588c885a7a11c516be5
    public function handle()
    {
        $ip = $this->argument('ip');
        $this->info("Mencoba terhubung ke mesin IP: $ip...");

        try {
            $zk = new ZKTeco($ip);
            
            if ($zk->connect()) {
                $this->info("Terhubung! Mengunduh log absensi...");
                $logs = $zk->getAttendance(); 
                $jumlahBaru = 0;

                foreach ($logs as $log) {
                    // Format waktu dari mesin: YYYY-MM-DD HH:MM:SS
                    $waktu = Carbon::parse($log['timestamp']);
                    $tanggal = $waktu->toDateString();
                    $jam = $waktu->toTimeString();

                    // Cari guru/siswa berdasarkan ID Sidik Jari di mesin
                    $member = Member::where('fingerprint_id', $log['id'])->first();

                    if ($member) {
                        // Cek apakah hari ini siswa tersebut sudah absen masuk
                        $absen = Attendance::firstOrNew([
                            'member_id' => $member->id,
                            'tanggal'   => $tanggal,
                        ]);

                        if (!$absen->exists) {
                            // Jika belum ada data sama sekali hari ini -> Jadikan Jam Masuk
                            $absen->jam_masuk = $jam;
                            $absen->status = 'Hadir';
                            $absen->save();
                            $jumlahBaru++;
                        } else {
                            // Jika sudah absen masuk, dan dia scan lagi di jam yang lebih siang/sore -> Jadikan Jam Pulang
                            if ($absen->jam_masuk && $absen->jam_masuk < $jam) {
                                $absen->jam_pulang = $jam;
                                $absen->save();
                            }
                        }
                    }
                }

                $zk->disconnect();
                $this->info("Selesai! $jumlahBaru data log absensi berhasil diproses.");
                return Command::SUCCESS;
                
            } else {
                $this->error("Gagal terhubung ke mesin. Pastikan kabel LAN tersambung.");
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("Terjadi error sistem: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
