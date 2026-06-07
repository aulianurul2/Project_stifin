<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Edit Profil - STIFIn Admin</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />

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
        /* ─── Card Profil ─────────────────────────────────── */
        .profil-avatar {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00AA5B 0%, #007a41 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; color: #fff; font-weight: 700;
            box-shadow: 0 6px 20px rgba(0,170,91,.30);
            flex-shrink: 0;
        }
        .profil-header-card {
            background: linear-gradient(135deg, #00AA5B 0%, #007a41 100%);
            border-radius: 16px;
            padding: 28px 28px 20px;
            color: #fff;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .profil-header-card::before {
            content: '';
            position: absolute; top: -40px; right: -40px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
        }
        .profil-header-card::after {
            content: '';
            position: absolute; bottom: -30px; right: 60px;
            width: 100px; height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
        }

        /* ─── Section divider label ───────────────────────── */
        .section-label {
            font-size: 11px; font-weight: 700; letter-spacing: 1px;
            text-transform: uppercase; color: #90a4ae;
            margin-bottom: 14px; margin-top: 8px;
            display: flex; align-items: center; gap: 8px;
        }
        .section-label::after {
            content: ''; flex: 1; height: 1px; background: #e8f5e9;
        }

        /* ─── Input styling ───────────────────────────────── */
        .form-control-profil {
            border: 1.5px solid #e0f2ec;
            border-radius: 10px;
            padding: 10px 14px 10px 40px;
            font-size: 14px;
            color: #1a1a2e;
            background: #f8fffe;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control-profil:focus {
            border-color: #00AA5B;
            box-shadow: 0 0 0 3px rgba(0,170,91,.12);
            background: #fff;
            outline: none;
        }
        .form-control-profil.is-invalid {
            border-color: #e53935;
            background: #fff8f8;
        }
        .input-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: #90a4ae; font-size: 14px; pointer-events: none;
        }
        .form-label-custom {
            font-size: 12px; font-weight: 700; color: #546e7a;
            margin-bottom: 6px; display: block;
        }

        /* ─── WA Card ─────────────────────────────────────── */
        .wa-card {
            background: #f0faf5;
            border: 1.5px solid #a5d6a7;
            border-radius: 14px;
            padding: 18px 20px;
        }
        .wa-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #25D366; color: #fff;
            font-size: 11px; font-weight: 700;
            border-radius: 8px; padding: 3px 10px;
            margin-bottom: 14px;
        }
        .wa-note {
            font-size: 11px; color: #78909c; font-style: italic;
            margin-top: 8px; display: flex; align-items: flex-start; gap: 6px;
        }

        /* ─── Password toggle eye ─────────────────────────── */
        .input-eye {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            cursor: pointer; color: #90a4ae; font-size: 14px;
            padding: 4px; user-select: none;
        }
        .input-eye:hover { color: #00AA5B; }

        /* ─── Password strength meter ─────────────────────── */
        .strength-bar { height: 4px; border-radius: 4px; margin-top: 8px; background: #e0e0e0; overflow: hidden; }
        .strength-fill { height: 100%; border-radius: 4px; transition: width .3s, background .3s; }

        /* ─── Save button ─────────────────────────────────── */
        .btn-simpan {
            background: linear-gradient(135deg, #00AA5B 0%, #007a41 100%);
            color: #fff; border: none; border-radius: 12px;
            padding: 12px 32px; font-size: 14px; font-weight: 700;
            letter-spacing: .3px;
            box-shadow: 0 4px 14px rgba(0,170,91,.35);
            transition: transform .15s, box-shadow .15s;
        }
        .btn-simpan:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,170,91,.40);
            color: #fff;
        }
        .btn-simpan:active { transform: translateY(0); }

        /* ─── Alert styling ───────────────────────────────── */
        .alert-profil-success {
            border-radius: 12px; border: none; border-left: 4px solid #00AA5B;
            background: #e8f5e9; color: #1b5e20;
            font-size: 13px; font-weight: 600;
            display: flex; align-items: center; gap: 10px;
        }
        .alert-profil-error {
            border-radius: 12px; border: none; border-left: 4px solid #e53935;
            background: #ffebee; color: #c62828;
            font-size: 13px; font-weight: 600;
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

                {{-- Helper: konversi 62xxx → 08xxx untuk tampilan --}}
                @php
                    function toLokal($nomor) {
                        if (!$nomor) return '—';
                        return str_starts_with($nomor, '62') ? '0' . substr($nomor, 2) : $nomor;
                    }
                @endphp

                {{-- Page Header --}}
                <div class="page-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="fw-bold mb-1">Edit Profil</h3>
                        <h6 class="op-7 mb-0">Kelola informasi akun dan kontak Admin</h6>
                        
                        
                    </div>
                </div>

                {{-- Alert Sukses --}}
                @if(session('success'))
                    <div class="alert alert-profil-success mb-4" role="alert">
                        <i class="fas fa-check-circle fa-lg"></i>
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Alert Error Global --}}
                @if($errors->has('error'))
                    <div class="alert alert-profil-error mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ $errors->first('error') }}
                    </div>
                @endif

                <div class="row">

                    {{-- ── Kolom Kiri: Header Profil ── --}}
                    <div class="col-md-4">
                        <div class="profil-header-card">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="profil-avatar">
                                    {{ strtoupper(substr($user->nama ?? 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold" style="font-size:17px;">{{ $user->nama }}</div>
                                    <span style="font-size:11px; background:rgba(255,255,255,.2); border-radius:6px; padding:2px 8px; display:inline-block; margin-top:4px; font-weight:600; text-transform:uppercase; letter-spacing:.5px;">
                                        {{ $user->role }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Info Kontak Singkat --}}
                        <div class="card card-round mb-4">
                            <div class="card-body">
                                <p class="section-label"><i class="fas fa-info-circle me-1"></i> Info Akun</p>
                                <div class="d-flex flex-column gap-3" style="font-size:13px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:30px;height:30px;border-radius:8px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;">
                                            <i class="fas fa-user" style="color:#00AA5B;font-size:12px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size:10px;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Nama</div>
                                            <div style="font-weight:700;color:#1a1a2e;">{{ $user->nama }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:30px;height:30px;border-radius:8px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;">
                                            <i class="fas fa-at" style="color:#00AA5B;font-size:12px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size:10px;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Username</div>
                                            <div style="font-weight:700;color:#1a1a2e;">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:30px;height:30px;border-radius:8px;background:#e8ffe8;display:flex;align-items:center;justify-content:center;">
                                            <i class="fab fa-whatsapp" style="color:#25D366;font-size:14px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size:10px;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Promotor</div>
                                            <div style="font-weight:700;color:#1a1a2e;">{{ toLokal($admin->wa1 ?? '') }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:30px;height:30px;border-radius:8px;background:#e8ffe8;display:flex;align-items:center;justify-content:center;">
                                            <i class="fab fa-whatsapp" style="color:#25D366;font-size:14px;"></i>
                                        </div>
                                        <div>
                                            <div style="font-size:10px;color:#90a4ae;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Admin</div>
                                            <div style="font-weight:700;color:#1a1a2e;">{{ toLokal($admin->wa2 ?? '') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Kolom Kanan: Form Edit ── --}}
                    <div class="col-md-8">
                        <div class="card card-round">
                            <div class="card-body" style="padding: 28px;">

                                <form method="POST" action="{{ route('profil.update') }}" id="formProfil">
                                    @csrf
                                    @method('PUT')

                                    {{-- ── Informasi Akun ── --}}
                                    <p class="section-label"><i class="fas fa-user-circle me-1"></i> Informasi Akun</p>

                                    <div class="row g-3 mb-4">

                                        {{-- Nama --}}
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Nama Lengkap <span class="text-danger">*</span></label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-user input-icon"></i>
                                                <input type="text" name="nama"
                                                    class="form-control form-control-profil @error('nama') is-invalid @enderror"
                                                    value="{{ old('nama', $user->nama) }}"
                                                    placeholder="Masukkan nama lengkap">
                                            </div>
                                            @error('nama')
                                                <div class="invalid-feedback d-block" style="font-size:11px;">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Username --}}
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Username <span class="text-danger">*</span></label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-at input-icon"></i>
                                                <input type="text" name="username"
                                                    class="form-control form-control-profil @error('username') is-invalid @enderror"
                                                    value="{{ old('username', $user->username) }}"
                                                    placeholder="Masukkan username">
                                            </div>
                                            @error('username')
                                                <div class="invalid-feedback d-block" style="font-size:11px;">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>

                                    {{-- ── Kontak WhatsApp ── --}}
                                    <p class="section-label"><i class="fab fa-whatsapp me-1"></i> Nomor WhatsApp Promtor dan Admin</p>

                                    <div class="wa-card mb-4">
                                        <div class="wa-badge">
                                            <i class="fab fa-whatsapp"></i>
                                            WhatsApp
                                        </div>
                                        <div class="row g-3">

                                            {{-- WA 1 --}}
                                            <div class="col-md-6">
                                                <label class="form-label-custom">Nomor Promotor</label>
                                                <div class="input-wrapper">
                                                    <i class="fab fa-whatsapp input-icon" style="color:#25D366;"></i>
                                                    <input type="text" name="wa1"
                                                        class="form-control form-control-profil @error('wa1') is-invalid @enderror"
                                                        value="{{ old('wa1', $admin->wa1 ?? '') }}"
                                                        placeholder="Contoh: 081xxx"
                                                        maxlength="20">
                                                </div>
                                                @error('wa1')
                                                    <div class="invalid-feedback d-block" style="font-size:11px;">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- WA 2 --}}
                                            <div class="col-md-6">
                                                <label class="form-label-custom">Nomor Admin</span></label>
                                                <div class="input-wrapper">
                                                    <i class="fab fa-whatsapp input-icon" style="color:#25D366;"></i>
                                                    <input type="text" name="wa2"
                                                        class="form-control form-control-profil @error('wa2') is-invalid @enderror"
                                                        value="{{ old('wa2', $admin->wa2 ?? '') }}"
                                                        placeholder="Contoh: 081xxx"
                                                        maxlength="20">
                                                </div>
                                                @error('wa2')
                                                    <div class="invalid-feedback d-block" style="font-size:11px;">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                        <div class="wa-note">
                                            <i class="fas fa-info-circle" style="color:#90a4ae; margin-top:2px; flex-shrink:0;"></i>
                                            <span>Nomor ini akan ditampilkan pada aplikasi mobile klien sebagai kontak promotor dan admin.</span>
                                        </div>
                                    </div>

                                    {{-- ── Ganti Password ── --}}
                                    <p class="section-label"><i class="fas fa-lock me-1"></i> Ganti Password</p>

                                    <div class="alert mb-4" role="alert" style="background:#f0faf5; border:1.5px solid #a5d6a7; border-radius:12px; font-size:12px; color:#546e7a; padding:12px 16px;">
                                        <i class="fas fa-shield-alt me-2" style="color:#00AA5B;"></i>
                                        Biarkan kolom password <strong>kosong</strong> jika tidak ingin mengganti password.
                                    </div>

                                    <div class="row g-3 mb-4">

                                        {{-- Password Lama --}}
                                        <div class="col-12">
                                            <label class="form-label-custom">Password Lama</label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-lock input-icon"></i>
                                                <input type="password" name="password_lama" id="passLama"
                                                    class="form-control form-control-profil @error('password_lama') is-invalid @enderror"
                                                    placeholder="Masukkan password lama Anda"
                                                    style="padding-right: 40px;">
                                                <span class="input-eye" onclick="togglePass('passLama', this)">
                                                    <i class="fas fa-eye-slash"></i>
                                                </span>
                                            </div>
                                            @error('password_lama')
                                                <div class="invalid-feedback d-block" style="font-size:11px;">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Password Baru --}}
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Password Baru</label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-key input-icon"></i>
                                                <input type="password" name="password" id="passNew"
                                                    class="form-control form-control-profil @error('password') is-invalid @enderror"
                                                    placeholder="Min. 6 karakter"
                                                    style="padding-right: 40px;"
                                                    oninput="checkStrength(this.value)">
                                                <span class="input-eye" onclick="togglePass('passNew', this)">
                                                    <i class="fas fa-eye-slash"></i>
                                                </span>
                                            </div>
                                            <div class="strength-bar">
                                                <div class="strength-fill" id="strengthFill" style="width:0%;background:#e53935;"></div>
                                            </div>
                                            <div id="strengthLabel" style="font-size:10px;color:#90a4ae;margin-top:4px;"></div>
                                            @error('password')
                                                <div class="invalid-feedback d-block" style="font-size:11px;">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        {{-- Konfirmasi Password --}}
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Konfirmasi Password Baru</label>
                                            <div class="input-wrapper">
                                                <i class="fas fa-check-circle input-icon"></i>
                                                <input type="password" name="password_confirmation" id="passConfirm"
                                                    class="form-control form-control-profil"
                                                    placeholder="Ulangi password baru"
                                                    style="padding-right: 40px;">
                                                <span class="input-eye" onclick="togglePass('passConfirm', this)">
                                                    <i class="fas fa-eye-slash"></i>
                                                </span>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Submit --}}
                                    <div class="d-flex justify-content-end gap-3">
                                        <a href="{{ route('dashboard') }}" class="btn btn-light" style="border-radius:12px; padding: 11px 24px; font-size:14px; font-weight:600; border: 1.5px solid #e0e0e0;">
                                            Batal
                                        </a>
                                        <button type="submit" class="btn btn-simpan">
                                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>
