    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Jadwal Tes - Admin</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body class="bg-gray-50 flex">

        <div class="w-64 bg-slate-900 min-h-screen text-white flex-shrink-0">
            <div class="p-6 text-xl font-bold border-b border-slate-800 text-center">STIFIn Admin</div>
            <nav class="mt-4">
                <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>
                <a href="{{ route('kelola-klien') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                    <i class="fas fa-users mr-3"></i> Kelola Klien
                </a>
                <a href="{{ route('pendaftaran-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                    <i class="fas fa-edit mr-3"></i> Pendaftaran Tes
                </a>
                <a href="{{ route('jadwal-tes') }}" class="flex items-center py-3 px-6 bg-blue-600 text-white">
                    <i class="fas fa-calendar-alt mr-3"></i> Jadwal Tes
                </a>
                <a href="{{ route('hasil-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                    <i class="fas fa-file-medical mr-3"></i> Hasil Tes
                </a>
                <a href="{{ route('laporan.index') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 hover:text-white transition">
                    <i class="fas fa-chart-bar mr-3"></i> Laporan
                </a>
                <div class="border-t border-slate-800 mt-4 pt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left py-3 px-6 text-red-400 hover:bg-red-900/20 transition">
                            <i class="fas fa-sign-out-alt mr-3"></i> Logout
                        </button>
                    </form>
                </div>
            </nav>
        </div>

        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm p-4 flex justify-between items-center px-8">
                <h2 class="font-semibold text-gray-800 text-xl">Agenda Jadwal Tes</h2>
                <span class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">
                    {{ $jadwal->count() }} Agenda Ditemukan
                </span>
            </header>

            <main class="p-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu & Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Klien</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Metode & Lokasi</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($jadwal as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-blue-50 text-blue-600 p-2 rounded-lg mr-3">
                                            <i class="far fa-calendar-check"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->waktu }} WIB</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item->nama_klien }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(Str::contains(strtolower($item->lokasi), 'home'))
                                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">
                                            <i class="fas fa-house-user mr-1"></i> Home Visit
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-xs font-bold">
                                            <i class="fas fa-building mr-1"></i> Visit Office
                                        </span>
                                    @endif
                                    <div class="text-xs text-gray-400 mt-1 italic">{{ $item->lokasi }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center space-x-3">
                                        <a href="https://wa.me/{{ $item->no_hp }}" target="_blank" class="text-green-600 hover:text-green-800">
                                            <i class="fab fa-whatsapp fa-lg"></i>
                                        </a>
                                        <form action="{{ route('jadwal.destroy', $item->id_jadwal) }}" method="POST" onsubmit="return confirm('Batalkan jadwal?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">Belum ada agenda tes.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </body>
    </html>