<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Klien - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 flex" x-data="{ openModal: false, openView: false, selected: {}, selectedView: {} }">

    <div class="w-64 bg-slate-900 min-h-screen text-white flex-shrink-0">
        <div class="p-6 text-xl font-bold border-b border-slate-800 text-center">STIFIn Admin</div>
        <nav class="mt-4">
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>
            <a href="{{ route('kelola-klien') }}" class="flex items-center py-3 px-6 bg-blue-600 text-white">
                <i class="fas fa-users mr-3"></i> Kelola Klien
            </a>
            <a href="{{ route('pendaftaran-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                <i class="fas fa-edit mr-3"></i> Pendaftaran Tes
            </a>
            <a href="{{ route('jadwal-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-calendar-alt mr-3"></i> Jadwal Tes
            </a>
            <a href="{{ route('hasil-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-file-medical mr-3"></i> Hasil Tes
            </a>
            <a href="{{ route('laporan.index') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                <i class="fas fa-chart-bar mr-3"></i> Laporan
            </a>
            <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-800 mt-4 pt-4">
                @csrf
                <button type="submit" class="w-full text-left py-3 px-6 text-red-400 hover:bg-red-900/20 transition">
                    <i class="fas fa-sign-out-alt mr-3"></i> Logout
                </button>
            </form>
        </nav>
    </div>

    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm p-4 flex justify-between items-center px-8">
            <h2 class="font-semibold text-gray-800 text-xl">Kelola Klien</h2>
            <div class="text-sm text-gray-500">Admin: <span class="font-bold">{{ Auth::user()->nama }}</span></div>
        </header>

        <main class="p-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($klien as $k)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $k->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $k->no_hp ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $status = $k->status_jadwal ?? 'Menunggu';
                                    $color = 'bg-yellow-100 text-yellow-800';
                                    if($status == 'Diterima' || $status == 'Selesai') $color = 'bg-green-100 text-green-800';
                                    if($status == 'Ditolak' || $status == 'Batal') $color = 'bg-red-100 text-red-800';
                                    if($status == 'Proses') $color = 'bg-blue-100 text-blue-800';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex justify-center space-x-2">
                                    <button @click="selectedView = {
                                            nama: '{{ $k->nama }}', 
                                            no_hp: '{{ $k->no_hp }}', 
                                            nik: '{{ $k->nik ?? '-' }}', 
                                            email: '{{ $k->email ?? '-' }}', 
                                            alamat: '{{ $k->alamat ?? '-' }}',
                                            status: '{{ $status }}'
                                        }; openView = true" 
                                        class="text-emerald-600 hover:text-emerald-900 bg-emerald-50 px-3 py-1 rounded-md transition">
                                        <i class="fas fa-eye"></i> Lihat
                                    </button>

                                    <button @click="selected = {id: '{{ $k->id_klien }}', nama: '{{ $k->nama }}', no_hp: '{{ $k->no_hp }}'}; openModal = true" 
                                        class="text-blue-600 hover:text-blue-900 bg-blue-50 px-3 py-1 rounded-md transition">
                                        Edit
                                    </button>
                                    
                                    <form action="{{ route('klien.destroy', $k->id_klien) }}" method="POST" onsubmit="return confirm('Hapus klien ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 font-medium">Belum ada data klien.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div x-show="openView" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-60" @click="openView = false"></div>
            <div class="bg-white rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full p-8 border border-gray-100">
                <div class="flex justify-between items-center mb-6 border-b pb-4">
                    <h3 class="text-xl font-bold text-gray-900"><i class="fas fa-id-card text-blue-600 mr-2"></i> Detail Data Klien</h3>
                    <button @click="openView = false" class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Lengkap</label>
                        <p class="text-gray-800 font-semibold text-lg" x-text="selectedView.nama"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">NIK</label>
                            <p class="text-gray-700" x-text="selectedView.nik"></p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">No. HP / WA</label>
                            <p class="text-gray-700" x-text="selectedView.no_hp"></p>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Email</label>
                        <p class="text-gray-700" x-text="selectedView.email"></p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Alamat Lengkap</label>
                        <p class="text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100" x-text="selectedView.alamat"></p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Status Terakhir</label>
                        <p>
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800" x-text="selectedView.status"></span>
                        </p>
                    </div>
                </div>

                <div class="mt-8">
                    <button @click="openView = false" class="w-full bg-gray-900 text-white py-3 rounded-lg hover:bg-gray-800 transition font-bold">
                        Tutup Detail
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="openModal = false"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Edit Klien: <span x-text="selected.nama"></span></h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
                </div>
                
                <form :action="'{{ url('kelola-klien') }}/' + selected.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" x-model="selected.nama" 
                            class="w-full border-gray-300 border rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" x-model="selected.no_hp" 
                            class="w-full border-gray-300 border rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="openModal = false" class="bg-white border px-4 py-2 rounded-md text-gray-700 hover:bg-gray-50 text-sm">Batal</button>
                        <button type="submit" class="bg-blue-600 px-4 py-2 rounded-md text-white hover:bg-blue-700 transition text-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style> [x-cloak] { display: none !important; } </style>
</body>
</html>