<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Manajemen Slot Jadwal - STIFIn Admin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
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

                    <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h3 class="fw-bold mb-3">Manajemen Slot Jadwal Tes</h3>
                            <ul class="breadcrumbs mb-3">
                                <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="{{ route('jadwal-tes') }}">Jadwal Tes</a></li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <span class="badge badge-primary px-3 py-2 fs-7 btn-round">
                                <i class="fas fa-calendar-check me-1"></i> {{ $jadwal->count() }} Slot Diterbitkan
                            </span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show card-round" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round shadow-sm">
                                <div class="card-header py-3">
                                    <div class="card-title text-uppercase fs-7 fw-bold text-muted">
                                        <i class="fas fa-plus-circle text-primary me-2"></i> Terbitkan Slot Baru
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('jadwal.store') }}" method="POST">
                                        @csrf
                                        <div class="row row-demo-grid align-items-end">
                                            <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                                <div class="form-group p-0">
                                                    <label class="mb-2 text-uppercase font-weight-bold text-muted fs-8">Tanggal</label>
                                                    <input type="date" name="tanggal" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-2 mb-3 mb-md-0">
                                                <div class="form-group p-0">
                                                    <label class="mb-2 text-uppercase font-weight-bold text-muted fs-8">Waktu</label>
                                                    <input type="time" name="waktu" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-2 mb-3 mb-md-0">
                                                <div class="form-group p-0">
                                                    <label class="mb-2 text-uppercase font-weight-bold text-muted fs-8">Kuota</label>
                                                    <input type="number" name="kuota" value="1" min="1" class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                                <div class="form-group p-0">
                                                    <label class="mb-2 text-uppercase font-weight-bold text-muted fs-8">Lokasi / Metode</label>
                                                    <select name="lokasi" class="form-select form-control">
                                                        <option value="Kantor Cabang">Kantor Cabang</option>
                                                        <option value="Home Visit">Home Visit</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-2">
                                                <button type="submit" class="btn btn-primary btn-round w-100 fw-bold px-4">
                                                    <i class="fas fa-paper-plane me-1"></i> Terbitkan
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="card card-round shadow-sm">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0 align-middle">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Waktu</th>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Lokasi</th>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Status & Kuota</th>
                                                    <th class="px-4 py-3 text-center text-uppercase font-weight-bold text-muted fs-8" style="width: 10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($jadwal as $item)
                                                <tr>
                                                    <td class="px-4 py-3">
                                                        <div class="fw-bold text-dark fs-6">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</div>
                                                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB</small>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        @if($item->lokasi == 'Home Visit')
                                                            <span class="badge badge-secondary px-3 py-1 btn-round text-uppercase fs-8 fw-bold">
                                                                <i class="fas fa-house-user me-1"></i> Home Visit
                                                            </span>
                                                        @else
                                                            <span class="badge badge-info px-3 py-1 btn-round text-uppercase fs-8 fw-bold">
                                                                <i class="fas fa-building me-1"></i> Kantor Cabang
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="fw-bold {{ $item->status == 'Tersedia' ? 'text-success' : 'text-danger' }}">
                                                            {{ $item->status }} <span class="text-muted fw-normal">({{ $item->kuota }} Slot)</span>
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <form action="{{ route('jadwal.destroy', $item->id_jadwal) }}" method="POST" onsubmit="return confirm('Hapus slot ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-icon btn-link btn-danger btn-sm" data-bs-toggle="tooltip" title="Hapus Slot">
                                                                <i class="fas fa-trash-alt fs-6"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="px-4 py-5 text-center text-muted italic">Belum ada slot jadwal yang diterbitkan.</td>
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

            <footer class="footer">
                <div class="container-fluid d-flex justify-content-between">
                    <div class="copyright text-center w-100">
                        2026, made with <i class="fa fa-heart heart text-danger"></i> by STIFIn Project
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

    <script>
        $(document).ready(function(){
            // Inisialisasi komponen Tooltip Bootstrap 5
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>
