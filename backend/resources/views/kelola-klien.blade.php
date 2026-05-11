<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Kelola Klien - STIFIn Admin</title>
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

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .modal-backdrop-custom {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="wrapper" x-data="{ openModal: false, openView: false, selected: {}, selectedView: {} }">

        @include('partials.sidebar')

        <div class="main-panel">
            <div class="main-header">
                @include('partials.navbar')
            </div>

            <div class="container">
                <div class="page-inner">
                    <div class="page-header">
                        <h3 class="fw-bold mb-3">Kelola Klien</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="{{ route('kelola-klien') }}">Data Klien</a></li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Daftar Klien STIFIn</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <div class="table-responsive">
                                        <table id="add-row" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>No. HP</th>
                                                    <th>Status</th>
                                                    <th style="width: 10%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($klien as $k)
                                                <tr>
                                                    <td>{{ $k->nama }}</td>
                                                    <td>{{ $k->no_hp ?? '-' }}</td>
                                                    <td>
                                                        @php
                                                            $status = $k->status_jadwal ?? 'Menunggu';
                                                            $badgeClass = 'badge-warning';
                                                            if(in_array($status, ['Diterima', 'Selesai'])) $badgeClass = 'badge-success';
                                                            if(in_array($status, ['Ditolak', 'Batal'])) $badgeClass = 'badge-danger';
                                                            if($status == 'Proses') $badgeClass = 'badge-info';
                                                        @endphp
                                                        <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="form-button-action">
                                                            <button type="button" class="btn btn-link btn-primary btn-lg"
                                                                @click="selectedView = {
                                                                    nama: '{{ $k->nama }}',
                                                                    no_hp: '{{ $k->no_hp }}',
                                                                    tanggal_lahir: '{{ $k->tanggal_lahir }}',
                                                                    jenis_kelamin: '{{ $k->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}',
                                                                    golongan_darah: '{{ $k->golongan_darah }}',
                                                                    email: '{{ $k->email ?? '-' }}',
                                                                    alamat: '{{ $k->alamat ?? '-' }}',
                                                                    institusi: '{{ $k->institusi ?? '-' }}',
                                                                    sosmed: '{{ $k->sosmed ?? '-' }}',
                                                                    domisili: '{{ $k->domisili ?? '-' }}',
                                                                    status: '{{ $status }}'
                                                                }; openView = true">
                                                                <i class="fa fa-eye"></i>
                                                            </button>

                                                            <button type="button" class="btn btn-link btn-primary btn-lg"
                                                                @click="selected = {
                                                                    id: '{{ $k->id_klien }}',
                                                                    nama: '{{ $k->nama }}',
                                                                    no_hp: '{{ $k->no_hp }}',
                                                                    email: '{{ $k->email }}',
                                                                    alamat: '{{ $k->alamat }}'
                                                                }; openModal = true">
                                                                <i class="fa fa-edit"></i>
                                                            </button>

                                                            <form action="{{ route('klien.destroy', $k->id_klien) }}" method="POST" onsubmit="return confirm('Hapus klien ini?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-link btn-danger">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">Belum ada data klien.</td>
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

        <div x-show="openView" x-cloak class="modal-backdrop-custom">
            <div class="card w-75 shadow-lg" style="max-width: 700px;" @click.away="openView = false">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title"><i class="fas fa-id-card text-primary me-2"></i> Detail Profil Klien</h4>
                    <button type="button" class="close" @click="openView = false"><span>&times;</span></button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Nama Lengkap</label>
                            <p class="fw-bold mb-0" x-text="selectedView.nama"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">No. HP / WA</label>
                            <p class="mb-0" x-text="selectedView.no_hp"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Email</label>
                            <p class="mb-0" x-text="selectedView.email"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Tanggal Lahir</label>
                            <p class="mb-0" x-text="selectedView.tanggal_lahir"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Gender / Gol. Darah</label>
                            <p class="mb-0"><span x-text="selectedView.jenis_kelamin"></span> / <span x-text="selectedView.golongan_darah"></span></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Status Sistem</label>
                            <div><span class="badge badge-primary" x-text="selectedView.status"></span></div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted small fw-bold text-uppercase">Alamat</label>
                            <p class="mb-0 p-2 bg-light rounded shadow-sm italic" x-text="selectedView.alamat"></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-black btn-round" @click="openView = false">Tutup</button>
                </div>
            </div>
        </div>

        <div x-show="openModal" x-cloak class="modal-backdrop-custom">
            <div class="card w-50 shadow-lg" style="max-width: 500px;" @click.away="openModal = false">
                <div class="card-header">
                    <h4 class="card-title">Edit Klien: <span class="text-primary" x-text="selected.nama"></span></h4>
                </div>
                <form :action="'{{ url('kelola-klien') }}/' + selected.id" method="POST">
                    @csrf @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" x-model="selected.nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" x-model="selected.no_hp" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" x-model="selected.email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" x-model="selected.alamat" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="card-action d-flex justify-content-end">
                        <button type="button" class="btn btn-border btn-round me-2" @click="openModal = false">Batal</button>
                        <button type="submit" class="btn btn-primary btn-round">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>
</body>
</html>
