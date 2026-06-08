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

    <!-- Third Party Plugins (Alpine.js & SweetAlert2) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    [x-cloak] { display: none !important; }

    /* ── Modal Detail Klien (konsisten pendaftaran-tes) ── */
    #modalDetailKlien .modal-content {
        border: none; border-radius: 20px; overflow: hidden;
        box-shadow: 0 24px 80px rgba(0,0,0,0.18);
    }
    #modalDetailKlien .modal-klien-header {
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
        padding: 28px 32px 24px; position: relative; overflow: hidden;
    }
    #modalDetailKlien .modal-klien-header::before {
        content: ''; position: absolute; top: -40px; right: -40px;
        width: 180px; height: 180px; background: rgba(255,255,255,0.06); border-radius: 50%;
    }
    #modalDetailKlien .modal-klien-header::after {
        content: ''; position: absolute; bottom: -60px; left: -20px;
        width: 140px; height: 140px; background: rgba(255,255,255,0.04); border-radius: 50%;
    }
    #modalDetailKlien .modal-klien-header-inner {
        position: relative; z-index: 2;
        display: flex; align-items: center; justify-content: space-between;
    }
    #modalDetailKlien .modal-klien-eyebrow {
        font-size: 0.7rem; font-weight: 700; letter-spacing: 1.5px;
        text-transform: uppercase; color: rgba(255,255,255,0.65); margin-bottom: 4px;
    }
    #modalDetailKlien .modal-klien-title {
        font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0;
    }
    #modalDetailKlien .modal-klien-body {
        padding: 0; max-height: 72vh; overflow-y: auto; background: #f4f6fb;
    }
    #modalDetailKlien .modal-klien-body::-webkit-scrollbar { width: 5px; }
    #modalDetailKlien .modal-klien-body::-webkit-scrollbar-track { background: #f4f6fb; }
    #modalDetailKlien .modal-klien-body::-webkit-scrollbar-thumb { background: #c5cae9; border-radius: 10px; }
    #modalDetailKlien .klien-section-label {
        font-size: 0.68rem; font-weight: 800; letter-spacing: 1.4px;
        text-transform: uppercase; color: #90a4ae; padding: 16px 24px 8px;
    }
    .detail-section-card {
        margin: 0 16px 12px; background: #fff; border-radius: 14px;
        border: 1px solid #e8eaf6; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;
    }
    .detail-section-card .dsc-body { padding: 16px 20px; }
    .detail-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .detail-info-item label {
        font-size: 0.67rem; font-weight: 800; letter-spacing: 1px;
        text-transform: uppercase; color: #90a4ae; display: block; margin-bottom: 3px;
    }
    .detail-info-item span { font-size: 0.84rem; font-weight: 600; color: #37474f; }
    .detail-info-item.full-width { grid-column: 1 / -1; }
    .detail-alamat-box {
        background: #f8f9ff; border-left: 3px solid #c5cae9;
        border-radius: 0 6px 6px 0; padding: 8px 12px;
        font-size: 0.82rem; color: #546e7a; line-height: 1.6; white-space: pre-line;
    }
    #modalDetailKlien .modal-klien-footer {
        padding: 16px 24px; background: #fff; border-top: 1px solid #e8eaf6;
        display: flex; justify-content: flex-end;
    }
    #modalDetailKlien .btn-tutup {
        padding: 10px 28px; border-radius: 10px; font-weight: 700; font-size: 0.875rem;
        border: 1.5px solid #dee2e6; background: #fff; color: #495057; transition: all 0.2s;
    }
    #modalDetailKlien .btn-tutup:hover { background: #f8f9fa; border-color: #adb5bd; }

    /* ── Modal Edit Klien (konsisten modal status) ── */
    #modalEditKlien .modal-content {
        border: none; border-radius: 16px; overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    #modalEditKlien .modal-edit-header {
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e1b 100%);
        padding: 28px 28px 20px; text-align: center;
    }
    #modalEditKlien .modal-edit-icon {
        width: 64px; height: 64px; background: rgba(255,255,255,0.2);
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px; backdrop-filter: blur(4px);
    }
    #modalEditKlien .modal-edit-icon i { font-size: 1.8rem; color: #fff; }
    #modalEditKlien .modal-edit-title { color: #fff; font-size: 1.2rem; font-weight: 700; margin: 0 0 4px; }
    #modalEditKlien .modal-edit-subtitle { color: rgba(255,255,255,0.75); font-size: 0.82rem; margin: 0; }
    #modalEditKlien .modal-edit-body {
        padding: 0; max-height: 65vh; overflow-y: auto; background: #f4f6fb;
    }
    #modalEditKlien .modal-edit-body::-webkit-scrollbar { width: 5px; }
    #modalEditKlien .modal-edit-body::-webkit-scrollbar-track { background: #f4f6fb; }
    #modalEditKlien .modal-edit-body::-webkit-scrollbar-thumb { background: #c5cae9; border-radius: 10px; }
    #modalEditKlien .edit-section-label {
        font-size: 0.68rem; font-weight: 800; letter-spacing: 1.4px;
        text-transform: uppercase; color: #90a4ae; padding: 16px 24px 8px;
    }
    .edit-section-card {
        margin: 0 16px 12px; background: #fff; border-radius: 14px;
        border: 1px solid #e8eaf6; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;
    }
    .edit-section-card .esc-body { padding: 16px 20px; }
    #modalEditKlien .modal-edit-footer {
        padding: 16px 28px 24px; display: flex; gap: 10px; justify-content: center;
    }
    #modalEditKlien .btn-edit-batal {
        flex: 1; max-width: 140px; padding: 10px 20px; border-radius: 10px;
        font-weight: 600; font-size: 0.875rem; border: 1.5px solid #dee2e6;
        background: #fff; color: #495057; transition: all 0.2s;
    }
    #modalEditKlien .btn-edit-batal:hover { background: #f8f9fa; border-color: #adb5bd; }
    #modalEditKlien .btn-edit-simpan {
        flex: 1; max-width: 180px; padding: 10px 20px; border-radius: 10px;
        font-weight: 600; font-size: 0.875rem; border: none;
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e1b 100%);
        color: #fff; transition: all 0.2s; box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    #modalEditKlien .btn-edit-simpan:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(46, 125, 50, 0.4); }
    #modalEditKlien .btn-edit-simpan:active { transform: translateY(0); }
    #modalEditKlien .form-control, #modalEditKlien .form-select {
        border: 1px solid #ced4da !important; color: #495057 !important; border-radius: 10px !important;
    }
    #modalEditKlien .form-control:focus, #modalEditKlien .form-select:focus {
        border-color: #2e7d32 !important; box-shadow: none !important;
    }

    /* ── Modal Hapus Klien ── */
    #modalHapusKlien .modal-content {
        border: none; border-radius: 16px; overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    #modalHapusKlien .modal-hapus-header {
        background: linear-gradient(135deg, #ff4d4d 0%, #c0392b 100%);
        padding: 28px 28px 20px; text-align: center;
    }
    #modalHapusKlien .modal-hapus-icon {
        width: 64px; height: 64px; background: rgba(255,255,255,0.2);
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px; backdrop-filter: blur(4px);
    }
    #modalHapusKlien .modal-hapus-icon i { font-size: 1.8rem; color: #fff; }
    #modalHapusKlien .modal-hapus-title { color: #fff; font-size: 1.2rem; font-weight: 700; margin: 0; }
    #modalHapusKlien .modal-hapus-body { padding: 24px 28px 8px; text-align: center; }
    #modalHapusKlien .hapus-klien-name {
        display: inline-block; background: #fff4f4; border: 1px solid #ffd5d5;
        border-radius: 8px; padding: 6px 14px; font-weight: 600; color: #c0392b;
        font-size: 0.875rem; max-width: 100%; overflow: hidden; text-overflow: ellipsis;
        white-space: nowrap; margin-bottom: 12px;
    }
    #modalHapusKlien .hapus-warning-text { font-size: 0.875rem; color: #6c757d; line-height: 1.6; margin: 0; }
    #modalHapusKlien .modal-hapus-footer {
        padding: 16px 28px 24px; display: flex; gap: 10px; justify-content: center;
    }
    #modalHapusKlien .btn-hapus-batal {
        flex: 1; max-width: 140px; padding: 10px 20px; border-radius: 10px;
        font-weight: 600; font-size: 0.875rem; border: 1.5px solid #dee2e6;
        background: #fff; color: #495057; transition: all 0.2s;
    }
    #modalHapusKlien .btn-hapus-batal:hover { background: #f8f9fa; border-color: #adb5bd; }
    #modalHapusKlien .btn-hapus-konfirmasi {
        flex: 1; max-width: 160px; padding: 10px 20px; border-radius: 10px;
        font-weight: 600; font-size: 0.875rem; border: none;
        background: linear-gradient(135deg, #ff4d4d 0%, #c0392b 100%);
        color: #fff; transition: all 0.2s; box-shadow: 0 4px 12px rgba(192,57,43,0.3);
    }
    #modalHapusKlien .btn-hapus-konfirmasi:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(192,57,43,0.4); }
    #modalHapusKlien .btn-hapus-konfirmasi:active { transform: translateY(0); }
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
                    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                        <h3 class="fw-bold mb-3">Kelola Klien</h3>
                        <ul class="breadcrumbs mb-3">
                            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                            <li class="separator"><i class="icon-arrow-right"></i></li>
                            <li class="nav-item"><a href="{{ route('kelola-klien') }}">Data Klien</a></li>
                        </ul>
                    </div>
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
                                                    <th class="text-center" style="width: 15%">Aksi</th>
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
                                                    <td class="text-center">
    <div class="d-flex justify-content-center align-items-center gap-2">

        {{-- Tombol Detail --}}
        <button type="button"
            class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center"
            style="background-color: #d1e7dd; border-color: #badbcc; min-width: 56px;"
            title="Lihat Detail"
            onclick="openDetailKlien(
                '{{ $k->nama }}',
                '{{ $k->no_hp ?? '-' }}',
                '{{ $k->email ?? '-' }}',
                '{{ $k->tanggal_lahir ?? '-' }}',
                '{{ $k->jenis_kelamin }}',
                '{{ $k->golongan_darah ?? '-' }}',
                '{{ $k->institusi ?? '-' }}',
                '{{ $k->sosmed ?? '-' }}',
                '{{ $k->domisili ?? '-' }}',
                {{ json_encode($k->alamat ?? '-') }},
                '{{ $status }}'
            )">
            <i class="fa fa-eye mb-1" style="color: #0f5132;"></i>
            <span style="font-size: 9px; color: #0f5132; font-weight: 600;">Detail</span>
        </button>

        {{-- Tombol Edit --}}
        <button type="button"
            class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center"
            style="background-color: #cfe2ff; border-color: #b6d4fe; min-width: 56px;"
            title="Edit Profil"
            onclick="openEditKlien(
                '{{ $k->id_klien }}',
                {{ json_encode($k->nama) }},
                '{{ $k->no_hp }}',
                '{{ $k->email }}',
                '{{ $k->tanggal_lahir }}',
                '{{ $k->jenis_kelamin }}',
                '{{ $k->golongan_darah }}',
                {{ json_encode($k->institusi ?? '') }},
                {{ json_encode($k->sosmed ?? '') }},
                {{ json_encode($k->domisili ?? '') }},
                {{ json_encode($k->alamat ?? '') }}
            )">
            <i class="fa fa-edit mb-1" style="color: #084298;"></i>
            <span style="font-size: 9px; color: #084298; font-weight: 600;">Edit</span>
        </button>

        {{-- Tombol Hapus --}}
        <form id="delete-form-{{ $k->id_klien }}" action="{{ route('klien.destroy', $k->id_klien) }}" method="POST" style="display:none;">
            @csrf @method('DELETE')
        </form>
        <button type="button"
            class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center"
            style="background-color: #f8d7da; border-color: #f5c2c7; min-width: 56px;"
            title="Hapus Klien"
            onclick="openHapusKlien('{{ $k->id_klien }}', {{ json_encode($k->nama) }})">
            <i class="fas fa-trash-alt mb-1" style="color: #842029;"></i>
            <span style="font-size: 9px; color: #842029; font-weight: 600;">Hapus</span>
        </button>

    </div>
