<x-app-layout>
    <x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Pantauan Real-time Piket - SMK N 1 Bukittinggi') }}
        </h2>

        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">Auto-refresh tiap 1 menit</span>

            <form action="{{ route('tarik.manual') }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Mulai tarik data manual dari mesin?')" class="px-4 py-2 font-bold text-white bg-blue-600 rounded shadow hover:bg-blue-700">
                    Tarik Data Sekarang
                </button>
            </form>
        </div>
    </div>
    </x-slot>

    <meta http-equiv="refresh" content="60">

    <div class="py-12">
        <div class="mx-auto mb-4 max-w-7xl sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="relative px-4 py-3 text-green-700 bg-green-100 border border-green-400 rounded" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="relative px-4 py-3 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                    <strong class="font-bold">Peringatan!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
        </div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto text-gray-900">

                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase border-b bg-blue-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Nama</th>
                                <th scope="col" class="px-6 py-3">Kategori</th>
                                <th scope="col" class="px-6 py-3">Kelas/Unit</th>
                                <th scope="col" class="px-6 py-3">Jam Masuk</th>
                                <th scope="col" class="px-6 py-3">Jam Pulang</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($absensiHariIni as $absen)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $absen->member->nama }}
                                    </td>
                                    <td class="px-6 py-4 capitalize">
                                        {{ $absen->member->kategori }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $absen->member->departemen }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-green-600">
                                        {{ $absen->jam_masuk ?? '--:--' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-blue-600">
                                        {{ $absen->jam_pulang ?? '--:--' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($absen->status == 'Terlambat')
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-400">Terlambat</span>
                                        @else
                                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-400">Hadir</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada data absensi hari ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
