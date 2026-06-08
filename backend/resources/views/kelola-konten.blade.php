<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Kelola Konten Informasi STIFIn - Admin</title>
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
           VARIABEL & BASE
        ═══════════════════════════════════════════ */
        :root {
            --green-dark : #1b5e20;
            --green-mid  : #2e7d32;
            --green-light: #43a047;
            --red-dark   : #c0392b;
            --red-mid    : #e53935;
            --shadow-card: 0 4px 24px rgba(0,0,0,.08);
            --radius-lg  : 16px;
            --radius-md  : 10px;
        }

        /* ═══════════════════════════════════════════
           MODAL SHARED — konten & hapus
        ═══════════════════════════════════════════ */
        .modal-konten .modal-content,
        #modalHapusKonten .modal-content {
            border        : none;
            border-radius : var(--radius-lg);
            overflow      : hidden;
            box-shadow    : 0 20px 60px rgba(0,0,0,.16);
        }

        /* ── Header hijau (Tambah & Edit) ── */
        .modal-konten .modal-header {
            background : linear-gradient(135deg, var(--green-mid) 0%, var(--green-dark) 100%);
            padding    : 20px 24px 18px;
            border     : none;
            align-items: center;
        }
        .modal-konten .modal-header .modal-title {
            color      : #fff;
            font-size  : 1rem;
            font-weight: 700;
            display    : flex;
            align-items: center;
            gap        : 8px;
        }
        .modal-konten .modal-header .modal-title i {
            font-size        : .95rem;
            background       : rgba(255,255,255,.18);
            width            : 28px;
            height           : 28px;
            border-radius    : 50%;
            display          : flex;
            align-items      : center;
            justify-content  : center;
            flex-shrink      : 0;
        }
        .modal-konten .modal-header .btn-close {
            filter : invert(1);
            opacity: .75;
        }
        .modal-konten .modal-header .btn-close:hover { opacity: 1; }

        /* ── Body ── */
        .modal-konten .modal-body {
            background : #f7f9fc;
            padding    : 22px 24px 8px;
            max-height : 70vh;
            overflow-y : auto;
            overflow-x : hidden;
        }

        /* ── Label & input ── */
        .modal-konten .field-label {
            font-size  : .775rem;
            font-weight: 600;
            color      : #5a6270;
            margin-bottom: 5px;
            display    : block;
            letter-spacing: .3px;
            text-transform: uppercase;
        }
        .modal-konten .form-control {
            border       : 1.5px solid #dde1e8 !important;
            border-radius: var(--radius-md) !important;
            font-size    : .875rem !important;
            color        : #3b3f4a !important;
            background   : #fff !important;
            transition   : border-color .2s, box-shadow .2s !important;
            padding      : 9px 13px !important;
        }
        .modal-konten .form-control:focus {
            border-color: var(--green-mid) !important;
            box-shadow  : 0 0 0 3px rgba(46,125,50,.12) !important;
            outline     : none !important;
        }
        .modal-konten textarea.form-control { resize: none; }

        /* ── Preview gambar ── */
        .img-preview-box {
            border-radius: var(--radius-md);
            border       : 1.5px dashed #c8cdd6;
            background   : #f0f4f8;
            min-height   : 130px;
            display      : flex;
            align-items  : center;
            justify-content: center;
            padding      : 10px;
            transition   : border-color .2s;
        }
        .img-preview-box img {
            max-width    : 100%;
            max-height   : 160px;
            object-fit   : contain;
            border-radius: 6px;
        }
        .img-preview-label {
            font-size: .75rem;
            color    : #7a8499;
            margin   : 0;
        }
        .img-preview-label.success { color: #2e7d32; }

        /* ── Upload hint chip ── */
        .upload-hint {
            font-size : .72rem;
            color     : #8e96a5;
            margin-top: 4px;
        }

        /* ── Footer ── */
        .modal-konten .modal-footer {
            background : #fff;
            border-top : 1px solid #eaecf2;
            padding    : 14px 24px;
            gap        : 10px;
        }

        /* ── Tombol batal & simpan (shared) ── */
        .btn-modal-batal {
            padding      : 8px 22px;
            border-radius: var(--radius-md);
            font-weight  : 600;
            font-size    : .85rem;
            border       : 1.5px solid #d4d8e2;
            background   : #fff;
            color        : #5a6270;
            transition   : all .2s;
        }
        .btn-modal-batal:hover {
            background  : #f5f7fa;
            border-color: #adb5bd;
            color       : #3b3f4a;
        }
        .btn-modal-simpan {
            padding      : 8px 22px;
            border-radius: var(--radius-md);
            font-weight  : 600;
            font-size    : .85rem;
            border       : none;
            background   : linear-gradient(135deg, var(--green-light) 0%, var(--green-dark) 100%);
            color        : #fff;
            transition   : all .2s;
            box-shadow   : 0 4px 12px rgba(46,125,50,.28);
            display      : flex;
            align-items  : center;
            gap          : 6px;
        }
        .btn-modal-simpan:hover {
            transform : translateY(-1px);
            box-shadow: 0 6px 18px rgba(46,125,50,.38);
        }
        .btn-modal-simpan:active { transform: translateY(0); }

        /* ═══════════════════════════════════════════
           MODAL HAPUS (merah)
        ═══════════════════════════════════════════ */
        #modalHapusKonten .modal-hapus-header {
            background : linear-gradient(135deg, var(--red-mid) 0%, var(--red-dark) 100%);
            padding    : 28px 28px 20px;
            text-align : center;
        }
        #modalHapusKonten .modal-hapus-icon {
            width       : 60px;
            height      : 60px;
            background  : rgba(255,255,255,.2);
            border-radius: 50%;
            display     : flex;
            align-items : center;
            justify-content: center;
            margin      : 0 auto 12px;
            backdrop-filter: blur(4px);
        }
        #modalHapusKonten .modal-hapus-icon i { font-size: 1.6rem; color: #fff; }
        #modalHapusKonten .modal-hapus-title  { color: #fff; font-size: 1.1rem; font-weight: 700; margin: 0; }

        #modalHapusKonten .modal-hapus-body { padding: 22px 28px 10px; text-align: center; }
        #modalHapusKonten .hapus-konten-name {
            display      : inline-block;
            background   : #fff4f4;
            border       : 1px solid #ffd5d5;
            border-radius: 8px;
            padding      : 5px 14px;
            font-weight  : 600;
            color        : var(--red-dark);
            font-size    : .875rem;
            max-width    : 100%;
            overflow     : hidden;
            text-overflow: ellipsis;
            white-space  : nowrap;
            margin-bottom: 10px;
        }
        #modalHapusKonten .hapus-warning-text { font-size: .85rem; color: #6c757d; line-height: 1.6; margin: 0; }

        #modalHapusKonten .modal-hapus-footer {
            padding        : 14px 28px 22px;
            display        : flex;
            gap            : 10px;
            justify-content: center;
        }
        #modalHapusKonten .btn-hapus-batal {
            flex        : 1;
            max-width   : 140px;
            padding     : 9px 20px;
            border-radius: var(--radius-md);
            font-weight : 600;
            font-size   : .875rem;
            border      : 1.5px solid #d4d8e2;
            background  : #fff;
            color       : #5a6270;
            transition  : all .2s;
        }
        #modalHapusKonten .btn-hapus-batal:hover { background: #f5f7fa; border-color: #adb5bd; }
        #modalHapusKonten .btn-hapus-konfirmasi {
            flex        : 1;
            max-width   : 160px;
            padding     : 9px 20px;
            border-radius: var(--radius-md);
            font-weight : 600;
            font-size   : .875rem;
            border      : none;
            background  : linear-gradient(135deg, var(--red-mid) 0%, var(--red-dark) 100%);
            color       : #fff;
            transition  : all .2s;
            box-shadow  : 0 4px 12px rgba(192,57,43,.3);
        }
        #modalHapusKonten .btn-hapus-konfirmasi:hover  { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(192,57,43,.4); }
        #modalHapusKonten .btn-hapus-konfirmasi:active { transform: translateY(0); }

        /* ═══════════════════════════════════════════
           DIVIDER SEKSI DALAM FORM
        ═══════════════════════════════════════════ */
        .form-section {
            background   : #fff;
            border-radius: var(--radius-md);
            padding      : 16px;
            border       : 1px solid #eaecf2;
            margin-bottom: 14px;
        }
        .form-section:last-child { margin-bottom: 0; }
        .form-section-title {
            font-size    : .7rem;
            font-weight  : 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            color        : #adb5bd;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom : 1px solid #f0f2f5;
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
                            <h3 class="fw-bold mb-1">Kelola Konten Informasi</h3>
                            <ul class="breadcrumbs mb-0 p-0" style="background: transparent;">
                                <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="{{ route('kelola-konten.index') }}" class="text-muted">Kelola Konten</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-primary btn-round" data-bs-toggle="modal" data-bs-target="#modalTambahKonten">
                            <i class="fa fa-plus me-2"></i> Tambah Kartu Baru
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show card-round shadow-sm" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card card-round shadow-sm mb-4">
                        <div class="card-body py-3">
                            <h5 class="fw-bold mb-1" style="font-size: 1rem; color: #2a2b2d;">Kartu Informasi Utama</h5>
                            <p class="text-muted small mb-0">Atur kartu slider informasi yang muncul di halaman utama aplikasi mobile user.</p>
                        </div>
                    </div>

                    <div class="row">
                        @forelse($konten as $item)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card card-round shadow-sm h-100" style="background-color: {{ $item->color ?? '#ffffff' }}; border: 1px solid #ebedf2;">
                                    <div class="card-body d-flex flex-column justify-content-between p-4">
                                        <div>
                                            @if($item->image)
                                                <div class="mb-3">
                                                    <div class="rounded border d-flex align-items-center justify-content-center p-2"
                                                         style="background-color: #f4f6f9; min-height: 140px; max-height: 200px;">
                                                        <img src="{{ asset('storage/' . $item->image) }}"
                                                             style="max-width: 100%; max-height: 180px; object-fit: contain; display: block; border-radius: 6px;"
                                                             alt="Gambar: {{ $item->title }}">
                                                    </div>
                                                    <p class="text-muted mt-1 mb-0" style="font-size: 10px;">
                                                        <i class="fa fa-info-circle me-1"></i>Gambar ditampilkan utuh sesuai aslinya.
                                                    </p>
                                                </div>
                                            @endif

                                            <h5 class="fw-bold mb-2" style="color: {{ $item->text_color ?? '#2a2b2d' }}">{{ $item->title }}</h5>
                                            <p class="small mb-0 text-dark opacity-75"
                                               style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                                                {{ $item->description }}
                                            </p>
                                        </div>

                                        <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-2 border-top border-black border-opacity-10">
                                            <button type="button"
                                                class="btn btn-white btn-sm shadow-sm border text-secondary btn-edit-konten"
                                                data-id="{{ $item->id }}"
                                                data-title="{{ $item->title }}"
                                                data-description="{{ $item->description }}">
                                                <i class="fa fa-edit me-1"></i> Edit
                                            </button>
                                            <button type="button"
                                                class="btn btn-danger btn-sm px-3 btn-hapus-konten"
                                                data-id="{{ $item->id }}"
                                                data-title="{{ $item->title }}"
                                                data-action="{{ route('kelola-konten.destroy', $item->id) }}">
                                                <i class="fa fa-trash-alt me-1"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 bg-white border border-dashed card-round">
                                <i class="fas fa-folder-open text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted fw-medium mb-0">Belum ada konten informasi. Silakan tambah kartu baru.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

            @include('partials.footer')
        </div>
    </div>


    {{-- ══════════════════════════════════════════════
         MODAL TAMBAH KONTEN
    ══════════════════════════════════════════════ --}}
    <div class="modal fade modal-konten" id="modalTambahKonten" tabindex="-1"
         aria-labelledby="modalTambahKontenLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahKontenLabel">
                        <i class="fa fa-plus"></i>
                        Tambah Informasi Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <form action="{{ route('kelola-konten.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">

                        {{-- Seksi: Teks --}}
                        <div class="form-section">
                            <p class="form-section-title"><i class="fa fa-align-left me-1"></i> Informasi Teks</p>

                            <div class="mb-3">
                                <label class="field-label" for="tambah_title">Judul Kartu</label>
                                <input type="text" name="title" id="tambah_title" required
                                       class="form-control"
                                       placeholder="Contoh: Cara Mendaftar STIFIn">
                            </div>

                            <div class="mb-0">
                                <label class="field-label" for="tambah_description">Deskripsi</label>
                                <textarea name="description" id="tambah_description" rows="4" required
                                          class="form-control"
                                          placeholder="Tulis rincian informasi di sini…"></textarea>
                            </div>
                        </div>

                        {{-- Seksi: Gambar --}}
                        <div class="form-section">
                            <p class="form-section-title"><i class="fa fa-image me-1"></i> Gambar Sampul <span style="font-weight:400; text-transform:none; letter-spacing:0;">(opsional)</span></p>

                            <input type="file" name="image" id="tambah_image_input"
                                   accept="image/*" class="form-control">
                            <p class="upload-hint"><i class="fa fa-info-circle me-1"></i>Format: JPG, PNG, WEBP · Maks. 2 MB</p>

                            <div id="tambah_preview_wrap" class="mt-3 d-none">
                                <p class="img-preview-label mb-1"><i class="fa fa-eye me-1"></i>Pratinjau gambar:</p>
                                <div class="img-preview-box">
                                    <img id="tambah_preview_img" src="#" alt="Pratinjau">
                                </div>
                                <p class="img-preview-label success mt-1 mb-0">
                                    <i class="fa fa-check-circle me-1"></i>Gambar ditampilkan utuh, tidak terpotong.
                                </p>
                            </div>
                        </div>

                    </div>{{-- /modal-body --}}

                    <div class="modal-footer justify-content-end">
                        <button type="button" class="btn-modal-batal" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-modal-simpan">
                            <i class="fas fa-save"></i> Simpan Konten
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    {{-- ══════════════════════════════════════════════
         MODAL EDIT KONTEN
    ══════════════════════════════════════════════ --}}
    <div class="modal fade modal-konten" id="modalEditKonten" tabindex="-1"
         aria-labelledby="modalEditKontenLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditKontenLabel">
                        <i class="fa fa-edit"></i>
                        Edit Informasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <form id="formEditKonten" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">

                        {{-- Seksi: Teks --}}
                        <div class="form-section">
                            <p class="form-section-title"><i class="fa fa-align-left me-1"></i> Informasi Teks</p>

                            <div class="mb-3">
                                <label class="field-label" for="edit_title">Judul Kartu</label>
                                <input type="text" name="title" id="edit_title" required
                                       class="form-control"
                                       placeholder="Judul kartu informasi">
                            </div>

                            <div class="mb-0">
                                <label class="field-label" for="edit_description">Deskripsi</label>
                                <textarea name="description" id="edit_description" rows="4" required
                                          class="form-control"
                                          placeholder="Deskripsi informasi…"></textarea>
                            </div>
                        </div>

                        {{-- Seksi: Gambar --}}
                        <div class="form-section">
                            <p class="form-section-title"><i class="fa fa-image me-1"></i> Ganti Gambar <span style="font-weight:400; text-transform:none; letter-spacing:0;">(kosongkan jika tidak diubah)</span></p>

                            <input type="file" name="image" id="edit_image_input"
                                   accept="image/*" class="form-control">
                            <p class="upload-hint"><i class="fa fa-info-circle me-1"></i>Format: JPG, PNG, WEBP · Maks. 2 MB</p>

                            <div id="edit_preview_wrap" class="mt-3 d-none">
                                <p class="img-preview-label mb-1"><i class="fa fa-eye me-1"></i>Pratinjau gambar baru:</p>
                                <div class="img-preview-box">
                                    <img id="edit_preview_img" src="#" alt="Pratinjau">
                                </div>
                                <p class="img-preview-label success mt-1 mb-0">
                                    <i class="fa fa-check-circle me-1"></i>Gambar ditampilkan utuh, tidak terpotong.
                                </p>
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
         MODAL KONFIRMASI HAPUS
    ══════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalHapusKonten" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content">

                <div class="modal-hapus-header">
                    <div class="modal-hapus-icon">
                        <i class="fa fa-trash-alt"></i>
                    </div>
                    <p class="modal-hapus-title">Hapus Konten?</p>
                </div>

                <div class="modal-hapus-body">
                    <span class="hapus-konten-name" id="hapus_konten_name">—</span>
                    <p class="hapus-warning-text">
                        Konten ini akan dihapus secara permanen dan <strong>tidak dapat dikembalikan</strong>.
                        Apakah Anda yakin ingin melanjutkan?
                    </p>
                </div>

                <div class="modal-hapus-footer">
                    <button type="button" class="btn-hapus-batal" data-bs-dismiss="modal">Batal</button>
                    <form id="formHapusKonten" method="POST" style="flex: 1; max-width: 160px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-hapus-konfirmasi w-100">
                            <i class="fa fa-trash-alt me-1"></i> Ya, Hapus
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <script>
    $(document).ready(function () {

        /* ── Helper: preview gambar ── */
        function previewGambar(input, imgSel, wrapSel) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $(imgSel).attr('src', e.target.result);
                    $(wrapSel).removeClass('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                $(wrapSel).addClass('d-none');
                $(imgSel).attr('src', '#');
            }
        }

        /* ── Buka modal Edit ── */
        $(document).on('click', '.btn-edit-konten', function () {
            const id          = $(this).data('id');
            const title       = $(this).data('title');
            const description = $(this).data('description');

            $('#formEditKonten').attr('action', "{{ route('kelola-konten.index') }}/" + id);
            $('#edit_title').val(title);
            $('#edit_description').val(description);

            // Reset preview
            $('#edit_image_input').val('');
            $('#edit_preview_wrap').addClass('d-none');
            $('#edit_preview_img').attr('src', '#');

            $('#modalEditKonten').modal('show');
        });

        /* ── Buka modal Hapus ── */
        $(document).on('click', '.btn-hapus-konten', function () {
            $('#hapus_konten_name').text($(this).data('title'));
            $('#formHapusKonten').attr('action', $(this).data('action'));
            $('#modalHapusKonten').modal('show');
        });

        /* ── Preview gambar — Tambah ── */
        $('#tambah_image_input').on('change', function () {
            previewGambar(this, '#tambah_preview_img', '#tambah_preview_wrap');
        });

        /* ── Preview gambar — Edit ── */
        $('#edit_image_input').on('change', function () {
            previewGambar(this, '#edit_preview_img', '#edit_preview_wrap');
        });

    });
    </script>
</body>
</html>