</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted italic">Belum ada data klien.</td>
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

      {{-- ══════════════════════════════════════
     MODAL DETAIL PROFIL KLIEN
══════════════════════════════════════ --}}
<div class="modal fade" id="modalDetailKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-klien-header">
                <div class="modal-klien-header-inner">
                    <div>
                        <p class="modal-klien-eyebrow"><i class="fas fa-id-badge me-1"></i> Profil Klien</p>
                        <h5 class="modal-klien-title">Detail Profil Klien</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.8;"></button>
                </div>
            </div>

            <div class="modal-klien-body">

                <p class="klien-section-label"><i class="fas fa-user me-1"></i> Identitas Utama</p>
                <div class="detail-section-card">
                    <div class="dsc-body">
                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <label>Nama Lengkap</label>
                                <span id="dk-nama">—</span>
                            </div>
                            <div class="detail-info-item">
                                <label>Status</label>
                                <span id="dk-status">—</span>
                            </div>
                            <div class="detail-info-item">
                                <label>No. HP / WhatsApp</label>
                                <span id="dk-nohp">—</span>
                            </div>
                            <div class="detail-info-item">
                                <label>Alamat Email</label>
                                <span id="dk-email">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="klien-section-label"><i class="fas fa-info-circle me-1"></i> Informasi Personal</p>
                <div class="detail-section-card">
                    <div class="dsc-body">
                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <label>Tanggal Lahir</label>
                                <span id="dk-tgllahir">—</span>
                            </div>
                            <div class="detail-info-item">
                                <label>Jenis Kelamin</label>
                                <span id="dk-jk">—</span>
                            </div>
                            <div class="detail-info-item">
                                <label>Golongan Darah</label>
                                <span id="dk-goldar">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="klien-section-label"><i class="fas fa-building me-1"></i> Afiliasi & Media Sosial</p>
                <div class="detail-section-card">
                    <div class="dsc-body">
                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <label>Institusi / Perusahaan</label>
                                <span id="dk-institusi">—</span>
                            </div>
                            <div class="detail-info-item">
                                <label>Media Sosial</label>
                                <span id="dk-sosmed">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="klien-section-label"><i class="fas fa-map-marker-alt me-1"></i> Lokasi Tempat Tinggal</p>
                <div class="detail-section-card" style="margin-bottom: 16px;">
                    <div class="dsc-body">
                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <label>Domisili</label>
                                <span id="dk-domisili">—</span>
                            </div>
                            <div class="detail-info-item full-width">
                                <label>Alamat Lengkap</label>
                                <div id="dk-alamat" class="detail-alamat-box"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-klien-footer">
                <button type="button" class="btn-tutup" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tutup
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ══════════════════════════════════════
     MODAL EDIT PROFIL KLIEN
