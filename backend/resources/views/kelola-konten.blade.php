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

                    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h3 class="fw-bold mb-1">Kelola Konten Informasi</h3>
                            <ul class="breadcrumbs mb-0 p-0" style="background: transparent;">
                                <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
                                <li class="separator"><i class="icon-arrow-right"></i></li>
                                <li class="nav-item"><a href="#" class="text-muted">Kelola Konten</a></li>
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
                                <div class="card card-round shadow-sm h-100" style="background-color: {{ $item->color }}; min-height: 220px;">
                                    <div class="card-body d-flex flex-column justify-content-between p-4">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="badge bg-white bg-opacity-75 px-2 py-1 border border-light text-truncate" style="color: {{ $item->text_color }}; font-weight: 700;">
                                                    <i class="fas fa-cube me-1"></i> {{ $item->icon }}
                                                </span>
                                                <span class="badge bg-white bg-opacity-70 text-uppercase fw-bold" style="color: {{ $item->text_color }}; font-size: 10px;">Penting</span>
                                            </div>

                                            @if($item->image)
                                                <div class="mb-3 w-100 rounded overflow-hidden bg-white bg-opacity-50 border" style="height: 120px;">
                                                    <img src="{{ asset('storage/' . $item->image) }}" class="w-100 h-100" style="object-fit: cover;" alt="Preview Image">
                                                </div>
                                            @endif

                                            <h5 class="fw-bold mb-2" style="color: {{ $item->text_color }}">{{ $item->title }}</h5>
                                            <p class="small mb-0 text-dark opacity-75" style="display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5;">
                                                {{ $item->description }}
                                            </p>
                                        </div>

                                        <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-2 border-top border-black border-opacity-10">
                                            <button type="button"
                                                class="btn btn-white btn-sm shadow-sm border text-secondary btn-edit-konten"
                                                data-id="{{ $item->id }}"
                                                data-title="{{ $item->title }}"
                                                data-description="{{ $item->description }}"
                                                data-icon="{{ $item->icon }}"
                                
                                                <i class="fa fa-edit me-1"></i> Edit
                                            </button>

                                            <form action="{{ route('kelola-konten.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus konten ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm px-3">
                                                    <i class="fa fa-trash-alt me-1"></i> Hapus
                                                </button>
                                            </form>
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

            <footer class="footer">
                <div class="container-fluid d-flex justify-content-between">
                    <div class="copyright text-center w-100">
                        2026, made with <i class="fa fa-heart text-danger"></i> by STIFIn Project
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <div class="modal fade" id="modalTambahKonten" tabindex="-1" aria-labelledby="modalTambahKontenLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-round p-2">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalTambahKontenLabel">Tambah Informasi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('kelola-konten.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body space-y-3">
                        <div class="form-group p-0 mb-3">
                            <label class="fw-semibold small mb-1 text-secondary">Judul Kartu</label>
                            <input type="text" name="title" required class="form-control" placeholder="Masukkan judul info">
                        </div>
                        <div class="form-group p-0 mb-3">
                            <label class="fw-semibold small mb-1 text-secondary">Deskripsi Informasi</label>
                            <textarea name="description" rows="4" required class="form-control" style="resize: none;" placeholder="Tulis rincian prosedur..."></textarea>
                        </div>
                        <div class="form-group p-0 mb-3">
                            <label class="fw-semibold small mb-1 text-secondary">Pilih Ikon Informasi</label>
                            <select name="icon" class="form-select">
                                <option value="information-circle-outline">💡 Informasi Umum / Prosedur</option>
                                <option value="calendar-outline">📅 Jadwal & Agenda</option>
                                <option value="star-outline">⭐ Promo / Pengumuman Penting</option>
                                <option value="help-circle-outline">❓ Bantuan / Panduan</option>
                            </select>
                        </div>
                        <div class="form-group p-0 mb-3">
                            <label class="fw-semibold small mb-1 text-secondary">Unggah Gambar Sampul (Opsional)</label>
                            <input type="file" name="image" accept="image/*" class="form-control">
                            <small class="text-muted d-block mt-1" style="font-size: 10px;">* Format berkas: JPG, PNG, WEBP. Maksimal ukuran 2MB.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light btn-sm fw-bold px-3 btn-round" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-3 btn-round">Simpan Konten</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditKonten" tabindex="-1" aria-labelledby="modalEditKontenLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-round p-2">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark" id="modalEditKontenLabel">Edit Informasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditKonten" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group p-0 mb-3">
                            <label class="fw-semibold small mb-1 text-secondary">Judul Kartu</label>
                            <input type="text" name="title" id="edit_title" required class="form-control">
                        </div>
                        <div class="form-group p-0 mb-3">
                            <label class="fw-semibold small mb-1 text-secondary">Deskripsi Informasi</label>
                            <textarea name="description" id="edit_description" rows="4" required class="form-control" style="resize: none;"></textarea>
                        </div>
                        <div class="form-group p-0 mb-3">
                            <label class="fw-semibold small mb-1 text-secondary">Pilih Ikon Informasi</label>
                            <select name="icon" id="edit_icon" class="form-select">
                                <option value="information-circle-outline">💡 Informasi Umum</option>
                                <option value="calendar-outline">📅 Jadwal & Agenda</option>
                                <option value="star-outline">⭐ Promo / Pengumuman Penting</option>
                                <option value="help-circle-outline">❓ Bantuan / Panduan</option>
                            </select>
                        </div>
                        <div class="form-group p-0 mb-3">
                            <label class="fw-semibold small mb-1 text-secondary">Ganti Gambar (Biarkan kosong jika tidak diubah)</label>
                            <input type="file" name="image" accept="image/*" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light btn-sm fw-bold px-3 btn-round" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-3 btn-round">Simpan Perubahan</button>
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

    <script>
        $(document).ready(function() {
            $('.btn-edit-konten').on('click', function() {
                const id = $(this).data('id');
                const title = $(this).data('title');
                const description = $(this).data('description');
                const icon = $(this).data('icon');
    
                // Inject action route dinamis Laravel
                $('#formEditKonten').attr('action', "{{ route('kelola-konten.index') }}/" + id);

                // Inject data ke form modal
                $('#edit_title').val(title);
                $('#edit_description').val(description);
                $('#edit_icon').val(icon);
               

                // Tampilkan Modal Edit Bootstrap 5
                $('#modalEditKonten').modal('show');
            });
        });
    </script>
</body>
</html>
