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
    <h4 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider border-b pb-3">Aktivitas Pendaftaran</h4>
    <div class="overflow-y-auto flex-1">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klien</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($pendaftaranTerbaru as $row) {{-- Ganti variabel jika perlu sesuai Controller --}}
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-3 py-4">
                        {{-- Mengambil nama klien dari data pendaftaran --}}
                        <p class="text-sm font-medium text-gray-900">{{ $row->nama_klien }}</p>
                        {{-- Mengambil info waktu pendaftaran --}}
                       <p class="text-[11px] text-gray-400">{{ $row->tanggal ? \Carbon\Carbon::parse($row->tanggal)->diffForHumans() : 'Baru saja' }}</p>
                    </td>
                    <td class="px-3 py-4 text-center">
                        @php
                            $st = $row->status ?? 'Menunggu';
                            $color = 'bg-yellow-100 text-yellow-800'; // Default Menunggu
                            
                            if ($st == 'Diterima') {
                                $color = 'bg-green-100 text-green-800';
                            } elseif ($st == 'Ditolak') {
                                $color = 'bg-red-100 text-red-800';
                            }
                        @endphp
                        <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $color }}">
                            {{ $st }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="px-3 py-10 text-center text-gray-400 italic text-sm">Belum ada pendaftaran masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <a href="{{ route('pendaftaran-tes') }}" class="text-center text-xs text-blue-600 font-bold mt-4 hover:underline">Lihat Semua Pendaftaran</a>
</div>

            </div>
        </main>
    </div>

    <script>
        const ctx = document.getElementById('stifinChart').getContext('2d');
        
        // Membuat efek gradasi warna yang modern
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.25)'); // Biru transparan di atas
        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');    // Menghilang di bawah

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Tes',
                    data: @json($dataBulanan),
                    borderColor: '#2563eb', // Warna garis biru pekat
                    backgroundColor: gradient, // Menggunakan gradasi yang dibuat tadi
                    fill: true,
                    tension: 0.45, // Membuat garis lebih melengkung halus
                    borderWidth: 3,
                    pointRadius: 0, // Sembunyikan titik default
                    pointHoverRadius: 6, // Munculkan titik besar saat kursor di atasnya
                    pointHitRadius: 30,
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b', // Warna gelap slate
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `📈 Total: ${context.raw} Tes`;
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        border: { display: false },
                        ticks: { 
                            stepSize: 1, 
                            color: '#94a3b8',
                            font: { size: 11 } 
                        },
                        grid: { 
                            color: 'rgba(226, 232, 240, 0.6)',
                            drawTicks: false
                        }
                    },
                    x: { 
                        border: { display: false },
                        grid: { display: false },
                        ticks: { 
                            color: '#94a3b8',
                            font: { size: 11 } 
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>