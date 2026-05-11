<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\Attendance;
use App\Models\Member;
use Carbon\Carbon;

#[Signature('app:tarik-data-mesin')]
#[Description('Command description')]
class TarikDataMesin extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 'absen:tarik {ip=192.168.1.201}';
    protected $description = 'Tarik data masuk & pulang dari mesin fingerprint';
    public function handle()
    {
        $ip = $this->argument('ip');
            $zk = new ZKTeco($ip);

            if ($zk->connect()) {
                $logs = $zk->getAttendance();

                foreach ($logs as $log) {
                    $member = Member::where('fingerprint_id', $log['id'])->first();

                    if ($member) {
                        $waktuScan = Carbon::parse($log['timestamp']);
                        $tanggal = $waktuScan->toDateString();
                        $jam = $waktuScan->toTimeString();

                        $absen = Attendance::firstOrCreate(
                            ['member_id' => $member->id, 'tanggal' => $tanggal]
                        );

                        // Pagi (sebelum jam 12) = Masuk | Sore (setelah jam 12) = Pulang
                        if ($jam < '12:00:00') {
                            if (is_null($absen->jam_masuk)) {
                                $absen->jam_masuk = $jam;
                                $absen->status = ($jam > '07:15:00') ? 'Terlambat' : 'Hadir';
                            }
                        } else {
                            // Terus perbarui jam pulang dengan scan terakhir
                            $absen->jam_pulang = $jam;
                        }
                        $absen->save();
                    }
                }
                $zk->disconnect();
                $this->info("Sinkronisasi berhasil.");
            } else {
                $this->error("Gagal terhubung ke IP {$ip}.");
            }
    }
}