══════════════════════════════════════ --}}
<div class="modal fade" id="modalEditKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-edit-header">
                <div class="modal-edit-icon"><i class="fas fa-user-edit"></i></div>
                <p class="modal-edit-title">Perbarui Data Klien</p>
                <p class="modal-edit-subtitle">Ubah informasi profil klien di bawah ini</p>
            </div>

            <form id="formEditKlien" method="POST">
                @csrf @method('PUT')

                <div class="modal-edit-body">

                    <p class="edit-section-label"><i class="fas fa-user me-1"></i> Data Identitas Utama</p>
                    <div class="edit-section-card">
                        <div class="esc-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Nama Lengkap</label>
                                    <input type="text" name="nama" id="ek-nama" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">No. HP / WhatsApp</label>
                                    <input type="text" name="no_hp" id="ek-nohp" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Alamat Email</label>
                                    <input type="email" name="email" id="ek-email" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="edit-section-label"><i class="fas fa-info-circle me-1"></i> Informasi Personal</p>
                    <div class="edit-section-card">
                        <div class="esc-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" id="ek-tgllahir" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" id="ek-jk" class="form-control form-select">
                                        <option value="">- Pilih -</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Gol. Darah</label>
                                    <select name="golongan_darah" id="ek-goldar" class="form-control form-select">
                                        <option value="">-</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="AB">AB</option>
                                        <option value="O">O</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="edit-section-label"><i class="fas fa-building me-1"></i> Afiliasi & Lokasi</p>
                    <div class="edit-section-card" style="margin-bottom: 16px;">
                        <div class="esc-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Institusi / Perusahaan</label>
                                    <input type="text" name="institusi" id="ek-institusi" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Media Sosial (IG/FB)</label>
                                    <input type="text" name="sosmed" id="ek-sosmed" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Domisili (Kota / Kabupaten)</label>
                                    <input type="text" name="domisili" id="ek-domisili" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Alamat Lengkap</label>
                                    <textarea name="alamat" id="ek-alamat" class="form-control" rows="3" style="resize: none; line-height: 1.5;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-edit-footer">
                    <button type="button" class="btn-edit-batal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-edit-simpan">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- ══════════════════════════════════════
     MODAL KONFIRMASI HAPUS KLIEN