</div>

<script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

<script>
    // Toggle show/hide password
    function togglePass(id, el) {
        const input = document.getElementById(id);
        const icon  = el.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        }
    }

    // Password strength meter
    function checkStrength(val) {
        const fill  = document.getElementById('strengthFill');
        const label = document.getElementById('strengthLabel');
        if (!val) { fill.style.width='0%'; label.textContent=''; return; }

        let score = 0;
        if (val.length >= 6)  score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { pct: '20%', color: '#e53935', text: 'Sangat Lemah' },
            { pct: '40%', color: '#f57c00', text: 'Lemah' },
            { pct: '60%', color: '#fbc02d', text: 'Cukup' },
            { pct: '80%', color: '#43a047', text: 'Kuat' },
            { pct:'100%', color: '#00AA5B', text: 'Sangat Kuat' },
        ];
        const lv = levels[score - 1] || levels[0];
        fill.style.width      = lv.pct;
        fill.style.background = lv.color;
        label.textContent     = lv.text;
        label.style.color     = lv.color;
    }

    // Auto-dismiss success alert after 4s
    setTimeout(function() {
        const al = document.querySelector('.alert-profil-success');
        if (al) { al.style.transition='opacity .5s'; al.style.opacity='0'; setTimeout(()=>al.remove(), 500); }
    }, 4000);
</script>
</body>
</html>