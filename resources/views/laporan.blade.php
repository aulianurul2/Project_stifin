<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - STIFIn Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            .w-64 { display: none !important; }
            body { background: white !important; }
            main { padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-gray-50 flex">

     <div class="w-64 bg-slate-900 min-h-screen text-white flex-shrink-0">
        <div class="p-6 text-xl font-bold border-b border-slate-800 text-center">STIFIn Admin</div>
        <nav class="mt-4">
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>
            <a href="{{ route('kelola-klien') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
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
            <a href="{{ route('laporan.index') }}" class="flex items-center py-3 px-6 bg-blue-600 text-white">
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
        <header class="bg-white shadow-sm p-6 flex justify-between items-center no-print">
            <h2 class="text-2xl font-bold text-gray-800">Laporan Statistik</h2>
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-print mr-2"></i> Cetak Laporan
            </button>
        </header>

        <main class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center transition hover:shadow-md">
                    <div class="p-4 bg-blue-50 rounded-xl text-blue-600 mr-4"><i class="fas fa-users fa-2x"></i></div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Total Klien</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $totalKlien }}</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center transition hover:shadow-md">
                    <div class="p-4 bg-green-50 rounded-xl text-green-600 mr-4"><i class="fas fa-check-circle fa-2x"></i></div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Tes Selesai</p>
                        <h3 class="text-2xl font-bold text-gray-800">{{ $totalTesSelesai }}</h3>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center transition hover:shadow-md">
                    <div class="p-4 bg-amber-50 rounded-xl text-amber-600 mr-4"><i class="fas fa-wallet fa-2x"></i></div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Pendapatan</p>
                        <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold mb-6 text-gray-700 flex items-center">
                        <i class="fas fa-pie-chart mr-2 text-blue-600"></i> Distribusi Hasil STIFIn
                    </h3>
                    <div class="space-y-5">
                        @forelse($statistikHasil as $stat)
                        @php 
                            $persen = $totalTesSelesai > 0 ? ($stat->total / $totalTesSelesai) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="font-bold text-gray-600 uppercase tracking-tight">{{ $stat->hasil }}</span>
                                <span class="text-gray-500 font-medium">{{ $stat->total }} Orang ({{ round($persen, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-400 italic text-center py-4">Belum ada data distribusi.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold mb-6 text-gray-700 flex items-center">
                        <i class="fas fa-history mr-2 text-blue-600"></i> 10 Tes Terbaru
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-gray-400 border-b uppercase text-[10px] tracking-widest">
                                    <th class="pb-3 font-bold">Nama Klien</th>
                                    <th class="pb-3 font-bold">Hasil</th>
                                    <th class="pb-3 font-bold text-right">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($riwayatLaporan as $row)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 font-semibold text-gray-800">{{ $row->nama }}</td>
                                    <td class="py-4">
                                        <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-md text-[10px] font-black uppercase border border-blue-100">
                                            {{ $row->hasil ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right text-gray-500 font-medium">
                                        {{ date('d/m/Y', strtotime($row->tanggal)) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-10 text-center text-gray-400 italic">Tidak ada data riwayat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

</body>
</html>