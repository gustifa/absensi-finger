<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrasi User ke Database & Mesin Fingerprint') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Peringatan!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('user.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Lengkap (Maks 24 Huruf)</label>
                                <input type="text" name="nama" required maxlength="24" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">NISN / NIP</label>
                                <input type="text" name="nomor_induk" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select name="kategori" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="siswa">Siswa</option>
                                    <option value="guru">Guru</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jurusan / Departemen</label>
                                <input type="text" name="departemen" required placeholder="Contoh: Teknik Ketenagalistrikan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">ID Fingerprint (Angka Unik untuk Mesin)</label>
                                <p class="text-xs text-gray-500 mb-1">Angka ini yang akan digunakan siswa saat registrasi sidik jari di mesin. Contoh: 101, 102, dst.</p>
                                <input type="number" name="fingerprint_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('fingerprint_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                                Simpan & Kirim ke Mesin
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>