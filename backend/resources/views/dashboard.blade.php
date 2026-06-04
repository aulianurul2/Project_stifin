<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Dashboard - STIFIn Admin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />

    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid","Font Awesome 5 Regular","Font Awesome 5 Brands","simple-line-icons"],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function () { sessionStorage.fonts = true; },
        });
    </script>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />
</head>
<body>
    <div class="wrapper">

        @include('partials.sidebar')

        <div class="main-panel">
            <div class="main-header">
                @include('partials.navbar')
            </div>

            <div class="container">
                <div class="page-inner">

                    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                        <div>
                            <h3 class="fw-bold mb-1">Dashboard Utama</h3>
                            <h6 class="op-7 mb-1">Selamat datang, {{ Auth::user()->nama }}</h6>
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Periode: <strong>{{ $labelBulan }}</strong>
                            </small>
                        </div>
                    </div>

                    {{-- Stat Cards --}}
                    <div class="row">

                        {{-- Total Klien (keseluruhan) --}}
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Total Klien</p>
                                                <h4 class="card-title">{{ $totalKlien }}</h4>
                                                <small class="text-muted" style="font-size:10px;">Keseluruhan</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pendaftaran bulan ini --}}
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fas fa-edit"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Pendaftaran</p>
                                                <h4 class="card-title">{{ $pendaftaran }}</h4>
                                                <small class="text-muted" style="font-size:10px;">{{ $labelBulan }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hasil Tes selesai bulan ini --}}
                        <div class="col-sm-6 col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fas fa-file-medical"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Hasil Tes</p>
                                                <h4 class="card-title">{{ $hasilTes }}</h4>
                                                <small class="text-muted" style="font-size:10px;">{{ $labelBulan }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pendapatan bulan ini --}}
<div class="col-sm-6 col-md-3">
    <div class="card card-stats card-round">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-icon">
                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="col col-stats ms-3 ms-sm-0">
                    <div class="numbers">
                        <p class="card-category">Pendapatan</p>
                        <h4 class="card-title text-truncate" title="Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}">
                            Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}
                        </h4>
                        <small class="text-muted" style="font-size:10px;">{{ $labelBulan }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                        
                    </div>
                    

                    {{-- Chart + Aktivitas Terbaru --}}
                    <div class="row">

                        <div class="col-md-8">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <div class="card-title">
                                            Statistik Tes Selesai — {{ date('Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="min-height: 300px">
                                        <canvas id="stifinChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="card-head-row">
                                        <div class="card-title">Pendaftaran Terbaru</div>
                                        <div class="card-tools">
                                            <a href="{{ route('pendaftaran-tes') }}" class="btn btn-label-info btn-round btn-sm">
                                                Lihat Semua
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-muted px-3">Klien</th>
                                                    <th class="text-muted text-center px-3">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($aktivitasTerbaru as $row)
                                                    <tr>
                                                        <td class="px-3">
                                                            <p class="fw-semibold mb-0">{{ $row->nama_klien }}</p>
                                                            <span class="text-muted" style="font-size:11px;">
                                                                <i class="far fa-calendar-alt"></i>
                                                                {{ $row->tanggal ? date('d M Y', strtotime($row->tanggal)) : '-' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center px-3">
                                                            @php
                                                                $statusRaw = strtolower($row->status_tes ?? '');
                                                                $badge = 'badge-warning';
                                                                if (in_array($statusRaw, ['selesai','konfirmasi','disetujui','diterima'])) {
                                                                    $badge = 'badge-success';
                                                                } elseif (in_array($statusRaw, ['batal','ditolak'])) {
                                                                    $badge = 'badge-danger';
                                                                }
                                                            @endphp
                                                            <span class="badge {{ $badge }}">
                                                                {{ ucfirst($row->status_tes ?? 'Pending') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center text-muted py-4">
                                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                            <em>Belum ada pendaftaran bulan ini.</em>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                    </div>

                </div>
            </div>

            @include('partials.footer')
        </div>
    </div>

    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
        var ctx = document.getElementById('stifinChart').getContext('2d');

        var gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        // Highlight bulan berjalan
        var bulanIni = {{ (int) date('m') }} - 1; // index 0-based
        var pointColors = Array(12).fill('#ffffff');
        var pointBorderColors = Array(12).fill('#10b981');
        pointColors[bulanIni] = '#10b981';
        pointBorderColors[bulanIni] = '#065f46';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"],
                datasets: [{
                    label: "Tes Selesai",
                    data: @json($dataBulanan),
                    borderColor: '#10b981',
                    backgroundColor: gradient,
                    fill: true,
                    borderWidth: 3,
                    tension: 0.3,
                    pointRadius: 5,
                    pointBackgroundColor: pointColors,
                    pointBorderColor: pointBorderColors,
                    pointBorderWidth: 3,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: '#10b981',
                    pointHoverBorderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#000000',
                        padding: 10,
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        displayColors: false,
                        callbacks: {
                            title: (items) => `Bulan ${items[0].label}`,
                            label: (item) => `${item.raw} Tes Selesai`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e5e7eb' },
                        ticks: { color: '#6b7280', stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' }
                    }
                }
            }
        });
    </script>
</body>
</html>