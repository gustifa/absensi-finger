<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Input Absensi (Database & Mesin)</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('absensi.simpan') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Pilih Siswa/Guru</label>
                        <select name="member_id" class="w-full rounded-md border-gray-300">
                            @foreach($siswa as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }} (ID: {{ $s->fingerprint_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full rounded-md border-gray-300">
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium">Jam Masuk</label>
                            <input type="time" name="jam_masuk" class="w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Jam Pulang</label>
                            <input type="time" name="jam_pulang" class="w-full rounded-md border-gray-300">
                        </div>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium">Status</label>
                        <select name="status" class="w-full rounded-md border-gray-300">
                            <option value="Hadir">Hadir</option>
                            <option value="Izin">Izin</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700">Simpan Kehadiran</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>