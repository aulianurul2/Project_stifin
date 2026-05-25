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
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Jadwal Tes</th>
                                                    <th class="px-4 py-3 text-uppercase font-weight-bold text-muted fs-8">Lokasi</th>
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
        
        <td class="px-4 py-3 text-dark">
            <span class="d-block fw-semibold">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</span>
            <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }} WIB</small>
        </td>
        
        <td class="px-4 py-3">
            @if($item->lokasi == 'Home Visit')
                <span class="badge badge-secondary px-3 py-1 btn-round text-uppercase fs-8 fw-bold">
                    <i class="fas fa-home me-1"></i> Home Visit
                </span>
            @else
                <span class="badge badge-info px-3 py-1 btn-round text-uppercase fs-8 fw-bold">
                    <i class="fas fa-building me-1"></i> Kantor Cabang
                </span>
            @endif
        </td>

        <td class="px-4 py-3">
            @if($item->status == 'Diterima')
                <span class="badge badge-success">Diterima</span>
            @elseif($item->status == 'Ditolak')
                <span class="badge badge-danger">Ditolak</span>
            @else
                <span class="badge badge-warning text-white">Menunggu</span>
            @endif
        </td>
        <td class="px-4 py-3 text-muted italic text-truncate" style="max-width: 200px;">
            {{ $item->komentar ?? 'Tidak ada komen' }}
        </td>
        
        <td class="px-4 py-3 text-center">
            <div class="form-button-action">
                <button type="button" 
                        class="btn btn-link btn-info btn-lg"
                        data-bs-toggle="tooltip"
                        title="Lihat Detail Klien"
                        onclick="openModalView('{{ $item->nama_klien }}', '{{ $item->no_hp }}', '{{ $item->email }}', '{{ $item->tanggal_lahir }}', '{{ $item->jenis_kelamin }}', '{{ $item->golongan_darah }}', '{{ $item->institusi ?? '-' }}', '{{ $item->sosmed ?? '-' }}', '{{ $item->domisili ?? '-' }}', '{{ $item->alamat }}')">
                    <i class="fa fa-eye"></i>
                </button>

                <button type="button" 
                        class="btn btn-link btn-primary btn-lg"
                        data-bs-toggle="tooltip"
                        title="Ubah Status Pendaftaran"
                        onclick="openModalStatus('{{ $item->id_jadwal }}', '{{ $item->nama_klien }}', '{{ ($item->status == 'Diterima' || $item->status == 'Ditolak') ? $item->status : 'Menunggu' }}', '{{ $item->komentar ?? '' }}')">
                    <i class="fa fa-edit"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="7" class="px-4 py-5 text-center text-muted italic">Belum ada data pendaftaran.</td>
    </tr>
    @endforelse
</tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer d-flex justify-content-center">
    {{ $pendaftaran->links('pagination::bootstrap-5') }}
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
    <div class="modal fade" id="modalView" tabindex="-1" aria-labelledby="modalViewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content card-round">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold" id="modalViewLabel">
                    <i class="fas fa-user-circle text-info me-2"></i>Detail Pendaftaran Klien
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-12">
                        <h6 class="fw-bold text-info border-bottom pb-2 mb-3">1. DATA PERSONAL</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Nama Lengkap</label>
                        <p id="viewNama" class="fw-bold text-dark fs-6 mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Nomor WhatsApp</label>
                        <p id="viewNoHp" class="fw-semibold text-dark mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Email</label>
                        <p id="viewEmail" class="text-dark mb-0">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Tanggal Lahir</label>
                        <p id="viewTglLahir" class="text-dark mb-0">-</p>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Gol. Darah / JK</label>
                        <p id="viewGoldarJK" class="text-dark mb-0">-</p>
                    </div>

                    <div class="col-md-12 mt-4">
                        <h6 class="fw-bold text-info border-bottom pb-2 mb-3">2. DATA TAMBAHAN</h6>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Institusi / Pekerjaan</label>
                        <p id="viewInstitusi" class="text-dark mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Username Sosmed</label>
                        <p id="viewSosmed" class="text-dark mb-0">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Kota Domisili</label>
                        <p id="viewDomisili" class="text-dark mb-0">-</p>
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small text-uppercase fw-bold mb-1">Alamat Lengkap</label>
                        <p id="viewAlamat" class="text-dark bg-light p-3 rounded mb-0" style="white-space: pre-line;">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 bg-light-gradient">
                <button type="button" class="btn btn-secondary btn-round px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
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
        const modalViewBS = new bootstrap.Modal(document.getElementById('modalView'));
        const modalStatusBS = new bootstrap.Modal(document.getElementById('modalStatus'));

        function openModalView(nama, noHp, email, tglLahir, jk, goldar, institusi, sosmed, domisili, alamat) {
    // Format visual Jenis Kelamin
    const jkText = jk === 'L' ? 'Laki-laki' : (jk === 'P' ? 'Perempuan' : jk);
    
    // Terapkan data ke teks elemen modal
    document.getElementById('viewNama').innerText = nama;
    document.getElementById('viewNoHp').innerText = noHp;
    document.getElementById('viewEmail').innerText = email;
    document.getElementById('viewTglLahir').innerText = tglLahir;
    document.getElementById('viewGoldarJK').innerText = `${goldar} / ${jkText}`;
    document.getElementById('viewInstitusi').innerText = institusi;
    document.getElementById('viewSosmed').innerText = sosmed;
    document.getElementById('viewDomisili').innerText = domisili;
    document.getElementById('viewAlamat').innerText = alamat;

    // Tampilkan modal detail
    modalViewBS.show();
}

        function openModalStatus(id, nama, status, komentar) {
            document.getElementById('modalNama').innerText = nama;
            document.getElementById('inputStatus').value = status;
            document.getElementById('inputKomentar').value = komentar;

            const baseUrl = "{{ url('pendaftaran-tes') }}";
            document.getElementById('formStatus').action = `${baseUrl}/${id}`;

            modalStatusBS.show();
        }
    </script>
</body>
</html>