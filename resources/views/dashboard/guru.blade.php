<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Pantauan Real-time Piket - SMK N 1 Bukittinggi') }}
            </h2>

            <div class="flex items-center space-x-2"> <span class="mr-2 text-sm text-gray-500">Auto-refresh tiap 1 menit</span>

                <form action="{{ route('koneksi.cek') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-gray-700 border border-transparent rounded-md shadow-sm hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cek Koneksi
                    </button>
                </form>

                <form action="{{ route('tarik.user') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" onclick="return confirm('Tarik data profil user/siswa dari mesin?')" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        Sinkron User
                    </button>
                </form>

                <form action="{{ route('tarik.manual') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" onclick="return confirm('Mulai tarik data absen hari ini dari mesin?')" class="inline-flex items-center px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Tarik Absen
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
