<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Pendaftaran Tes - STIFIn Admin</title>
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

    <style>
    .modal-content .form-control,
    .modal-content .form-select {
        border: 1px solid #ced4da !important;
        color: #495057 !important;
    }
    .modal-content .form-control:focus,
    .modal-content .form-select:focus {
        border-color: #6c757d !important;
        box-shadow: none !important;
    }

    /* ── Modal Detail (konsisten dengan jadwal-tes) ── */
    #modalView .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 80px rgba(0,0,0,0.18);
    }
    #modalView .modal-klien-header {
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
        padding: 28px 32px 24px;
        position: relative;
        overflow: hidden;
    }
    #modalView .modal-klien-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    #modalView .modal-klien-header::after {
        content: '';
        position: absolute;
        bottom: -60px; left: -20px;
        width: 140px; height: 140px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    #modalView .modal-klien-header-inner {
        position: relative; z-index: 2;
        display: flex; align-items: center; justify-content: space-between;
    }
    #modalView .modal-klien-eyebrow {
        font-size: 0.7rem; font-weight: 700; letter-spacing: 1.5px;
        text-transform: uppercase; color: rgb(255, 255, 255); margin-bottom: 4px;
    }
    #modalView .modal-klien-title {
        font-size: 1.2rem; font-weight: 800; color: #fff; margin: 0;
    }
    #modalView .modal-klien-body {
        padding: 0;
        max-height: 72vh;
        overflow-y: auto;
        background: #f4f6fb;
    }
    #modalView .modal-klien-body::-webkit-scrollbar { width: 5px; }
    #modalView .modal-klien-body::-webkit-scrollbar-track { background: #f4f6fb; }
    #modalView .modal-klien-body::-webkit-scrollbar-thumb { background: #c5cae9; border-radius: 10px; }
    #modalView .klien-section-label {
        font-size: 0.68rem; font-weight: 800; letter-spacing: 1.4px;
        text-transform: uppercase; color: #90a4ae;
        padding: 16px 24px 8px;
    }

    /* Info section cards */
    .detail-section-card {
        margin: 0 16px 12px;
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e8eaf6;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .detail-section-card .dsc-body {
        padding: 16px 20px;
    }
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
        font-size: 0.82rem; color: #546e7a; line-height: 1.6;
        white-space: pre-line;
    }
    .komentar-detail-box {
        background: #f8f9ff; border-left: 3px solid #c5cae9;
        border-radius: 0 6px 6px 0; padding: 8px 12px;
        font-size: 0.82rem; color: #546e7a; line-height: 1.6; font-style: italic;
    }
    .komentar-detail-box.empty { color: #b0bec5; }

    /* Pembayaran section */
    .bukti-biaya-section {
        margin: 0 16px 16px;
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e8eaf6;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .bukti-biaya-tabs {
        display: flex;
        border-bottom: 1px solid #e8eaf6;
    }
    .bukti-biaya-tab {
        flex: 1; padding: 12px 16px; text-align: center;
        font-size: 0.78rem; font-weight: 700; letter-spacing: 0.5px;
        text-transform: uppercase; color: #90a4ae;
        border-bottom: 3px solid transparent;
    }
    .bukti-biaya-tab.active { color: #2e7d32; border-bottom-color: #2e7d32; }
    .bukti-biaya-content { padding: 16px 20px; }
    .biaya-breakdown-v2 { width: 100%; }
    .biaya-row-v2 {
        display: flex; justify-content: space-between;
        padding: 7px 0; font-size: 0.84rem; color: #2e7d32;
        border-bottom: 1px dashed #eceff1;
    }
    .biaya-row-v2:last-child { border-bottom: none; }
    .biaya-total-v2 {
        display: flex; justify-content: space-between;
        padding: 10px 0 0; font-size: 0.95rem; font-weight: 800;
        color: #2e7d32; border-top: 2px solid #e8f5e9; margin-top: 4px;
    }
    .wilayah-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 700;
        margin-bottom: 14px;
    }
    .wilayah-dalam { background: #e8f5e9; color: #2e7d32; }
    .wilayah-luar  { background: #e8f5e9; color: #2e7d32; }
    .bukti-thumb-v2 {
        border-radius: 10px; overflow: hidden;
        background: #f4f6f9; border: 1px solid #e0e0e0;
        display: flex; align-items: center; justify-content: center;
        min-height: 160px;
    }
    .bukti-thumb-v2 img {
        max-width: 100%; max-height: 200px; object-fit: contain;
        display: block; cursor: zoom-in;
        transition: transform 0.2s;
    }
    .bukti-thumb-v2 img:hover { transform: scale(1.02); }
    .bukti-empty-v2 {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: 28px; color: #b0bec5;
    }
    .bukti-empty-v2 i { font-size: 2rem; margin-bottom: 8px; }
    .bukti-empty-v2 span { font-size: 0.8rem; text-align: center; }

    #modalView .modal-klien-footer {
        padding: 16px 24px;
        background: #fff;
        border-top: 1px solid #e8eaf6;
        display: flex; justify-content: flex-end;
    }
    #modalView .btn-tutup {
        padding: 10px 28px; border-radius: 10px;
        font-weight: 700; font-size: 0.875rem;
        border: 1.5px solid #dee2e6; background: #fff; color: #495057;
        transition: all 0.2s;
    }
    #modalView .btn-tutup:hover { background: #f8f9fa; border-color: #adb5bd; }

    /* ── Modal Status (konsisten dengan modalHapusJadwal style) ── */
    #modalStatus .modal-content {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    #modalStatus .modal-status-header {
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e1b 100%);
        padding: 28px 28px 20px;
        text-align: center;
    }
    #modalStatus .modal-status-icon {
        width: 64px; height: 64px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 14px;
        backdrop-filter: blur(4px);
    }
    #modalStatus .modal-status-icon i { font-size: 1.8rem; color: #fff; }
    #modalStatus .modal-status-title { color: #fff; font-size: 1.2rem; font-weight: 700; margin: 0 0 4px; }
    #modalStatus .modal-status-subtitle { color: rgb(255, 255, 255); font-size: 0.82rem; margin: 0; }
    #modalStatus .modal-status-body { padding: 24px 28px 8px; }
    #modalStatus .modal-status-footer {
        padding: 16px 28px 24px;
        display: flex; gap: 10px; justify-content: center;
    }
    #modalStatus .btn-status-batal {
        flex: 1; max-width: 140px; padding: 10px 20px; border-radius: 10px;
        font-weight: 600; font-size: 0.875rem; border: 1.5px solid #dee2e6;
        background: #fff; color: #495057; transition: all 0.2s;
    }
    #modalStatus .btn-status-batal:hover { background: #f8f9fa; border-color: #adb5bd; }
    #modalStatus .btn-status-simpan {
        flex: 1; max-width: 180px; padding: 10px 20px; border-radius: 10px;
        font-weight: 600; font-size: 0.875rem; border: none;
        background: linear-gradient(135deg, #2e7d32 0%, #1b5e1b  100%);
        color: #fff; transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    #modalStatus .btn-status-simpan:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(46, 125, 50, 0.4); }
    #modalStatus .btn-status-simpan:active { transform: translateY(0); }
    #modalStatus .status-klien-name {
        display: inline-block;
        background: #fff; border: 1px solid #1b5e1b; border-radius: 8px;
        padding: 6px 14px; font-weight: 600; color: #1b5e1b; font-size: 0.875rem;
        max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        margin-bottom: 16px;
    }
