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
                        <div class="mb-3 text-muted">
                            Admin: <span class="fw-bold text-dark">{{ Auth::user()->nama }}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-round">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0 align-middle">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Nama</th>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">No. HP</th>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Status</th>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Komentar</th>
                                                    <th class="px-4 py-3 text-center text-uppercase font-weight-bold text-muted fs-8" style="width: 15%">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pendaftaran as $item)
                                                <tr>
                                                    <td class="px-4 py-3 fw-bold text-dark">{{ $item->nama_klien }}</td>
                                                    <td class="px-4 py-3 text-muted">{{ $item->no_hp }}</td>
                                                    <td class="px-4 py-3">
                                                        @if($item->status == 'Diterima')
                                                            <span class="badge badge-success">Diterima</span>
                                                        @elif($item->status == 'Ditolak')
                                                            <span class="badge badge-danger">Ditolak</span>
                                                        @else
                                                            <span class="badge badge-warning text-white">Menunggu</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-muted italic text-truncate" style="max-w-xs: 200px;">
                                                        {{ $item->komentar ?? 'Tidak ada komen' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        <button type="button"
                                                                onclick="openModalStatus('{{ $item->id_jadwal }}', '{{ $item->nama_klien }}', '{{ $item->status ?? 'Menunggu' }}', '{{ $item->komentar ?? '' }}')"
                                                                class="btn btn-sm btn-light-primary btn-round fw-bold px-3">
                                                            <i class="fas fa-edit me-1"></i> Update Status
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="px-4 py-5 text-center text-muted italic">Belum ada data pendaftaran.</td>
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

    <div class="modal fade" id="modalStatus" tabindex="-1" aria-labelledby="modalStatusLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-round">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalStatusLabel">Update Pendaftaran: <span id="modalNama" class="text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formStatus" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3 p-0">
                            <label class="mb-2 fw-bold text-dark">Pilih Status</label>
                            <select name="status" id="inputStatus" class="form-select form-control">
                                <option value="Menunggu">Menunggu</option>
                                <option value="Diterima">Diterima</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>

                        <div class="form-group mb-2 p-0">
                            <label class="mb-2 fw-bold text-dark">Komentar *</label>
                            <textarea name="komentar" id="inputKomentar" rows="4" class="form-control" placeholder="Alasan ditolak atau informasi tambahan jadwal..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-border btn-round" data-bs-dismiss="modal">Batal</button>
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

    <script>
        // Inisialisasi Instance Modal Bootstrap 5
        const modalStatusBS = new bootstrap.Modal(document.getElementById('modalStatus'));

        // Fungsi Trigger Modal menggunakan Native JavaScript (Menggantikan AlpineJS)
        function openModalStatus(id, nama, status, komentar) {
            // 1. Set informasi nama klien di judul modal
            document.getElementById('modalNama').innerText = nama;

            // 2. Set isi value input sesuai data row yang dipilih
            document.getElementById('inputStatus').value = status;
            document.getElementById('inputKomentar').value = komentar;

            // 3. Set Route Action Form secara dinamis
            const baseUrl = "{{ url('pendaftaran-tes') }}";
            document.getElementById('formStatus').action = `${baseUrl}/${id}`;

            // 4. Tampilkan Modal ke layar
            modalStatusBS.show();
        }
    </script>
</body>
</html>
