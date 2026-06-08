<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Hasil Tes - STIFIn Admin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('assets/img/kaiadmin/favicon.ico') }}" type="image/x-icon" />

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

    <style>
        /* ═══════════════════════════════════════════
           VARIABEL & BASE — skema hijau (konsisten dengan pendaftaran-tes)
        ═══════════════════════════════════════════ */
        :root {
            --green-dark  : #1b5e20;
            --green-mid   : #2e7d32;
            --green-light : #43a047;
            --green-pale  : #e8f5e9;
            --green-border: #a5d6a7;
            --red-dark    : #c0392b;
            --red-mid     : #e53935;
            --radius-lg   : 20px;
            --radius-md   : 14px;
            --radius-sm   : 10px;
        }

        /* ═══════════════════════════════════════════
           MODAL SHARED — Input, Edit, & Preview
        ═══════════════════════════════════════════ */
        .modal-hasil .modal-content,
        #modalPreview .modal-content {
            border        : none;
            border-radius : var(--radius-lg);
            overflow      : hidden;
            box-shadow    : 0 24px 80px rgba(0,0,0,.18);
        }

        /* ── Header hijau (Input & Edit) — konsisten dengan pendaftaran-tes ── */
        .modal-hasil .modal-header {
            background : linear-gradient(135deg, var(--green-mid) 0%, var(--green-dark) 100%);
            padding    : 28px 32px 24px;
            border     : none;
            position   : relative;
            overflow   : hidden;
        }
        /* Dekorasi lingkaran seperti pendaftaran-tes */
        .modal-hasil .modal-header::before {
            content      : '';
            position     : absolute;
            top: -40px; right: -40px;
            width: 180px; height: 180px;
            background   : rgba(255,255,255,.06);
            border-radius: 50%;
        }
        .modal-hasil .modal-header::after {
            content      : '';
            position     : absolute;
            bottom: -60px; left: -20px;
            width: 140px; height: 140px;
            background   : rgba(255,255,255,.04);
            border-radius: 50%;
        }
        .modal-hasil .modal-header .modal-title {
            color      : #fff;
            font-size  : 1rem;
            font-weight: 800;
            display    : flex;
            align-items: center;
            gap        : 8px;
            position   : relative;
            z-index    : 2;
        }
        .modal-hasil .modal-header .modal-title i {
            font-size      : .95rem;
            background     : rgba(255,255,255,.18);
            width          : 32px;
            height         : 32px;
            border-radius  : 50%;
            display        : flex;
            align-items    : center;
            justify-content: center;
            flex-shrink    : 0;
            backdrop-filter: blur(4px);
        }
        .modal-hasil .modal-header .modal-title .nama-klien {
            opacity    : .85;
            font-weight: 500;
            font-size  : .875rem;
        }
        .modal-hasil .modal-header .btn-close {
            filter  : invert(1);
            opacity : .8;
            position: relative;
            z-index : 2;
        }
        .modal-hasil .modal-header .btn-close:hover { opacity: 1; }

        /* ── Eyebrow label di header ── */
        .modal-hasil .modal-header-eyebrow {
            font-size     : 0.7rem;
            font-weight   : 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color         : rgba(255,255,255,.8);
            margin-bottom : 4px;
            position      : relative;
            z-index       : 2;
        }
        .modal-hasil .modal-header-inner {
            display        : flex;
            align-items    : center;
            justify-content: space-between;
            position       : relative;
            z-index        : 2;
        }

        /* ── Body ── */
        .modal-hasil .modal-body {
            background : #f4f6fb;
            padding    : 22px 24px 8px;
            max-height : 70vh;
            overflow-y : auto;
            overflow-x : hidden;
        }
        .modal-hasil .modal-body::-webkit-scrollbar { width: 5px; }
        .modal-hasil .modal-body::-webkit-scrollbar-track { background: #f4f6fb; }
        .modal-hasil .modal-body::-webkit-scrollbar-thumb { background: #a5d6a7; border-radius: 10px; }

        /* ── Label & input ── */
        .modal-hasil .field-label {
            font-size     : .7rem;
            font-weight   : 800;
            color         : #90a4ae;
            margin-bottom : 5px;
            display       : block;
            letter-spacing: .8px;
            text-transform: uppercase;
        }
        .modal-hasil .form-control,
        .modal-hasil .form-select {
            border       : 1.5px solid #dde1e8 !important;
            border-radius: var(--radius-sm) !important;
            font-size    : .875rem !important;
            color        : #37474f !important;
            background   : #fff !important;
            transition   : border-color .2s, box-shadow .2s !important;
            padding      : 9px 13px !important;
        }
        .modal-hasil .form-control:focus,
        .modal-hasil .form-select:focus {
            border-color: var(--green-mid) !important;
            box-shadow  : 0 0 0 3px rgba(46,125,50,.12) !important;
            outline     : none !important;
        }

        /* ── Upload hint ── */
        .upload-hint {
            font-size : .72rem;
            color     : #90a4ae;
            margin-top: 4px;
        }

        /* ── Footer ── */
        .modal-hasil .modal-footer {
            background : #fff;
            border-top : 1px solid #e8eaf6;
            padding    : 16px 24px;
            gap        : 10px;
        }

        /* ── Seksi dalam form ── */
        .form-section {
            background   : #fff;
            border-radius: var(--radius-sm);
            padding      : 16px;
            border       : 1px solid #e8eaf6;
            margin-bottom: 14px;
            box-shadow   : 0 2px 10px rgba(0,0,0,.04);
        }
        .form-section:last-child { margin-bottom: 0; }
        .form-section-title {
            font-size     : .68rem;
            font-weight   : 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color         : #90a4ae;
            margin-bottom : 12px;
            padding-bottom: 8px;
            border-bottom : 1px solid #f0f2f5;
        }

        /* ── Info box (alert) dalam modal ── */
        .modal-info-box {
            background   : #e8f5e9;
            border       : 1px solid #a5d6a7;
            border-radius: var(--radius-sm);
            padding      : 10px 14px;
            font-size    : .8rem;
            color        : #1b5e20;
            margin-bottom: 14px;
            display      : flex;
            align-items  : flex-start;
            gap          : 8px;
        }
        .modal-info-box i { margin-top: 1px; flex-shrink: 0; }

        /* ═══════════════════════════════════════════
           TOMBOL SHARED (batal & simpan)
        ═══════════════════════════════════════════ */
        .btn-modal-batal {
            padding      : 10px 28px;
            border-radius: var(--radius-sm);
            font-weight  : 700;
            font-size    : .875rem;
            border       : 1.5px solid #dee2e6;
            background   : #fff;
            color        : #495057;
            transition   : all .2s;
        }
        .btn-modal-batal:hover {
            background  : #f8f9fa;
            border-color: #adb5bd;
            color       : #3b3f4a;
        }
        .btn-modal-simpan {
            padding      : 10px 28px;
            border-radius: var(--radius-sm);
            font-weight  : 700;
            font-size    : .875rem;
            border       : none;
            background   : linear-gradient(135deg, var(--green-mid) 0%, var(--green-dark) 100%);
            color        : #fff;
            transition   : all .2s;
            box-shadow   : 0 4px 12px rgba(46,125,50,.3);
            display      : flex;
            align-items  : center;
            gap          : 6px;
        }
        .btn-modal-simpan:hover  {
            transform : translateY(-1px);
            box-shadow: 0 6px 18px rgba(46,125,50,.4);
        }
        .btn-modal-simpan:active { transform: translateY(0); }

        /* ═══════════════════════════════════════════
           MODAL PREVIEW
        ═══════════════════════════════════════════ */
        #modalPreview .modal-header {
            background   : #fff;
            border-bottom: 1px solid #e8eaf6;
            padding      : 14px 20px;
        }
        #modalPreview .modal-title { font-size: .95rem; font-weight: 700; color: #2a2b2d; }
        #modalPreview .modal-footer {
            background: #f4f6fb;
            border-top: 1px solid #e8eaf6;
            padding   : 12px 20px;
        }

        /* ── Tombol aksi mini di tabel riwayat ── */
        .btn-aksi {
            display        : flex;
            flex-direction : column;
            align-items    : center;
            justify-content: center;
            gap            : 3px;
            border-radius  : var(--radius-sm);
            border         : 1px solid transparent;
            padding        : 7px 10px;
            min-width      : 56px;
            font-size      : .7rem;
            font-weight    : 600;
            transition     : all .2s;
            box-shadow     : 0 1px 4px rgba(0,0,0,.07);
            cursor         : pointer;
        }
        .btn-aksi i { font-size: .85rem; }
        .btn-aksi:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,.12); }
        .btn-aksi:active { transform: translateY(0); }

        .btn-aksi-sertifikat {
            background  : #fff8e1;
            border-color: #ffe082;
            color       : #7a5c00;
        }
        .btn-aksi-sertifikat i { color: #f59e0b; }

        /* Warna tombol detail diubah ke hijau, konsisten dengan pendaftaran-tes */
        .btn-aksi-detail {
            background  : #d1e7dd;
            border-color: #badbcc;
            color       : #0f5132;
        }
        .btn-aksi-detail i { color: #0f5132; }

        /* Warna tombol edit disesuaikan ke biru muda (konsisten dengan pendaftaran-tes) */
        .btn-aksi-edit {
            background  : #cfe2ff;
            border-color: #b6d4fe;
            color       : #084298;
        }
        .btn-aksi-edit i { color: #084298; }

        /* ── File preview saat ini (dalam modal Edit) ── */
        .current-files-box {
            background   : #f4f6fb;
            border       : 1px solid #e8eaf6;
            border-radius: var(--radius-sm);
            padding      : 12px 14px;
            margin-bottom: 0;
        }
        .current-files-label {
            font-size     : .68rem;
            font-weight   : 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color         : #90a4ae;
            margin-bottom : 10px;
        }

        /* ── Nama klien pill di modal Edit — konsisten dengan pendaftaran-tes ── */
        .modal-klien-name-pill {
            display      : inline-block;
            background   : #fff;
            border       : 1px solid var(--green-dark);
            border-radius: 8px;
            padding      : 6px 14px;
            font-weight  : 600;
            color        : var(--green-dark);
            font-size    : .875rem;
            max-width    : 100%;
            overflow     : hidden;
            text-overflow: ellipsis;
            white-space  : nowrap;
            margin-bottom: 4px;
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

                    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h3 class="fw-bold mb-1">Manajemen Hasil Tes</h3>
                            <ul class="breadcrumbs mb-0 p-0" style="background: transparent;">
                                <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="{{ route('hasil-tes') }}" class="text-muted">Hasil Tes</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show card-round shadow-sm mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show card-round shadow-sm mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Tab navigasi --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <ul class="nav nav-tabs nav-line nav-color-primary border-bottom" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4 fw-bold {{ $tab == 'kelola' ? 'active' : '' }}" href="?tab=kelola">
                                        <i class="fas fa-tasks me-2"></i> Kelola Hasil Tes
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4 fw-bold {{ $tab == 'riwayat' ? 'active' : '' }}" href="?tab=riwayat">
                                        <i class="fas fa-history me-2"></i> Riwayat Tes
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round shadow-sm">
                                <div class="card-body p-4">
                                    <div class="table-responsive">

                                        @if($tab == 'kelola')
                                            <table id="basic-datatables" class="table table-striped table-hover mb-0 align-middle">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8" style="width: 40%">Nama Klien</th>
                                                        <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8" style="width: 30%">Status</th>
                                                        <th class="px-4 py-3 text-center text-uppercase font-weight-bold text-muted fs-8" style="width: 30%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($data as $item)
                                                        <tr>
                                                            <td class="px-4 py-3">
                                                                <div class="fw-bold text-dark fs-6">{{ $item->nama }}</div>
                                                                <small class="text-muted"><i class="fab fa-whatsapp me-1"></i> {{ $item->no_hp }}</small>
                                                            </td>
                                                            <td class="px-4 py-3">
                                                                <span class="badge badge-primary px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                    Belum diinput
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                <button type="button"
                                                                    onclick="openModal('{{ $item->id_tes }}', '{{ $item->id_jadwal }}', '{{ $item->nama }}')"
                                                                    class="btn btn-sm p-2 card-round shadow-sm d-flex flex-column align-items-center mx-auto"
                                                                    style="background-color: #d1e7dd; border-color: #badbcc; min-width: 70px;">
                                                                    <i class="fas fa-check-circle mb-1" style="color: #0f5132;"></i>
                                                                    <span style="font-size: 9px; color: #0f5132; font-weight: 600;">Input Hasil</span>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center py-4 text-muted">
                                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                                Tidak ada data yang perlu dikelola.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>

                                        @else
                                            {{-- Riwayat Tes --}}
                                            <table id="basic-datatables-riwayat" class="table table-striped table-hover mb-0 align-middle">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8" style="width: 35%">Nama Klien</th>
                                                        <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8" style="width: 20%">Hasil STIFIn</th>
                                                        <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8" style="width: 15%">Status</th>
                                                        <th class="px-4 py-3 text-center text-uppercase font-weight-bold text-muted fs-8" style="width: 30%">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($data as $item)
                                                        <tr>
                                                            <td class="px-4 py-3">
                                                                <div class="fw-bold text-dark fs-6">{{ $item->nama }}</div>
                                                                <small class="text-muted"><i class="fab fa-whatsapp me-1"></i> {{ $item->no_hp }}</small>
                                                            </td>
                                                            <td class="px-4 py-3">
                                                                @if($item->hasil_tes)
                                                                    <span class="badge badge-info px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                        <i class="fas fa-brain me-1"></i> {{ $item->hasil_tes }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted fs-8 fst-italic">-</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-3">
                                                                <span class="badge badge-success px-3 py-1 btn-round text-capitalize fs-8 fw-normal">
                                                                    <i class="fas fa-check-circle me-1"></i> Tersertifikasi
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                <div class="d-flex justify-content-center align-items-center gap-2">

                                                                    <button type="button"
                                                                            onclick="previewFile('{{ asset('uploads/hasil/' . $item->file_hasil) }}')"
                                                                            class="btn-aksi btn-aksi-sertifikat"
                                                                            title="Lihat Sertifikat">
                                                                        <i class="fas fa-file-pdf"></i>
                                                                        Sertifikat
                                                                    </button>

                                                                    <button type="button"
                                                                            onclick="previewFile('{{ asset('uploads/hasil/' . $item->file_detail) }}')"
                                                                            class="btn-aksi btn-aksi-detail"
                                                                            title="Lihat Detail">
                                                                        <i class="fas fa-file-alt"></i>
                                                                        Detail
                                                                    </button>

                                                                    <button type="button"
                                                                            onclick="openEditModal('{{ $item->id_tes }}', '{{ $item->nama }}', '{{ $item->file_hasil }}', '{{ $item->file_detail }}', '{{ $item->hasil_tes }}')"
                                                                            class="btn-aksi btn-aksi-edit"
                                                                            title="Edit">
                                                                        <i class="fas fa-pencil-alt"></i>
                                                                        Edit
                                                                    </button>

                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center py-4 text-muted">
                                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                                Belum ada riwayat tes yang selesai.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        @endif

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


    {{-- ══════════════════════════════════════════════
         MODAL INPUT HASIL TES
    ══════════════════════════════════════════════ --}}
    <div class="modal fade modal-hasil" id="modalHasil" tabindex="-1"
         aria-labelledby="modalHasilLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
            <div class="modal-content">

                <div class="modal-header">
                    <div class="modal-header-inner w-100">
                        <div>
                            <p class="modal-header-eyebrow"><i class="fas fa-id-badge me-1"></i> Input Hasil Tes</p>
                            <h5 class="modal-title" id="modalHasilLabel">
                                <i class="fas fa-check-circle"></i>
                                <span id="modalNama"></span>
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                </div>

                <form id="formHasil" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="id_jadwal" id="hiddenIdJadwal">

                    <div class="modal-body">

                        {{-- Seksi: Hasil STIFIn --}}
                        <div class="form-section">
                            <p class="form-section-title"><i class="fas fa-brain me-1"></i> Hasil STIFIn</p>
                            <label class="field-label" for="inputHasilTes">Hasil Tes</label>
                            <select name="hasil_tes" id="inputHasilTes" class="form-select form-control" required>
                                <option value="" disabled selected>— Pilih hasil STIFIn —</option>
                                <optgroup label="Feeling">
                                    <option value="Fe">Fe – Feeling Extrovert</option>
                                    <option value="Fi">Fi – Feeling Introvert</option>
                                </optgroup>
                                <optgroup label="Thinking">
                                    <option value="Te">Te – Thinking Extrovert</option>
                                    <option value="Ti">Ti – Thinking Introvert</option>
                                </optgroup>
                                <optgroup label="Sensing">
                                    <option value="Se">Se – Sensing Extrovert</option>
                                    <option value="Si">Si – Sensing Introvert</option>
                                </optgroup>
                                <optgroup label="iNtuiting">
                                    <option value="Ne">Ne – iNtuiting Extrovert</option>
                                    <option value="Ni">Ni – iNtuiting Introvert</option>
                                </optgroup>
                                <optgroup label="Instinct">
                                    <option value="I">I – Instinct</option>
                                </optgroup>
                            </select>
                        </div>

                        {{-- Seksi: Berkas --}}
                        <div class="form-section">
                            <p class="form-section-title"><i class="fas fa-paperclip me-1"></i> Berkas Hasil</p>

                            <div class="mb-3">
                                <label class="field-label" for="inputFileSertifikat">
                                    <i class="fas fa-certificate text-warning me-1"></i> Sertifikat
                                </label>
                                <input type="file" name="file_hasil" id="inputFileSertifikat"
                                       class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                <p class="upload-hint"><i class="fa fa-info-circle me-1"></i>Format: PDF / JPG / PNG</p>
                            </div>

                            <div class="mb-0">
                                <label class="field-label" for="inputFileDetail">
                                    <i class="fas fa-file-alt me-1" style="color: var(--green-mid)"></i> Hasil Tes
                                </label>
                                <input type="file" name="file_detail" id="inputFileDetail"
                                       class="form-control" accept=".pdf,.doc,.docx" required>
                                <p class="upload-hint"><i class="fa fa-info-circle me-1"></i>Format: PDF / DOC / DOCX</p>
                            </div>
                        </div>

                    </div>{{-- /modal-body --}}

                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn-modal-batal" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-modal-simpan">
                            <i class="fas fa-save"></i> Simpan & Selesaikan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════
         MODAL EDIT DATA HASIL
    ══════════════════════════════════════════════ --}}
    <div class="modal fade modal-hasil" id="modalEdit" tabindex="-1"
         aria-labelledby="modalEditLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
            <div class="modal-content">

                <div class="modal-header">
                    <div class="modal-header-inner w-100">
                        <div>
                            <p class="modal-header-eyebrow"><i class="fas fa-pencil-alt me-1"></i> Edit Data Hasil Tes</p>
                            <h5 class="modal-title" id="modalEditLabel">
                                <i class="fas fa-pencil-alt"></i>
                                <span id="editNama"></span>
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                </div>

                <form id="formEdit" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        {{-- Info hint --}}
                        <div class="modal-info-box">
                            <i class="fas fa-info-circle"></i>
                            <span>Kosongkan data yang tidak ingin diganti. Perubahan hanya diterapkan pada data yang diisi.</span>
                        </div>

                        {{-- Seksi: File tersimpan saat ini --}}
                        <div class="form-section">
                            <p class="form-section-title"><i class="fas fa-folder-open me-1"></i> File Tersimpan Saat Ini</p>
                            <div class="d-flex gap-2">
                                <button type="button" id="btnPreviewSertifikat"
                                        onclick="previewFileFromEdit('sertifikat')"
                                        class="btn-aksi btn-aksi-sertifikat">
                                    <i class="fas fa-eye"></i>
                                    Sertifikat
                                </button>
                                <button type="button" id="btnPreviewDetail"
                                        onclick="previewFileFromEdit('detail')"
                                        class="btn-aksi btn-aksi-detail">
                                    <i class="fas fa-eye"></i>
                                    Detail
                                </button>
                            </div>
                        </div>

                        {{-- Seksi: Ubah Hasil STIFIn --}}
                        <div class="form-section">
                            <p class="form-section-title"><i class="fas fa-brain me-1"></i> Ubah Hasil STIFIn <span style="font-weight:400; text-transform:none; letter-spacing:0;">(opsional)</span></p>
                            <label class="field-label" for="editHasilTes">Hasil Tes</label>
                            <select name="hasil_tes" id="editHasilTes" class="form-select form-control">
                                <option value="">— Biarkan tetap —</option>
                                <optgroup label="Feeling">
                                    <option value="Fe">Fe – Feeling Extrovert</option>
                                    <option value="Fi">Fi – Feeling Introvert</option>
                                </optgroup>
                                <optgroup label="Thinking">
                                    <option value="Te">Te – Thinking Extrovert</option>
                                    <option value="Ti">Ti – Thinking Introvert</option>
                                </optgroup>
                                <optgroup label="Sensing">
                                    <option value="Se">Se – Sensing Extrovert</option>
                                    <option value="Si">Si – Sensing Introvert</option>
                                </optgroup>
                                <optgroup label="iNtuiting">
                                    <option value="Ne">Ne – iNtuiting Extrovert</option>
                                    <option value="Ni">Ni – iNtuiting Introvert</option>
                                </optgroup>
                                <optgroup label="Instinct">
                                    <option value="I">I – Instinct</option>
                                </optgroup>
                            </select>
                        </div>

                        {{-- Seksi: Ganti Berkas --}}
                        <div class="form-section">
                            <p class="form-section-title"><i class="fas fa-paperclip me-1"></i> Ganti Berkas <span style="font-weight:400; text-transform:none; letter-spacing:0;">(opsional)</span></p>

                            <div class="mb-3">
                                <label class="field-label">
                                    <i class="fas fa-certificate text-warning me-1"></i> Sertifikat Baru
                                </label>
                                <input type="file" name="file_hasil" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <p class="upload-hint"><i class="fa fa-info-circle me-1"></i>Format: PDF / JPG / PNG</p>
                            </div>

                            <div class="mb-0">
                                <label class="field-label">
                                    <i class="fas fa-file-alt me-1" style="color: var(--green-mid)"></i> Hasil Tes Lengkap Baru
                                </label>
                                <input type="file" name="file_detail" class="form-control" accept=".pdf,.doc,.docx">
                                <p class="upload-hint"><i class="fa fa-info-circle me-1"></i>Format: PDF / DOC / DOCX</p>
                            </div>
                        </div>

                    </div>{{-- /modal-body --}}

                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn-modal-batal" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-modal-simpan">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════
         MODAL PREVIEW BERKAS
    ══════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalPreview" tabindex="-1"
         aria-labelledby="modalPreviewLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content" style="max-height: 95vh;">

                <div class="modal-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 rounded" style="background: #e8f5e9;">
                            <i class="fas fa-file-alt" style="color: var(--green-mid);"></i>
                        </div>
                        <h5 class="modal-title" id="modalPreviewLabel">Preview Berkas Hasil Tes</h5>
                    </div>
                    <button type="button" class="btn-close" onclick="closePreview()" aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-0 bg-light" style="height: 65vh; min-height: 500px;">
                    <iframe id="previewFrame" src="" class="w-100 h-100 border-0 m-0 p-0"></iframe>
                </div>

                <div class="modal-footer justify-content-between">
                    <span class="text-muted small fst-italic">
                        <i class="fas fa-info-circle me-1"></i>Gunakan tombol unduh di tabel jika ingin menyimpan file.
                    </span>
                    <button type="button" onclick="closePreview()" class="btn-modal-batal">
                        <i class="fas fa-times me-1"></i> Tutup
                    </button>
                </div>

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
        @if($tab == 'kelola')
            $('#basic-datatables').DataTable({
                pageLength: 10,
                order: [],
                language: {
                    search       : "Cari Data:",
                    lengthMenu   : "Tampilkan _MENU_ data",
                    zeroRecords  : "Tidak ada data yang cocok",
                    info         : "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty    : "Data tidak tersedia",
                    infoFiltered : "(difilter dari _MAX_ total data)"
                }
            });
        @endif

        @if($tab == 'riwayat')
            $('#basic-datatables-riwayat').DataTable({
                pageLength: 10,
                order: [],
                language: {
                    search       : "Cari Data:",
                    lengthMenu   : "Tampilkan _MENU_ data",
                    zeroRecords  : "Tidak ada data yang cocok",
                    info         : "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty    : "Data tidak tersedia",
                    infoFiltered : "(difilter dari _MAX_ total data)"
                }
            });
        @endif
    });

    /* ── Buka modal Input Hasil ── */
    function openModal(idTes, idJadwal, nama) {
        document.getElementById('modalNama').innerText = nama;
        document.getElementById('hiddenIdJadwal').value = idJadwal;

        let url = "{{ route('hasil.update', ':id') }}".replace(':id', idTes);
        document.getElementById('formHasil').action = url;

        // Reset form
        document.getElementById('inputHasilTes').value = '';
        document.getElementById('inputFileSertifikat').value = '';
        document.getElementById('inputFileDetail').value = '';

        new bootstrap.Modal(document.getElementById('modalHasil')).show();
    }

    /* ── Preview berkas ── */
    function previewFile(url) {
        document.getElementById('previewFrame').src = url;
        new bootstrap.Modal(document.getElementById('modalPreview')).show();
    }

    function closePreview() {
        const modalEl = document.getElementById('modalPreview');
        const instance = bootstrap.Modal.getInstance(modalEl);
        if (instance) instance.hide();
        document.getElementById('previewFrame').src = '';
    }

    /* ── State file edit ── */
    let _currentFileHasil  = '';
    let _currentFileDetail = '';

    /* ── Buka modal Edit ── */
    function openEditModal(idTes, nama, fileHasil, fileDetail, hasilTes) {
        document.getElementById('editNama').innerText = nama;

        const base = "{{ asset('uploads/hasil') }}/";
        _currentFileHasil  = (fileHasil  && fileHasil  !== 'null' && fileHasil.trim()  !== '') ? base + fileHasil  : '';
        _currentFileDetail = (fileDetail && fileDetail !== 'null' && fileDetail.trim() !== '') ? base + fileDetail : '';

        document.getElementById('btnPreviewSertifikat').style.display = _currentFileHasil  ? '' : 'none';
        document.getElementById('btnPreviewDetail').style.display     = _currentFileDetail ? '' : 'none';

        const select = document.getElementById('editHasilTes');
        select.value = (hasilTes && hasilTes !== 'null') ? hasilTes : '';

        let url = "{{ route('hasil.edit', ':id') }}".replace(':id', idTes);
        document.getElementById('formEdit').action = url;

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    /* ── Preview dari dalam modal Edit (tutup edit → buka preview) ── */
    function previewFileFromEdit(tipe) {
        const url = tipe === 'sertifikat' ? _currentFileHasil : _currentFileDetail;
        if (!url) return;

        const modalEditEl = document.getElementById('modalEdit');
        const modalEditInstance = bootstrap.Modal.getInstance(modalEditEl);
        if (modalEditInstance) modalEditInstance.hide();

        modalEditEl.addEventListener('hidden.bs.modal', function reopenAfterClose() {
            modalEditEl.removeEventListener('hidden.bs.modal', reopenAfterClose);
            previewFile(url);
        });
    }
    </script>
</body>
</html>