</style>
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
                            <h3 class="fw-bold mb-3">Pendaftaran Tes</h3>
                            <ul class="breadcrumbs mb-3">
                                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#">Pendaftaran Tes</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Daftar Pendaftaran Tes STIFIn</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="add-row" class="display table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>No. HP</th>
                                                    <th>Jadwal Tes</th>
                                                    <th>Lokasi</th>
                                                    <th>Status</th>
                                                    <th>Komentar</th>
                                                    <th class="text-center" style="width: 15%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pendaftaran as $item)
                                                <tr>
                                                    <td>{{ $item->nama_klien }}</td>
                                                    <td>{{ $item->no_hp }}</td>
                                                    <td>
                                                        <span class="d-block fw-semibold">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</span>
                                                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB</small>
                                                    </td>
                                                    <td>
                                                        @if($item->lokasi == 'Home Visit')
                                                            <span class="badge badge-secondary px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                <i class="fas fa-home me-1"></i> Home Visit
                                                            </span>
                                                        @else
                                                            <span class="badge badge-info px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                <i class="fas fa-building me-1"></i> Kantor Subang
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->status == 'Diterima')
                                                            <span class="badge badge-success">Diterima</span>
                                                        @elseif($item->status == 'Ditolak')
                                                            <span class="badge badge-danger">Ditolak</span>
                                                        @else
                                                            <span class="badge badge-warning text-white">Menunggu</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-truncate" style="max-width: 200px;">
                                                        {{ $item->komentar ?? 'Tidak ada komen' }}
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center align-items-center gap-2">

                                                            {{-- Detail --}}
                                                            <button type="button"
                                                                class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center"
                                                                style="background-color: #d1e7dd; border-color: #badbcc; min-width: 56px;"
                                                                title="Lihat Detail"
                                                                onclick="openModalView(
                                                                    '{{ $item->nama_klien }}',
                                                                    '{{ $item->no_hp }}',
                                                                    '{{ $item->email }}',
                                                                    '{{ $item->tanggal_lahir }}',
                                                                    '{{ $item->jenis_kelamin }}',
                                                                    '{{ $item->golongan_darah }}',
                                                                    '{{ $item->institusi ?? '-' }}',
                                                                    '{{ $item->sosmed ?? '-' }}',
                                                                    '{{ $item->domisili ?? '-' }}',
                                                                    '{{ $item->alamat }}',
                                                                    '{{ $item->bukti_transfer ?? '' }}',
                                                                    {{ $item->is_luar_subang ? 'true' : 'false' }},
                                                                    '{{ $item->nama_kota ?? '' }}',
                                                                    {{ json_encode($item->komentar ?? '') }}
                                                                )">
                                                                <i class="fa fa-eye mb-1" style="color: #0f5132;"></i>
                                                                <span style="font-size: 9px; color: #0f5132; font-weight: 600;">Detail</span>
                                                            </button>

                                                            {{-- Edit Status --}}
                                                            <button type="button"
                                                                class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center"
                                                                style="background-color: #cfe2ff; border-color: #b6d4fe; min-width: 56px;"
                                                                title="Edit Status"
                                                                onclick="openModalStatus(
                                                                    '{{ $item->id_jadwal }}',
                                                                    '{{ $item->nama_klien }}',
                                                                    '{{ ($item->status == 'Diterima' || $item->status == 'Ditolak') ? $item->status : 'Menunggu' }}',
                                                                    '{{ $item->komentar ?? '' }}'
                                                                )">
                                                                <i class="fa fa-edit mb-1" style="color: #084298;"></i>
                                                                <span style="font-size: 9px; color: #084298; font-weight: 600;">Status</span>
                                                            </button>

                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted italic">Belum ada data pendaftaran.</td>
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

   {{-- ══════════════════════════════════════════════════
     MODAL DETAIL PROFIL KLIEN (konsisten jadwal-tes)
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalView" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            {{-- Header gradient hijau --}}
            <div class="modal-klien-header">
                <div class="modal-klien-header-inner">
                    <div>
                        <p class="modal-klien-eyebrow"><i class="fas fa-id-badge me-1"></i> Informasi Pendaftar</p>
                        <h5 class="modal-klien-title">Detail Klien</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity:0.8;"></button>
                </div>
            </div>

            {{-- Body --}}
            <div class="modal-klien-body">

                {{-- I. Identitas Utama --}}
                <p class="klien-section-label"><i class="fas fa-user me-1"></i> Identitas Utama</p>
                <div class="detail-section-card">
                    <div class="dsc-body">
                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <label>Nama Lengkap</label>
                                <span id="viewNama">—</span>
                            </div>
                            <div class="detail-info-item">
                                <label>No. HP / WhatsApp</label>
                                <span id="viewNoHp">—</span>
                            </div>
                            <div class="detail-info-item full-width">
                                <label>Alamat Email</label>
                                <span id="viewEmail">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- II. Informasi Personal --}}
                <p class="klien-section-label"><i class="fas fa-info-circle me-1"></i> Informasi Personal</p>
                <div class="detail-section-card">
                    <div class="dsc-body">
                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <label>Tanggal Lahir</label>
                                <span id="viewTglLahir">—</span>
                            </div>
                            <div class="detail-info-item">
                                <label>Gol. Darah / Jenis Kelamin</label>
                                <span id="viewGoldarJK">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- III. Institusi & Sosmed --}}
                <p class="klien-section-label"><i class="fas fa-building me-1"></i> Institusi & Media Sosial</p>
                <div class="detail-section-card">
                    <div class="dsc-body">
                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <label>Institusi / Perusahaan</label>
                                <span id="viewInstitusi">—</span>
                            </div>
                            <div class="detail-info-item">
                                <label>Media Sosial</label>
                                <span id="viewSosmed">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- IV. Lokasi --}}
                <p class="klien-section-label"><i class="fas fa-map-marker-alt me-1"></i> Lokasi Tempat Tinggal</p>
                <div class="detail-section-card">
                    <div class="dsc-body">
                        <div class="detail-info-grid">
                            <div class="detail-info-item">
                                <label>Domisili</label>
                                <span id="viewDomisili">—</span>
                            </div>
                            <div class="detail-info-item full-width">
                                <label>Alamat Lengkap</label>
                                <div id="viewAlamat" class="detail-alamat-box"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- V. Pembayaran & Bukti --}}
                <p class="klien-section-label"><i class="fas fa-file-invoice-dollar me-1"></i> Pembayaran & Bukti Transfer</p>
                <div class="bukti-biaya-section">
                    <div class="bukti-biaya-tabs">
                        <div class="bukti-biaya-tab active"><i class="fas fa-coins me-1"></i> Rincian Biaya</div>
                        <div class="bukti-biaya-tab active" style="border-left: 1px solid #e8eaf6;"><i class="fas fa-receipt me-1"></i> Bukti Transfer</div>
                    </div>
                    <div class="bukti-biaya-content">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <div id="viewWilayahWrap" class="mb-2"></div>
                                <div id="viewBiayaBreakdown"></div>
                            </div>
                            <div class="col-md-7">
                                <div id="viewBuktiWrapper" class="bukti-thumb-v2"></div>
                                <div id="viewBuktiBtnWrap" class="mt-2 d-none">
                                    <a id="viewBuktiLink" href="#" target="_blank" class="btn btn-sm btn-outline-primary btn-round">
                                        <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- VI. Komentar --}}
                <p class="klien-section-label"><i class="fas fa-comment-alt me-1"></i> Komentar / Catatan Admin</p>
                <div class="detail-section-card" style="margin-bottom: 16px;">
                    <div class="dsc-body">
                        <div id="viewKomentar" class="komentar-detail-box"></div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="modal-klien-footer">
                <button type="button" class="btn-tutup" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tutup
                </button>
            </div>

        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════
     MODAL EDIT STATUS (konsisten dengan modalHapus)