══════════════════════════════════════ --}}
<div class="modal fade" id="modalHapusKlien" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content">
            <div class="modal-hapus-header">
                <div class="modal-hapus-icon"><i class="fas fa-user-times"></i></div>
                <p class="modal-hapus-title">Hapus Data Klien?</p>
            </div>
            <div class="modal-hapus-body">
                <span class="hapus-klien-name" id="hapus-klien-nama">—</span>
                <p class="hapus-warning-text">
                    Data klien ini akan dihapus secara permanen termasuk akun terkait dan <strong>tidak dapat dikembalikan</strong>.
                    Apakah Anda yakin ingin melanjutkan?
                </p>
            </div>
            <div class="modal-hapus-footer">
                <button type="button" class="btn-hapus-batal" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn-hapus-konfirmasi" id="btn-konfirmasi-hapus-klien">
                    <i class="fas fa-trash-alt me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Core JS Files -->
    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Datatables Plugin & Inisialisasi bawaan KaiAdmin -->
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
    $(document).ready(function() {
        $('#add-row').DataTable({
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang cocok ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Data tidak tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)"
            }
        });
    });

    // ── Detail Klien ──
    function openDetailKlien(nama, noHp, email, tglLahir, jk, goldar, institusi, sosmed, domisili, alamat, status) {
        const jkText = jk === 'L' ? 'Laki-laki' : (jk === 'P' ? 'Perempuan' : jk || '-');
        document.getElementById('dk-nama').innerText     = nama;
        document.getElementById('dk-status').innerText   = status;
        document.getElementById('dk-nohp').innerText     = noHp;
        document.getElementById('dk-email').innerText    = email;
        document.getElementById('dk-tgllahir').innerText = tglLahir || '-';
        document.getElementById('dk-jk').innerText       = jkText;
        document.getElementById('dk-goldar').innerText   = goldar || '-';
        document.getElementById('dk-institusi').innerText = institusi;
        document.getElementById('dk-sosmed').innerText   = sosmed;
        document.getElementById('dk-domisili').innerText = domisili;
        document.getElementById('dk-alamat').innerText   = alamat;
        new bootstrap.Modal(document.getElementById('modalDetailKlien')).show();
    }

    // ── Edit Klien ──
    function openEditKlien(id, nama, noHp, email, tglLahir, jk, goldar, institusi, sosmed, domisili, alamat) {
        document.getElementById('ek-nama').value      = nama;
        document.getElementById('ek-nohp').value      = noHp;
        document.getElementById('ek-email').value     = email;
        document.getElementById('ek-tgllahir').value  = tglLahir;
        document.getElementById('ek-jk').value        = jk;
        document.getElementById('ek-goldar').value    = goldar;
        document.getElementById('ek-institusi').value = institusi;
        document.getElementById('ek-sosmed').value    = sosmed;
        document.getElementById('ek-domisili').value  = domisili;
        document.getElementById('ek-alamat').value    = alamat;

        document.getElementById('formEditKlien').action = "{{ url('kelola-klien') }}/" + id;
        new bootstrap.Modal(document.getElementById('modalEditKlien')).show();
    }

    // ── Hapus Klien ──
    var _hapusKlienId = null;
    function openHapusKlien(id, nama) {
        _hapusKlienId = id;
        document.getElementById('hapus-klien-nama').innerText = nama;
        new bootstrap.Modal(document.getElementById('modalHapusKlien')).show();
    }
    document.getElementById('btn-konfirmasi-hapus-klien').addEventListener('click', function () {
        if (_hapusKlienId) {
            bootstrap.Modal.getInstance(document.getElementById('modalHapusKlien')).hide();
            document.getElementById('delete-form-' + _hapusKlienId).submit();
        }
    });
</script>
</body>
</html>