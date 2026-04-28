<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 flex">

    <div class="w-64 bg-slate-900 min-h-screen text-white flex-shrink-0">
        <div class="p-6 text-xl font-bold border-b border-slate-800 text-center">STIFIn Admin</div>
        <nav class="mt-4">
            <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 bg-blue-600 text-white">
                <i class="fas fa-home mr-3"></i> Dashboard
            </a>
            <a href="{{ route('kelola-klien') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                <i class="fas fa-users mr-3"></i> Kelola Klien
            </a>
            <a href="{{ route('pendaftaran-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                <i class="fas fa-edit mr-3"></i> Pendaftaran Tes
            </a>
            <a href="{{ route('jadwal-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                <i class="fas fa-calendar-alt mr-3"></i> Jadwal Tes
            </a>
            <a href="{{ route('hasil-tes') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
                <i class="fas fa-file-medical mr-3"></i> Hasil Tes
            </a>
            <a href="{{ route('laporan.index') }}" class="flex items-center py-3 px-6 text-gray-400 hover:bg-slate-800 transition">
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
            <h2 class="font-semibold text-gray-800 text-xl">Dashboard Utama</h2>
            <div class="text-sm text-gray-500">Admin: <span class="font-bold">{{ Auth::user()->nama }}</span></div>
        </header>

        <main class="p-8">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Klien</p>
                    <div class="flex justify-between items-end mt-2">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $totalKlien }}</h3>
                        <i class="fas fa-users text-blue-100 text-3xl"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pendaftaran</p>
                    <div class="flex justify-between items-end mt-2">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $pendaftaran }}</h3>
                        <i class="fas fa-edit text-blue-100 text-3xl"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Hasil Tes</p>
                    <div class="flex justify-between items-end mt-2">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $hasilTes }}</h3>
                        <i class="fas fa-file-medical text-blue-100 text-3xl"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Jadwal Tes</p>
                    <div class="flex justify-between items-end mt-2">
                        <h3 class="text-3xl font-bold text-gray-900">{{ $jadwalTerkini }}</h3>
                        <i class="fas fa-calendar-alt text-blue-100 text-3xl"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h4 class="text-sm font-bold text-gray-700 mb-6 uppercase tracking-wider border-b pb-3">Statistik Pertumbuhan Tes</h4>
                    <div class="h-80">
                        <canvas id="stifinChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex flex-col">
                    <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider border-b pb-3">Aktivitas Terbaru</h4>
                    <div class="overflow-y-auto flex-1">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Klien</th>
                                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($aktivitasTerbaru as $row)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-3 py-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $row->nama }}</p>
                                        <p class="text-[11px] text-gray-400">{{ date('d M Y', strtotime($row->tanggal)) }}</p>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        @php
                                            $st = $row->status_tes;
                                            $color = ($st == 'Selesai') ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $color }}">
                                            {{ $st }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-3 py-10 text-center text-gray-400 italic text-sm">Belum ada aktivitas.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        const ctx = document.getElementById('stifinChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Tes',
                    data: @json($dataBulanan),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1, font: { size: 11 } },
                        grid: { borderDash: [5, 5] }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    </script>
</body>
</html>