══════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">

            <div class="modal-status-header">
                <div class="modal-status-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <p class="modal-status-title">Perbarui Status Pendaftaran</p>
                <p class="modal-status-subtitle">Ubah status dan tambahkan catatan untuk klien</p>
            </div>

            <form id="formStatus" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-status-body">
                    <div class="text-center mb-1">
                        <span class="status-klien-name" id="modalNama">—</span>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Pilih Status</label>
                        <select name="status" id="inputStatus" class="form-control form-select" required>
                            <option value="Menunggu">Menunggu</option>
                            <option value="Diterima">Diterima</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div class="form-group mb-2">
                        <label class="text-muted small fw-semibold mb-1 text-uppercase" style="font-size:0.7rem; letter-spacing:1px;">Komentar / Catatan</label>
                        <textarea name="komentar" id="inputKomentar" class="form-control" rows="4"
                            style="resize: none; line-height: 1.5; border-radius: 10px;"
                            placeholder="Alasan ditolak atau informasi tambahan jadwal..."></textarea>
                    </div>
                </div>

                <div class="modal-status-footer">
                    <button type="button" class="btn-status-batal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-status-simpan">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
    $(document).ready(function () {
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

    // ── Konstanta biaya ──
    const BIAYA_TES            = 550000;
    const BIAYA_TRANSPORT_LUAR = 75000;
    const TRANSPORT_DALAM = {
        'Kota Subang': 25000,
        'Kab. Subang':  50000,
    };

    function formatRupiah(nominal) {
        return 'Rp ' + parseInt(nominal).toLocaleString('id-ID');
    }

    function openModalView(
        nama, noHp, email, tglLahir, jk, goldar,
        institusi, sosmed, domisili, alamat,
        buktiTransfer, isLuarSubang, namaKota, komentar
    ) {
        const jkText = jk === 'L' ? 'Laki-laki' : (jk === 'P' ? 'Perempuan' : jk);

        document.getElementById('viewNama').innerText      = nama;
        document.getElementById('viewNoHp').innerText      = noHp;
        document.getElementById('viewEmail').innerText     = email;
        document.getElementById('viewTglLahir').innerText  = tglLahir || '-';
        document.getElementById('viewGoldarJK').innerText  = goldar + ' / ' + jkText;
        document.getElementById('viewInstitusi').innerText = institusi;
        document.getElementById('viewSosmed').innerText    = sosmed;
        document.getElementById('viewDomisili').innerText  = domisili;
        document.getElementById('viewAlamat').innerText    = alamat;

        // Komentar
        const komentarEl = document.getElementById('viewKomentar');
        if (komentar && komentar.trim() !== '') {
            komentarEl.innerText = komentar;
            komentarEl.classList.remove('empty');
        } else {
            komentarEl.innerText = 'Belum ada komentar dari admin.';
            komentarEl.classList.add('empty');
        }

        // Biaya
        const kotaBersih = (namaKota || '').trim();
        let transport = 0;
        let wilayahHTML = '';
        let areaLabel = '';

        if (!isLuarSubang) {
            transport   = TRANSPORT_DALAM[kotaBersih] !== undefined ? TRANSPORT_DALAM[kotaBersih] : 0;
            wilayahHTML = '<span class="wilayah-pill wilayah-dalam"><i class="fas fa-map-marker-alt"></i>Dalam Subang</span>';
            if (kotaBersih !== '' && TRANSPORT_DALAM[kotaBersih] !== undefined) areaLabel = kotaBersih;
        } else {
            transport   = BIAYA_TRANSPORT_LUAR;
            wilayahHTML = '<span class="wilayah-pill wilayah-luar"><i class="fas fa-road"></i>Luar Subang</span>';
        }

        const total = BIAYA_TES + transport;
        document.getElementById('viewWilayahWrap').innerHTML = wilayahHTML;
        document.getElementById('viewBiayaBreakdown').innerHTML =
            '<table class="biaya-breakdown-v2">' +
            '<tr class="biaya-row-v2"><td>Biaya Tes</td><td class="text-end fw-semibold">' + formatRupiah(BIAYA_TES) + '</td></tr>' +
            '<tr class="biaya-row-v2"><td>Biaya Transport' + (areaLabel ? ' <small class="text-muted">(' + areaLabel + ')</small>' : '') + '</td><td class="text-end fw-semibold">' + formatRupiah(transport) + '</td></tr>' +
            '</table>' +
            '<div class="biaya-total-v2"><span>Total Pembayaran</span><span>' + formatRupiah(total) + '</span></div>';

        // Bukti transfer
        const imgWrap  = document.getElementById('viewBuktiWrapper');
        const btnWrap  = document.getElementById('viewBuktiBtnWrap');
        const btnLink  = document.getElementById('viewBuktiLink');

        if (buktiTransfer && buktiTransfer.trim() !== '') {
            const imgUrl = '/uploads/bukti/' + buktiTransfer;
            imgWrap.innerHTML = '<img src="' + imgUrl + '" alt="Bukti Transfer"' +
                ' onclick="window.open(\'' + imgUrl + '\', \'_blank\')"' +
                ' onerror="this.parentElement.innerHTML=\'<div class=\\\'bukti-empty-v2\\\'><i class=\\\'fas fa-image\\\'></i><span>Gambar tidak dapat dimuat</span></div>\'">';
            btnLink.href = imgUrl;
            btnWrap.classList.remove('d-none');
        } else {
            imgWrap.innerHTML =
                '<div class="bukti-empty-v2">' +
                '<i class="fas fa-file-image"></i>' +
                '<span>Belum ada bukti transfer yang diupload</span>' +
                '</div>';
            btnWrap.classList.add('d-none');
        }

        new bootstrap.Modal(document.getElementById('modalView')).show();
    }

    function openModalStatus(id, nama, status, komentar) {
        document.getElementById('modalNama').innerText    = nama;
        document.getElementById('inputStatus').value     = status;
        document.getElementById('inputKomentar').value   = komentar;

        const baseUrl = "{{ url('pendaftaran-tes') }}";
        document.getElementById('formStatus').action = baseUrl + '/' + id;

        new bootstrap.Modal(document.getElementById('modalStatus')).show();
    }
</script>
</body>
</html>