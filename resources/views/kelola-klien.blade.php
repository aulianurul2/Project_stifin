<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Klien - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex" x-data="{ openEdit: false, currKlien: {} }">

    <div class="w-64 bg-slate-900 min-h-screen text-white flex-shrink-0">
        <div class="p-6 text-xl font-bold border-b border-slate-800 text-center">STIFIn Admin</div>
        <nav class="mt-4">
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>
            <a href="{{ route('kelola-klien') }}" class="flex items-center py-3 px-6 bg-blue-600 text-white">
                <i class="fas fa-users mr-3"></i> Kelola Klien
            </a>
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-edit mr-3"></i> Pendaftaran Tes
            </a>

            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-calendar-alt mr-3"></i> Jadwal Tes
            </a>

            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-file-medical mr-3"></i> Hasil Tes
            </a>
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-chart-bar mr-3"></i> Laporan
            </a>

               <div class="border-t border-slate-800 mt-4 pt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left py-3 px-6 text-red-400 hover:bg-red-900/20 transition">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </button>
                </form>
            </nav>
    </div>

    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm p-4 flex justify-between items-center">
            <h2 class="font-semibold text-gray-700">Kelola Klien</h2>
            @if(session('success'))
                <span class="text-green-500 text-sm font-bold">{{ session('success') }}</span>
            @endif
        </header>

        <main class="p-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto border">
                        <thead class="bg-gray-50 text-center">
                            <tr>
                                <th class="px-4 py-2 border">Nama Klien</th>
                                <th class="px-4 py-2 border">Alamat</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($klien as $k)
                            <tr class="text-center">
                                <td class="px-4 py-2 border">{{ $k->nama }}</td>
                                <td class="px-4 py-2 border">{{ $k->alamat ?? '-' }}</td>
                                <td class="px-4 py-2 border">
                                    <span class="px-2 py-1 rounded text-xs {{ $k->status_jadwal ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ $k->status_jadwal ?? 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 border flex justify-center space-x-2">
                                    <button 
                                        @click="currKlien = {id: '{{ $k->id_klien }}', nama: '{{ $k->nama }}', alamat: '{{ $k->alamat }}'}; openEdit = true"
                                        class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('klien.destroy', $k->id_klien) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus klien ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <div x-show="openEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div class="bg-white p-6 rounded-lg w-96 shadow-xl" @click.away="openEdit = false">
            <h3 class="text-lg font-bold mb-4">Edit Data Klien</h3>
            <form :action="'{{ url('kelola-klien') }}/' + currKlien.id" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="nama" x-model="currKlien.nama" class="w-full border rounded px-3 py-2 mt-1 focus:ring-blue-500 border-gray-300">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea name="alamat" x-model="currKlien.alamat" class="w-full border rounded px-3 py-2 mt-1 focus:ring-blue-500 border-gray-300"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="openEdit = false" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <style> [x-cloak] { display: none !important; } </style>
</body>
</html>