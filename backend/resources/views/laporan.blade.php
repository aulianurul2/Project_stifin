<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Laporan - STIFIn Admin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function () { sessionStorage.fonts = true; },
        });
    </script>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

    <style>
        @media print {
            .no-print, .sidebar, .main-header, .footer { display: none !important; }
            .main-panel { width: 100% !important; float: none !important; padding: 0 !important; margin: 0 !important; }
            .page-inner { padding: 0 !important; }
            body { background: white !important; }
            .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body>
    <div class="wrapper">

        <div class="no-print">
            @include('partials.sidebar')
        </div>

        <div class="main-panel">
            <div class="main-header no-print">
                @include('partials.navbar')
            </div>

            <div class="container">
                <div class="page-inner">

                    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                        <div>
                            <h3 class="fw-bold mb-1">Laporan Statistik</h3>
                            <ul class="breadcrumbs mb-0 p-0 no-print" style="background: transparent;">
                                <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#" class="text-muted">Laporan</a></li>
                            </ul>
                        </div>
                        <div class="d-flex gap-2 no-print">
                            <a href="{{ route('laporan.pdf') }}" class="btn btn-danger btn-round btn-sm px-3 shadow-sm">
                                <i class="fas fa-file-pdf me-1"></i> PDF
                            </a>
                            <a href="{{ route('laporan.excel') }}" class="btn btn-success btn-round btn-sm px-3 shadow-sm">
                                <i class="fas fa-file-excel me-1"></i> Excel
                            </a>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-6 col-md-4">
                            <div class="card card-stats card-round border border-light shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center text-primary bubble-shadow-small">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category text-uppercase fw-bold text-muted small mb-1">Total Klien</p>
                                                <h4 class="card-title fw-bold text-dark mb-0">{{ $totalKlien }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="card card-stats card-round border border-light shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center text-success bubble-shadow-small">
                                                <i class="far fa-check-circle"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category text-uppercase fw-bold text-muted small mb-1">Tes Selesai</p>
                                                <h4 class="card-title fw-bold text-dark mb-0">{{ $totalTesSelesai }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <div class="card card-stats card-round border border-light shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center text-warning bubble-shadow-small">
                                                <i class="fas fa-wallet"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category text-uppercase fw-bold text-muted small mb-1">Pendapatan</p>
                                                <h4 class="card-title fw-bold text-dark mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-lg-6 mb-4">
                            <div class="card card-round shadow-sm h-100">
                                <div class="card-header border-0 pb-0 bg-transparent">
                                    <div class="card-title fw-bold text-secondary d-flex align-items-center" style="font-size: 1.1rem;">
                                        <i class="fas fa-chart-pie text-primary me-2"></i> Distribusi Hasil STIFIn
                                    </div>
                                </div>
                                <div class="card-body pt-4">
                                    <div class="d-flex flex-column gap-3">
                                        @forelse($statistikHasil as $stat)
                                            @php
                                                $persen = $totalTesSelesai > 0 ? ($stat->total / $totalTesSelesai) * 100 : 0;
                                            @endphp
                                            <div>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold text-dark text-uppercase small">{{ $stat->hasil }}</span>
                                                    <span class="text-muted fw-semibold small">{{ $stat->total }} Orang</span>
                                                </div>
                                                <div class="progress card-round" style="height: 10px; background-color: #f1f3f5;">
                                                    <div class="progress-bar bg-primary card-round" role="progressbar" style="width: {{ $persen }}%"></div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4 text-muted fst-italic">Belum ada data distribusi.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6 mb-4">
                            <div class="card card-round shadow-sm h-100">
                                <div class="card-header border-0 pb-0 bg-transparent">
                                    <div class="card-title fw-bold text-secondary d-flex align-items-center" style="font-size: 1.1rem;">
                                        <i class="fas fa-history text-primary me-2"></i> 10 Tes Terbaru
                                    </div>
                                </div>
                                <div class="card-body p-0 pt-3">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light text-secondary">
                                                <tr>
                                                    <th class="px-4 py-2 fw-bold text-uppercase small" style="font-size: 11px;">Nama Klien</th>
                                                    <th class="px-4 py-2 fw-bold text-uppercase small" style="font-size: 11px;">Hasil</th>
                                                    <th class="px-4 py-2 fw-bold text-uppercase small text-end" style="font-size: 11px;">Tanggal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($riwayatLaporan as $row)
                                                    <tr>
                                                        <td class="px-4 py-3 fw-bold text-dark" style="font-size: 0.9rem;">{{ $row->nama }}</td>
                                                        <td class="px-4 py-3">
                                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase border border-primary border-opacity-20 px-2.5 py-1.5 rounded" style="font-size: 10px;">
                                                                {{ $row->hasil ?? 'N/A' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3 text-end text-muted small">{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-5 text-muted fst-italic">Tidak ada data riwayat.</td>
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

            <footer class="footer no-print">
                <div class="container-fluid d-flex justify-content-between">
                    <div class="copyright text-center w-100">
                        2026, made with <i class="fa fa-heart text-danger"></i> by STIFIn Project
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>
</body>
</html>
