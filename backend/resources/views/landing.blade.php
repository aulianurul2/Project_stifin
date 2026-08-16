<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>STIFIn</title>
  <!--
  TemplateMo 622 Clearwave
  https://templatemo.com/tm-622-clearwave
  Free for personal and commercial use
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,300&family=Playfair+Display:ital,wght@0,700;1,600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/templatemo-622-clearwave.css') }}" />
  <style>
  :root {
    --bg-light: #f2f8f6;
    --primary-green: #136353;
    --accent-cyan: #30b9d4;
    --text-dark: #1e293b;
    --text-muted: #64748b;
    --card-bg: #ffffff;
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Inter, sans-serif;
  }

  body {
    background-color: var(--bg-light);
    color: var(--text-dark);
  }

  .hero-section {
    max-width: 1200px;
    margin: 0 auto;
    padding: 60px 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: center;
  }

  .hero-left {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .badge-rating {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 0.875rem;
    color: var(--text-muted);
    width: fit-content;
  }

  .badge-rating strong {
    color: var(--primary-green);
  }

  .hero-title {
    font-size: 3.2rem;
    line-height: 1.15;
    font-weight: 800;
  }

  .hero-title span {
    font-style: italic;
    font-weight: 400;
    color: var(--primary-green);
  }

  .hero-desc {
    font-size: 1.1rem;
    color: var(--text-muted);
  }

  .hero-buttons {
    display: flex;
    gap: 15px;
    margin-top: 10px;
  }

  .btn-primary {
    background-color: var(--primary-green);
    color: #fff;
    padding: 14px 28px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
  }

  .btn-secondary {
    background-color: transparent;
    border: 1px solid #cbd5e1;
    color: var(--text-dark);
    padding: 14px 28px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
  }

  .trust-badges {
    display: flex;
    gap: 20px;
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 15px;
  }

  .hero-right {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-width: 460px;
    margin: 0 auto;
    width: 100%;
  }

  .promoter-photo-card {
  width: fit-content;
  max-width: 100%;
  margin: 0 auto;
  padding: 10px;
  background: var(--card-bg, #ffffff);
  border-radius: 18px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
  display: flex;
  justify-content: center;
}

.promoter-photo-card img {
  width: 100%;
  max-width: 380px;
  height: auto;
  border-radius: 14px;
  display: block;
  object-fit: cover;
}

.promoter-info-card {
  width: 100%;
  max-width: 400px;
  margin: 12px auto 0 auto;
  box-sizing: border-box;
  background: var(--card-bg, #ffffff);
  border-radius: 14px;
  padding: 16px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
  border-left: 4px solid var(--primary-green, #30b9d4);
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.promoter-tag {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  font-weight: 700;
  color: var(--accent-cyan, #30b9d4);
}

.promoter-name {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--text-dark, #222222);
}

.promoter-description {
  font-size: 0.85rem;
  color: var(--text-muted, #666666);
  line-height: 1.4;
}

@media (max-width: 900px) {
  .hero-section {
    grid-template-columns: 1fr;
  }

  .promoter-photo-card img,
  .promoter-info-card {
    max-width: 100%;
  }
}

html {
  scroll-behavior: smooth;
}

#pricing {
  max-width: 1000px;
  margin: 0 auto;
  padding: 40px 24px;
  font-family: system-ui, -apple-system, sans-serif;
  scroll-margin-top: 80px;
}

/* Header Style */
.stifin-header {
  text-align: center;
  margin-bottom: 40px;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.stifin-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #0d7a5f;
  font-weight: 600;
  background: #e8f5f1;
  border: 1px solid rgba(13, 122, 95, 0.2);
  padding: 6px 16px;
  border-radius: 20px;
  margin-bottom: 16px;
  letter-spacing: 0.5px;
}

.badge-dot {
  font-size: 14px;
  line-height: 1;
}

.stifin-header h2 {
  font-size: 38px;
  font-weight: 800;
  color: #111827;
  line-height: 1.25;
  margin: 0 0 16px 0;
  letter-spacing: -0.5px;
}

.stifin-header h2 i {
  font-style: italic;
  color: #0d7a5f;
  font-weight: 700;
  font-family: Georgia, serif;
}

.stifin-subtitle {
  font-size: 16px;
  font-weight: 400;
  color: #4b5563;
  line-height: 1.6;
  margin: 0;
}

/* Grid Layout */
.stifin-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

/* Cards Style */
.stifin-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.stifin-card-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 15px;
  font-weight: 700;
  margin: 0 0 16px 0;
  color: #111827;
}

.stifin-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.stifin-list li {
  position: relative;
  padding-left: 18px;
  margin-bottom: 8px;
  font-size: 14px;
  color: #475569;
  line-height: 1.5;
}

.stifin-list li::before {
  content: "•";
  color: #0d7a5f;
  font-weight: bold;
  position: absolute;
  left: 0;
}

/* Keunggulan Item Style (Sudah Disesuaikan) */
.stifin-advantage-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.stifin-adv-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  color: #334155;
}

/* Pricing Card Style */
.stifin-price-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  border: 2px solid #0d7a5f;
  position: relative;
}

.price-badge {
  position: absolute;
  top: -12px;
  background: #0d7a5f;
  color: #ffffff;
  font-size: 11px;
  font-weight: 700;
  padding: 2px 10px;
  border-radius: 20px;
}

.price-label {
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  letter-spacing: 0.5px;
}

.price-old {
  font-size: 13px;
  color: #94a3b8;
  margin: 4px 0;
}

.price-old span {
  text-decoration: line-through;
  color: #ef4444;
}

.price-main {
  font-size: 32px;
  font-weight: 800;
  color: #0d7a5f;
}

/* CTA Button */
.stifin-cta-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  background: #0d7a5f;
  color: #ffffff;
  text-decoration: none;
  font-weight: 700;
  padding: 14px;
  border-radius: 10px;
  transition: opacity 0.2s;
  box-sizing: border-box;
}

.stifin-cta-btn:hover {
  opacity: 0.9;
}

/* Responsive View */
@media (max-width: 768px) {
  .stifin-grid-2 {
    grid-template-columns: 1fr;
  }

  .stifin-header h2 {
    font-size: 28px;
  }
  
  .stifin-subtitle {
    font-size: 14px;
  }
}

.stifin-steps-section {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  max-width: 1000px;
  margin: 40px auto;
  padding: 0 24px;
}

.step-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 24px;
  text-align: center;
  transition: border-color 0.2s;
}

.step-card:hover {
  border-color: #0d7a5f;
}

.step-icon {
  width: 56px;
  height: 56px;
  background: #e8f5f1;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
}

.step-number {
  font-size: 12px;
  font-weight: 700;
  color: #0d7a5f;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.step-card h4 {
  font-size: 18px;
  font-weight: 700;
  color: #1e293b;
  margin: 8px 0;
}

.step-card p {
  font-size: 14px;
  color: #64748b;
  line-height: 1.5;
  margin: 0;
}

@media (max-width: 768px) {
  .stifin-steps-section {
    grid-template-columns: 1fr;
  }
}
.stifin-divider {
  border: none;
  height: 1.5px;
  background-color: rgba(13, 122, 95, 0.15); /* Warna hijau tipis */
  
  /* Trik menembus padding parent hingga penuh layar */
  width: 100vw;
  position: relative;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
  
  margin-top: 50px;
  margin-bottom: 50px;
}
/* Pembungkus luar kartu agar tumpuk kebawah di mobile */
.fv-card {
  width: 100%;
  box-sizing: border-box;
  margin-bottom: 16px;
}

/* Item list karir & profesi */
.fv-list-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  width: 100%;
  box-sizing: border-box;
}

/* 1. Pembungkus Utama Kartu (Parent Wrapper) */
.fv-card-grid,
.fv-row,
.fv-container {
  display: flex !important;
  flex-wrap: wrap !important; /* Memaksa kartu turun ke bawah jika layar sempit */
  gap: 16px !important;       /* Jarak antar kartu */
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

/* 2. Pengaturan Kartunya */
.fv-card {
  flex: 1 1 100% !important; /* Di mobile, 1 kartu mengambil 100% lebar layar */
  width: 100% !important;
  max-width: 100% !important;
  box-sizing: border-box !important;
}

/* 3. Khusus Layar Tablet / Desktop (Opsional: Kembali 2 kolom) */
@media (min-width: 768px) {
  .fv-card {
    flex: 1 1 calc(50% - 16px) !important; /* Jadi 2 kolom jika di PC/Tablet */
  }
}
</style>
</head>
<body>

  <!-- ── MOBILE MENU ── -->
  <div class="mobile-menu" id="mobileMenu" role="dialog" aria-modal="true" aria-label="Navigation">
    <a href="#screens">App</a>
    <a href="#features">Features</a>
    <a href="#pricing">Pricing</a>
    <a href="#testimonials">Reviews</a>
    <a href="#integrations">Integrations</a>
    <a href="#faq">FAQ</a>
    <a href="#" class="mobile-cta btn-primary">Start Free Trial</a>
  </div>

  <!-- ── 1. NAV ── -->
  <nav class="nav" id="mainNav" role="navigation" aria-label="Main navigation">
    <div class="nav-inner">
      <a href="#" class="nav-logo">SIM-<span>STIFIn</span></a>
      <ul class="nav-links" role="list">
        <li><a href="#screens">App</a></li>
        <li><a href="#features">Profil Kecerdasan</a></li>
        <li><a href="#pricing"> Biaya dan Paket</a></li>
        <!-- <li><a href="#faq">FAQ</a></li> -->
      </ul>
      <div class="nav-cta">
        <a href="#" class="btn-primary">Unduh Aplikasi</a>
      </div>
      <button class="nav-hamburger" id="hamburger" aria-label="Toggle menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </nav>

  <!-- ── 2. HERO ── -->
  <section class="hero">
    <div class="container">
      <div class="hero-inner">
        <div class="hero-content">
          <div class="hero-badge reveal">
            <div class="hero-badge-dot">✦</div>
            <span>Rated <strong>Akurasi Genetik 95%</strong> Cukup 1x Seumur Hidup</span>
          </div>
          <h1 class="hero-title reveal reveal-delay-1">
            Unlocking Your<br><em>Genetic Potential</em>
          </h1>
          <p class="hero-sub reveal reveal-delay-2">
            Bersama Kami, Temukan Jati Diri Anda Lewat Metode STIFIn.<br>Unduh Aplikasi SIM-STIFIn sekarang untuk melakukan pedaftaran tes secara mudah dan cepat.
          </p>
          <div class="hero-actions reveal reveal-delay-3">
            <a href="#" class="btn-primary-lg">
              Unduh Aplikasi STIFIn
              <span class="btn-arrow">→</span>
            </a>
            <a href="#screens" class="btn-outline-lg">
              <span>▶</span> Lihat Fitur Aplikasi
            </a>
          </div>
          <div class="hero-trust reveal reveal-delay-4">
            <div class="trust-item">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
              8+ Kota yang ditangani
            </div>
            <div class="trust-divider"></div>
            <div class="trust-item">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Subang, Jawa Barat
          </div>
            <div class="trust-divider"></div>
            <div class="trust-item">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              260+ Klien yang telah tes
            </div>
          </div>
        </div>
        <div class="hero-right">
      <div class="promoter-photo-card">
        <img src="{{ asset('assets/img/profil.jpeg') }}" alt="A. Yulia Rosdiana - Licensed Promotor STIFIn">
      </div>

      <div class="promoter-info-card">
        <span class="promoter-tag">Promotor Resmi</span>
        <div class="promoter-name">A. Yulia Rosdiana</div>
        <p class="promoter-description">
          Khusus untuk layanan tes dan konsultasi STIFIn pada cabang Cimahi yang berlokasi di Subang, Jawa Barat, didampingi langsung oleh <strong>A Yulia Rosdiana - Licensed Promotor STIFIn </strong> resmi.
        </p>
      </div>
    </div>
  </section>

 <!-- ── 3. LOGO TICKER ── -->
<div class="ticker-section">
  <div class="ticker-label">Dipercaya dan Bekerja Sama Dengan</div>
  <div class="ticker-track-wrap">
    <div class="ticker-track">
      <!-- Set 1 -->
      <div class="ticker-item">
        <div class="ticker-item-icon">
          <!-- Ikon DNA / Genetik -->
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 15c6.667-6 13.333 0 20-6"/><path d="M2 9c6.667 6 13.333 0 20 6"/><path d="M9 22c0-4 1.5-7.5 3-10"/><path d="M15 2c0 4-1.5 7.5-3 10"/></svg>
        </div>STIFIn Genetik Indonesia
      </div>
      <div class="ticker-dot"></div>

      <div class="ticker-item">
        <div class="ticker-item-icon">
          <!-- Ikon Gedung / Lembaga / Institute -->
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>STIFIn Institute
      </div>
      <div class="ticker-dot"></div>

      <div class="ticker-item">
        <div class="ticker-item-icon">
          <!-- Ikon Lokasi / Cabang -->
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>STIFIn Cabang Cimahi
      </div>
      <div class="ticker-dot"></div>

      <div class="ticker-item">
        <div class="ticker-item-icon">
          <!-- Ikon Keseimbangan / Selaras -->
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 0 0 20 5 5 0 0 1 0-10 5 5 0 0 0 0-10z"/><circle cx="12" cy="7" r="1"/><circle cx="12" cy="17" r="1"/></svg>
        </div>STIFIn Selaras
      </div>
      <div class="ticker-dot"></div>

      <div class="ticker-item">
        <div class="ticker-item-icon">
          <!-- Ikon Keluarga / Family -->
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>Family Happiness STIFIn
      </div>
      <div class="ticker-dot"></div>

      <!-- Set 2 (duplikasi agar animasi looping seamless) -->
      <div class="ticker-item">
        <div class="ticker-item-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 15c6.667-6 13.333 0 20-6"/><path d="M2 9c6.667 6 13.333 0 20 6"/><path d="M9 22c0-4 1.5-7.5 3-10"/><path d="M15 2c0 4-1.5 7.5-3 10"/></svg>
        </div>STIFIn Genetik Indonesia
      </div>
      <div class="ticker-dot"></div>

      <div class="ticker-item">
        <div class="ticker-item-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div>STIFIn Institute
      </div>
      <div class="ticker-dot"></div>

      <div class="ticker-item">
        <div class="ticker-item-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>STIFIn Cabang Cimahi
      </div>
      <div class="ticker-dot"></div>

      <div class="ticker-item">
        <div class="ticker-item-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 0 0 20 5 5 0 0 1 0-10 5 5 0 0 0 0-10z"/><circle cx="12" cy="7" r="1"/><circle cx="12" cy="17" r="1"/></svg>
        </div>STIFIn Selaras
      </div>
      <div class="ticker-dot"></div>

      <div class="ticker-item">
        <div class="ticker-item-icon">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>Family Happiness STIFIn
      </div>
      <div class="ticker-dot"></div>
    </div>
  </div>
</div>

  <!-- ── 5. 3D PHONE CAROUSEL ── -->
  <section class="carousel-section" id="screens">
    <div class="container">
      <div class="carousel-header">
        <div class="section-label reveal">Mobile App</div>
        <h2 class="section-title reveal reveal-delay-1">Daftar Tes STIFIn,<br><em>Langsung dari HP Anda</em></h2>
        <p class="section-sub reveal reveal-delay-2">Aplikasi STIFIn Expert memudahkan Anda mengisi registrasi klien, memilih lokasi & jadwal tes, hingga memantau hasil tes genetik secara praktis dalam satu genggaman.</p>
      </div>
    </div>
    <div class="carousel-zoom">
      <button class="zoom-btn" id="zoomOut" aria-label="Zoom out">−</button>
      <div class="zoom-pips" id="zoomPips"></div>
      <button class="zoom-btn" id="zoomIn" aria-label="Zoom in">+</button>
    </div>

    <div class="carousel-stage" id="carouselStage">
      <div class="carousel-track" id="carouselTrack">

        <!-- Phone screens: 5 different screen designs + extras for looping -->
        <!-- We will render 7 cards and cycle positions -->

        <div class="phone-card" data-pos="left2" data-index="0">
          <div class="phone-shell">
            <div class="phone-screen">
              <img src="{{ asset('assets/img/registrasi.jpeg') }} " alt="App screen 1" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy" />
            </div>
          </div>

        </div>

        <div class="phone-card" data-pos="left1" data-index="1">
          <div class="phone-shell">
            <div class="phone-screen">
              <img src="{{ asset('assets/img/landing.jpeg') }} " alt="App screen 2" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy" />
            </div>
          </div>

        </div>

        <div class="phone-card" data-pos="center" data-index="2">
          <div class="phone-shell">
            <div class="phone-screen">
              <img src="{{ asset('assets/img/fitur.jpeg') }} " alt="App screen 3" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy" />
            </div>
          </div>

        </div>

        <div class="phone-card" data-pos="right1" data-index="3">
          <div class="phone-shell">
            <div class="phone-screen">
             <img src="{{ asset('assets/img/daftar.jpeg') }} " alt="App screen 3" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy" />
            </div>
          </div>

        </div>

        <div class="phone-card" data-pos="right2" data-index="4">
          <div class="phone-shell">
            <div class="phone-screen">
              <img src="{{ asset('assets/img/riwayat.jpeg') }} " alt="App screen 5" style="width:100%;height:100%;object-fit:cover;display:block;" loading="lazy" />
            </div>
          </div>

        </div>

      </div>
    </div>

    <div style="display:flex;align-items:center;justify-content:center;gap:24px;width:100%;margin-top:48px;">
      <button class="carousel-btn" id="carouselPrev" aria-label="Previous screen">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      </button>
      <div class="carousel-dots" id="carouselDots"></div>
      <button class="carousel-btn" id="carouselNext" aria-label="Next screen">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
      </button>
    </div>
  </section>

  <!-- ── 4. FEATURES ── -->
  <section class="features-section" id="features">
    <div class="container">
      <div class="features-header">
        <div class="section-label reveal">5 Mesin Kecerdasan</div>
        <h2 class="section-title reveal reveal-delay-1">Kenali <em>Dirimu</em><br>Melalui 5 Mesin Kecerdasan</h2>
        <p class="section-sub reveal reveal-delay-2">Kenali Dirimu, Maksimalkan Potensimu.
Temukan bakat alami, gaya belajar, karier, dan hubungan yang lebih harmonis melalui Tes STIFIn.</p>
      </div>

      <!-- Feature 1 -->
      <div class="feature-row">
        <div class="feature-content reveal">
          <div class="feature-number">01 — Sensing (S)</div>
          <h3 class="feature-title">Praktis, Konkret, & Berbasis Data</h3>
          <p class="feature-desc"> Digerakkan oleh fungsi otak kiri bawah yang mengutamakan memori, fakta lapangan, panca indera, dan eksekusi nyata.</p>
          <div class="feature-checklist">
            <div class="feature-check"><div class="check-icon">✓</div><span>Gaya belajar praktis (hands-on) & latihan soal berulang</span></div>
            <div class="feature-check"><div class="check-icon">✓</div><span>Komunikasi tegas, jelas, dan to the point</span></div>
            <div class="feature-check"><div class="check-icon">✓</div><span>Kunci Sukses: Kedisiplinan tinggi, pembiasaan rutin, dan eksekusi nyata</span></div>
          </div>
        </div>
        <div class="feature-visual reveal reveal-delay-1">
  <div class="feature-visual-inner">
    <div class="fv-row">
      <!-- Kartu Karakter Utama -->
      <div class="fv-card">
        <div class="fv-card-label">Mesin Kecerdasan</div>
        <div class="fv-card-val" style="color:var(--primary-green,#136353); font-size:1.1rem; font-weight:700; margin-top:4px;">Memori & Fisik</div>
        <div class="fv-card-bar" style="margin-top:8px;"><div class="fv-card-bar-fill" style="width:95%"></div></div>
      </div>
      
      <!-- Kartu Role Kunci -->
      <div class="fv-card">
        <div class="fv-card-label">Peran Ideal</div>
        <div class="fv-card-val" style="color:var(--text-dark,#1e293b); font-size:1.1rem; font-weight:700; margin-top:4px;">Eksekutor Hebat</div>
        <div class="fv-card-bar" style="margin-top:8px;"><div class="fv-card-bar-fill" style="width:88%"></div></div>
      </div>
    </div>

    <!-- Kartu Karir & Karakter Unggulan -->
    <div class="fv-card">
      <div class="fv-card-label">Bidang & Profesi Unggulan</div>
      <div class="fv-list" style="margin-top:10px">
        <div class="fv-list-item">
          <span class="fv-list-name">🏃 Atlet & Olahragawan</span>
          <span class="fv-pill green">Sangat Cocok</span>
        </div>
        <div class="fv-list-item">
          <span class="fv-list-name">💼 Pebisnis Praktis / Praktisi</span>
          <span class="fv-pill green">Unggulan</span>
        </div>
        <div class="fv-list-item">
          <span class="fv-list-name">📊 Akuntan & Logistik</span>
          <span class="fv-pill blue">Rekomendasi</span>
        </div>
      </div>
    </div>
  </div>
</div>
      </div>

      <!-- Feature 02: Thinking (T) -->
<div class="feature-row reverse">
  <div class="feature-content reveal">
    <div class="feature-number">02 — THINKING (T)</div>
    <h3 class="feature-title">Analitis, Logis, & Terstruktur</h3>
    <p class="feature-desc">Digerakkan oleh fungsi otak kiri atas yang menitikberatkan pada kelogisan, analisis data mendalam, dan efisiensi sistem.</p>
    <div class="feature-checklist">
      <div class="feature-check"><div class="check-icon">✓</div><span>Gaya belajar analisis data, skema, dan alur terstruktur</span></div>
      <div class="feature-check"><div class="check-icon">✓</div><span>Komunikasi objektif, efisien, dan berbasis argumen rasional</span></div>
      <div class="feature-check"><div class="check-icon">✓</div><span>Kunci Sukses: Penguasaan standar operasional, logika teruji, dan efisiensi</span></div>
    </div>
  </div>
  <div class="feature-visual reveal reveal-delay-1">
    <div class="feature-visual-inner">
      <div class="fv-row">
        <div class="fv-card">
          <div class="fv-card-label">Mesin Kecerdasan</div>
          <div class="fv-card-val" style="color:var(--primary-green,#136353); font-size:1.05rem; font-weight:700; margin-top:4px;">Logika & Sistem</div>
          <div class="fv-card-bar" style="margin-top:8px;"><div class="fv-card-bar-fill" style="width:92%"></div></div>
        </div>
        <div class="fv-card">
          <div class="fv-card-label">Peran Ideal</div>
          <div class="fv-card-val" style="color:var(--text-dark,#1e293b); font-size:1.05rem; font-weight:700; margin-top:4px;">Evaluator & Planner</div>
          <div class="fv-card-bar" style="margin-top:8px;"><div class="fv-card-bar-fill" style="width:85%"></div></div>
        </div>
      </div>
      <div class="fv-card">
        <div class="fv-card-label">Bidang & Profesi Unggulan</div>
        <div class="fv-list" style="margin-top:10px">
          <div class="fv-list-item"><span class="fv-list-name">💻 Software Engineer / Analyst</span><span class="fv-pill green">Sangat Cocok</span></div>
          <div class="fv-list-item"><span class="fv-list-name">📊 Manajer Keuangan / CFO</span><span class="fv-pill green">Unggulan</span></div>
          <div class="fv-list-item"><span class="fv-list-name">⚖️ Konsultan Hukum / Peneliti</span><span class="fv-pill blue">Rekomendasi</span></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Feature 03: Intuiting (I) -->
<div class="feature-row">
  <div class="feature-content reveal">
    <div class="feature-number">03 — INTUITING (I)</div>
    <h3 class="feature-title">Kreatif, Inovatif, & Visioner</h3>
    <p class="feature-desc">Digerakkan oleh fungsi otak kanan atas yang mengutamakan daya cipta dan ide-ide besar masa depan.</p>
    <div class="feature-checklist">
      <div class="feature-check"><div class="check-icon">✓</div><span>Gaya belajar pola visual, mind mapping, dan eksperimen ide</span></div>
      <div class="feature-check"><div class="check-icon">✓</div><span>Komunikasi inspiratif, berorientasi masa depan, dan inovatif</span></div>
      <div class="feature-check"><div class="check-icon">✓</div><span>Kunci Sukses: Ruang kreasi bebas, riset ide baru, dan pemikiran jangka panjang</span></div>
    </div>
  </div>
  <div class="feature-visual reveal reveal-delay-1">
    <div class="feature-visual-inner">
      <div class="fv-row">
        <div class="fv-card">
          <div class="fv-card-label">Mesin Kecerdasan</div>
          <div class="fv-card-val" style="color:var(--primary-green,#136353); font-size:1.05rem; font-weight:700; margin-top:4px;">Kreativitas & Abstrak</div>
          <div class="fv-card-bar" style="margin-top:8px;"><div class="fv-card-bar-fill" style="width:96%"></div></div>
        </div>
        <div class="fv-card">
          <div class="fv-card-label">Peran Ideal</div>
          <div class="fv-card-val" style="color:var(--text-dark,#1e293b); font-size:1.05rem; font-weight:700; margin-top:4px;">Inovator & Konseptor</div>
          <div class="fv-card-bar" style="margin-top:8px;"><div class="fv-card-bar-fill" style="width:90%"></div></div>
        </div>
      </div>
      <div class="fv-card">
        <div class="fv-card-label">Bidang & Profesi Unggulan</div>
        <div class="fv-list" style="margin-top:10px">
          <div class="fv-list-item"><span class="fv-list-name">🎨 Creative Director & Desainer</span><span class="fv-pill green">Sangat Cocok</span></div>
          <div class="fv-list-item"><span class="fv-list-name">🚀 Entrepreneur & Business Designer</span><span class="fv-pill green">Unggulan</span></div>
          <div class="fv-list-item"><span class="fv-list-name">🎬 Sineas & Penulis Skenario</span><span class="fv-pill blue">Rekomendasi</span></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Feature 04: Feeling (F) -->
<div class="feature-row reverse">
  <div class="feature-content reveal">
    <div class="feature-number">04 — FEELING (F)</div>
    <h3 class="feature-title">Empatis, Komunikatif, & Pemimpin</h3>
    <p class="feature-desc">Digerakkan oleh fungsi otak kanan bawah yang berfokus pada emosi, hubungan antarmanusia, dan kemampuan merangkul sesama.</p>
    <div class="feature-checklist">
      <div class="feature-check"><div class="check-icon">✓</div><span>Gaya belajar diskusi kelompok, metode audio, dan narasi cerita</span></div>
      <div class="feature-check"><div class="check-icon">✓</div><span>Komunikasi persuasif, hangat, dan mengedepankan empati</span></div>
      <div class="feature-check"><div class="check-icon">✓</div><span>Kunci Sukses: Lingkungan sosial yang mendukung, pengaruh positif, dan empati</span></div>
    </div>
  </div>
  <div class="feature-visual reveal reveal-delay-1">
    <div class="feature-visual-inner">
      <div class="fv-row">
        <div class="fv-card">
          <div class="fv-card-label">Mesin Kecerdasan</div>
          <div class="fv-card-val" style="color:var(--primary-green,#136353); font-size:1.05rem; font-weight:700; margin-top:4px;">Emosi & Hubungan</div>
          <div class="fv-card-bar" style="margin-top:8px;"><div class="fv-card-bar-fill" style="width:94%"></div></div>
        </div>
        <div class="fv-card">
          <div class="fv-card-label">Peran Ideal</div>
          <div class="fv-card-val" style="color:var(--text-dark,#1e293b); font-size:1.05rem; font-weight:700; margin-top:4px;">Leader & Persuader</div>
          <div class="fv-card-bar" style="margin-top:8px;"><div class="fv-card-bar-fill" style="width:89%"></div></div>
        </div>
      </div>
      <div class="fv-card">
        <div class="fv-card-label">Bidang & Profesi Unggulan</div>
        <div class="fv-list" style="margin-top:10px">
          <div class="fv-list-item"><span class="fv-list-name">🗣️ Pemimpin & Public Speaker</span><span class="fv-pill green">Sangat Cocok</span></div>
          <div class="fv-list-item"><span class="fv-list-name">🤝 HRD & Psikolog Klinis</span><span class="fv-pill green">Unggulan</span></div>
          <div class="fv-list-item"><span class="fv-list-name">📢 Politisi & Public Relations</span><span class="fv-pill blue">Rekomendasi</span></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Feature 05: Instinct (In) -->
<div class="feature-row">
  <div class="feature-content reveal">
    <div class="feature-number">05 — INSTINCT (IN)</div>
    <h3 class="feature-title">Naluri, Multitalenta, Cepat Tanggap</h3>
    <p class="feature-desc">Digerakkan oleh fungsi otak tengah yang mengutamakan insting, kemampuan serba bisa, serta adaptasi yang spontan.</p>
    <div class="feature-checklist">
      <div class="feature-check"><div class="check-icon">✓</div><span>Gaya belajar rangkuman cepat, fleksibel, dan suasana tenang</span></div>
      <div class="feature-check"><div class="check-icon">✓</div><span>Komunikasi to the point, penengah konflik, dan adil</span></div>
      <div class="feature-check"><div class="check-icon">✓</div><span>Kunci Sukses: Ketenangan emosional, ketulusan berkontribusi, dan kecepatan beradaptasi</span></div>
    </div>
  </div>
  <div class="feature-visual reveal reveal-delay-1">
    <div class="feature-visual-inner">
      <div class="fv-row">
        <div class="fv-card">
          <div class="fv-card-label">Mesin Kecerdasan</div>
          <div class="fv-card-val" style="color:var(--primary-green,#136353); font-size:1.05rem; font-weight:700; margin-top:4px;">Naluri & Serba Bisa</div>
          <div class="fv-card-bar" style="margin-top:8px;"><div class="fv-card-bar-fill" style="width:95%"></div></div>
        </div>
        <div class="fv-card">
          <div class="fv-card-label">Peran Ideal</div>
          <div class="fv-card-val" style="color:var(--text-dark,#1e293b); font-size:1.05rem; font-weight:700; margin-top:4px;">Generalis & Penengah</div>
          <div class="fv-card-bar" style="margin-top:8px;"><div class="fv-card-bar-fill" style="width:91%"></div></div>
        </div>
      </div>
      <div class="fv-card">
        <div class="fv-card-label">Bidang & Profesi Unggulan</div>
        <div class="fv-list" style="margin-top:10px">
          <div class="fv-list-item"><span class="fv-list-name">⚡ Pertolongan Darurat / Medis</span><span class="fv-pill green">Sangat Cocok</span></div>
          <div class="fv-list-item"><span class="fv-list-name">⚖️ Mediator & Activist</span><span class="fv-pill green">Unggulan</span></div>
          <div class="fv-list-item"><span class="fv-list-name">🛠️ Project Manager & Generalist</span><span class="fv-pill blue">Rekomendasi</span></div>
        </div>
      </div>
    </div>
  </div>
</div>
    </div>
  </section>
<hr class="stifin-divider">
  <!-- ── 6. ALUR STIFIn ── -->
<div class="stifin-steps-section">
  <div class="step-card">
    <div class="step-icon">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0d7a5f" stroke-width="2"><path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 4.12a21.86 21.86 0 00.518-3.62"/></svg>
    </div>
    <span class="step-number">Langkah 1</span>
    <h4>Scan Sidik Jari</h4>
    <p>Proses pemindaian 10 ujung jari yang cepat, aman, dan hanya membutuhkan waktu beberapa menit.</p>
  </div>

  <div class="step-card">
    <div class="step-icon">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0d7a5f" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </div>
    <span class="step-number">Langkah 2</span>
    <h4>Analisis & Hasil</h4>
    <p>Data dikirim ke server pusat STIFIn untuk menentukan 1 dari 5 Mesin Kecerdasan secara presisi.</p>
  </div>

  <div class="step-card">
    <div class="step-icon">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0d7a5f" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <span class="step-number">Langkah 3</span>
    <h4>Sesi Konsultasi</h4>
    <p>Penjelasan privat bersama Promotor STIFIn untuk menggali potensi, gaya belajar, dan arahan karier.</p>
  </div>
</div>

<hr class="stifin-divider">

  <!-- Main Container dengan ID pricing -->
<div id="pricing" class="stifin-container">
  
  <!-- Section Header (Tengah & Besar) -->
  <div class="stifin-header">
    <span class="stifin-badge">
      <span class="badge-dot">•</span> BIAYA & PAKET
    </span>
    <h2>Ingin Tahu <i>Gaya Belajar Efektif, Minat & Bakat</i> Anak?</h2>
    <p class="stifin-subtitle">
      STIFIn Genetic Punya Jawabannya! Temukan bakat alami, gaya belajar, karier, dan hubungan yang lebih harmonis melalui Tes STIFIn.
    </p>
  </div>

  <!-- Section Top Grid (Apa Untungnya & Keunggulan) -->
  <div class="stifin-grid-2">
    <div class="stifin-card">
      <h3 class="stifin-card-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0d7a5f" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        APA UNTUNGNYA?
      </h3>
      <ul class="stifin-list">
        <li>Mengenali kepribadian diri</li>
        <li>Mengetahui minat dan bakat</li>
        <li>Menentukan jurusan dan profesi</li>
        <li>Mengetahui gaya belajar dan berkomunikasi efektif</li>
      </ul>
    </div>

    <!-- Keunggulan (Warna Disesuaikan) -->
    <div class="stifin-card">
      <h3 class="stifin-card-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0d7a5f" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
        KEUNGGULAN
      </h3>
      <div class="stifin-advantage-list">
        <div class="stifin-adv-item">
          <span>Simpel</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0d7a5f" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
        </div>
        <div class="stifin-adv-item">
          <span>Aplikatif</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0d7a5f" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        </div>
        <div class="stifin-adv-item">
          <span>Akurat</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0d7a5f" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
        </div>
      </div>
    </div>
  </div>

  <!-- Section Bottom Grid (Harga & Bonus) -->
  <div class="stifin-grid-2">
    <div class="stifin-card stifin-price-card">
      <span class="price-badge">HEMAT 100K</span>
      <span class="price-label">HARGA SPESIAL</span>
      <div class="price-old">Hanya <span>Rp 650.000</span></div>
      <div class="price-main">Rp 550.000</div>
    </div>

    <div class="stifin-card">
      <h3 class="stifin-card-title">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0d7a5f" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
        BONUS SPESIAL
      </h3>
      <ul class="stifin-list">
        <li>Tes STIFIn & Penjelasan hasil tes</li>
        <li>E-Certificate Test STIFIn</li>
        <li>E-Book Personality STIFIn</li>
        <li>Free Konsultasi Online/Offline</li>
      </ul>
    </div>
  </div>

  <!-- Button CTA -->
  <a href="#" class="stifin-cta-btn">
    <span>Daftar Tes STIFIn Sekarang</span>
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
  </a>

</div>

  <!-- ── 8. TESTIMONIALS ──
  <section class="testimonials-section" id="testimonials">
    <div class="container">
      <div class="testimonials-header">
        <div class="section-label reveal">Customer Stories</div>
        <h2 class="section-title reveal reveal-delay-1">Teams that <em>love</em> Clearwave</h2>
        <p class="section-sub reveal reveal-delay-2">Don't take our word for it — here's what real teams say after 90 days.</p>
      </div>
      <div class="testimonials-grid">
        <div class="testimonial-card tall reveal">
          <div class="testimonial-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
          <p class="testimonial-quote">"We replaced three separate tools with Clearwave and actually have fewer meetings now. The automation flows handle the handoffs our team used to spend mornings sorting out. It's the calmest our workflow has ever felt."</p>
          <div class="testimonial-author">
            <div class="author-avatar">SL</div>
            <div>
              <div class="author-name">Sarah Lindqvist</div>
              <div class="author-role">Head of Operations · Stratum IO</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card reveal reveal-delay-1">
          <div class="testimonial-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
          <p class="testimonial-quote">"The mobile app alone justified the switch. I can review dashboards and approve tasks between meetings without opening my laptop."</p>
          <div class="testimonial-author">
            <div class="author-avatar">MR</div>
            <div>
              <div class="author-name">Marcus Reyes</div>
              <div class="author-role">Product Director · Meridian</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card reveal reveal-delay-2">
          <div class="testimonial-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
          <p class="testimonial-quote">"Onboarding our 30-person team took one afternoon. The learning curve is genuinely flat."</p>
          <div class="testimonial-author">
            <div class="author-avatar">PK</div>
            <div>
              <div class="author-name">Priya Kapoor</div>
              <div class="author-role">Engineering Lead · Vanta Labs</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card reveal reveal-delay-1">
          <div class="testimonial-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
          <p class="testimonial-quote">"The reporting features are leagues ahead of what we had. We can finally show stakeholders live data instead of preparing decks."</p>
          <div class="testimonial-author">
            <div class="author-avatar">TW</div>
            <div>
              <div class="author-name">Tom Wainwright</div>
              <div class="author-role">CFO · Pulsar HQ</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card reveal reveal-delay-2">
          <div class="testimonial-stars"><span>★</span><span>★</span><span>★</span><span>★</span><span>★</span></div>
          <p class="testimonial-quote">"Customer support actually reads your message. Had a custom integration question answered in under two hours."</p>
          <div class="testimonial-author">
            <div class="author-avatar">AN</div>
            <div>
              <div class="author-name">Aiko Nakamura</div>
              <div class="author-role">CTO · Nexaflow</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ── 9. INTEGRATIONS ── -->
  <!-- <section class="integrations-section" id="integrations">
    <div class="container">
      <div class="integrations-header">
        <div class="section-label reveal">Integrations</div>
        <h2 class="section-title reveal reveal-delay-1">Connects with your<br><em>existing stack</em></h2>
        <p class="section-sub reveal reveal-delay-2">One-click integrations with the tools your team already uses. No dev work required.</p>
      </div>
      <div class="integrations-grid">
        <div class="integration-tile reveal"><div class="integration-name">Slack</div></div>
        <div class="integration-tile reveal reveal-delay-1"><div class="integration-name">Google Sheets</div></div>
        <div class="integration-tile reveal reveal-delay-2"><div class="integration-name">Google Drive</div></div>
        <div class="integration-tile reveal reveal-delay-3"><div class="integration-name">Zapier</div></div>
        <div class="integration-tile reveal"><div class="integration-name">Stripe</div></div>
        <div class="integration-tile reveal reveal-delay-1"><div class="integration-name">GitHub</div></div>
        <div class="integration-tile reveal reveal-delay-2"><div class="integration-name">Notion</div></div>
        <div class="integration-tile reveal reveal-delay-3"><div class="integration-name">Mailchimp</div></div>
        <div class="integration-tile reveal"><div class="integration-name">HubSpot</div></div>
        <div class="integration-tile reveal reveal-delay-1"><div class="integration-name">Airtable</div></div>
        <div class="integration-tile reveal reveal-delay-2"><div class="integration-name">Intercom</div></div>
        <div class="integration-tile reveal reveal-delay-3"><div class="integration-name">Salesforce</div></div>
        <div class="integration-tile reveal"><div class="integration-name">Figma</div></div>
        <div class="integration-tile reveal reveal-delay-1"><div class="integration-name">Linear</div></div>
        <div class="integration-tile reveal reveal-delay-2"><div class="integration-name">Jira</div></div>
        <div class="integration-tile reveal reveal-delay-3"><div class="integration-name">Webflow</div></div>
      </div>
    </div>
  </section> --> -->

  <!-- ── 10. FAQ ──
  <section class="faq-section" id="faq">
    <div class="container">
      <div class="faq-inner">
        <div class="faq-sidebar reveal">
          <div class="section-label">FAQ</div>
          <h2 class="section-title">Questions,<br><em>answered</em></h2>
          <p class="section-sub">Can't find what you're looking for? Reach our team at hello@clearwave.io — we reply within 2 hours.</p>
          <button class="faq-toggle-all" id="faqToggleAll">
            <span id="faqToggleIcon">+</span> Expand all</button>
        </div>
        <div class="faq-list reveal reveal-delay-1" id="faqList">
          <div class="faq-item">
            <div class="faq-question" tabindex="0" role="button" aria-expanded="false">
              Is there a free trial?
              <div class="faq-icon">+</div>
            </div>
            <div class="faq-answer"><div class="faq-answer-inner">Yes — every plan starts with a 14-day free trial, no credit card required. You get full access to all features in your chosen tier so you can make a real evaluation before committing.</div></div>
          </div>
          <div class="faq-item">
            <div class="faq-question" tabindex="0" role="button" aria-expanded="false">
              How does pricing work for larger teams?
              <div class="faq-icon">+</div>
            </div>
            <div class="faq-answer"><div class="faq-answer-inner">Starter and Professional plans are per-workspace, not per-seat — so you won't see surprise bills as your team grows. Enterprise plans are custom-quoted based on your specific needs and contract length.</div></div>
          </div>
          <div class="faq-item">
            <div class="faq-question" tabindex="0" role="button" aria-expanded="false">
              Can I migrate data from another tool?
              <div class="faq-icon">+</div>
            </div>
            <div class="faq-answer"><div class="faq-answer-inner">We support CSV imports and direct migration from Notion, Airtable, Asana, and Trello. Enterprise customers get a dedicated migration specialist who handles the entire process for you.</div></div>
          </div>
          <div class="faq-item">
            <div class="faq-question" tabindex="0" role="button" aria-expanded="false">
              What does the 99.9% uptime SLA mean?
              <div class="faq-icon">+</div>
            </div>
            <div class="faq-answer"><div class="faq-answer-inner">It means Clearwave is contractually committed to less than 8.7 hours of downtime per year. We monitor availability 24/7, post all incidents publicly at status.clearwave.io, and issue credits automatically if SLA is breached.</div></div>
          </div>
          <div class="faq-item">
            <div class="faq-question" tabindex="0" role="button" aria-expanded="false">
              Is my data secure?
              <div class="faq-icon">+</div>
            </div>
            <div class="faq-answer"><div class="faq-answer-inner">Clearwave is SOC 2 Type II certified, GDPR compliant, and ISO 27001 aligned. All data is encrypted in transit and at rest. You can request a full security report from our compliance team at any time.</div></div>
          </div>
          <div class="faq-item">
            <div class="faq-question" tabindex="0" role="button" aria-expanded="false">
              Can I cancel anytime?
              <div class="faq-icon">+</div>
            </div>
            <div class="faq-answer"><div class="faq-answer-inner">Yes. There are no lock-in contracts on monthly plans. Cancel from your account settings at any time and you won't be charged again. Annual plans are non-refundable but can be cancelled to stop renewal.</div></div>
          </div>
        </div>
      </div>
    </div>
  </section> -->

  <!-- ── 11. CTA BANNER ── -->
  <section class="cta-section">
  <div class="container">
    <div class="cta-inner reveal">
      <div class="cta-content">
        <div class="cta-label">✦ Mulai Perjalanan Mengenali Diri</div>
        <h2 class="cta-title">Siap Temukan<br><em>Potensi Genetik Terbaikmu?</em></h2>
        <p class="cta-sub">Bergabunglah dengan ratusan orang yang telah menemukan gaya belajar, bakat alami, arah karir, dan cara berkomunikasi paling efektif bersama STIFIn.</p>
      </div>
      <div class="cta-actions">
        <!-- Pendaftaran Utama via Aplikasi -->
        <a href="#" class="btn-cta-primary">
          Daftar via Aplikasi
          <span>→</span>
        </a>
        
        <!-- Bantuan/Kesulitan via WhatsApp (Ganti nomor 628xxxxxxxxxx sesuai nomor Admin) -->
        <a href="https://wa.me/6282127747105?text=Halo%20Admin,%20saya%20kesulitan%20mendaftar%20tes%20STIFIn%20di%20aplikasi,%20mohon%20bantuannya" target="_blank" class="btn-cta-ghost">
          Butuh Bantuan? Hubungi Admin
        </a>
      </div>
    </div>
  </div>
</section>

  <!-- ── 12. FOOTER ── -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <a href="#" class="nav-logo">SIM - <span style="color:var(--accent-light)">STIFIn</span></a>
          <p class="footer-brand-desc">Temukan bakat alami, gaya belajar, karier, dan hubungan yang lebih harmonis melalui Tes STIFIn.</p>
        </div>
        <div>
          <div class="footer-col-label">Product</div>
          <div class="footer-links">
            <a href="#">About</a>
            <a href="#screens">Mobile App</a>
            <a href="#features">Profil Kecerdasan</a>
            <a href="#pricing">Biaya dan Paket</a>
            
          </div>
        </div>
        <div>
          
        <div>
  <div class="footer-col-label">Media Sosial & Kontak</div>
  <div class="footer-links">
    <!-- Alamat (Google Maps) -->
    <a href="https://maps.app.goo.gl/j9SSbUhxPGUeZwM5A?g_st=ic" target="_blank" rel="noopener noreferrer">
      <i class="fa-solid fa-location-dot"></i> Alamat Kami
    </a>

    <!-- Instagram -->
    <a href="https://instagram.com/a_yuliarosdiana" target="_blank" rel="noopener noreferrer">
      <i class="fa-brands fa-instagram"></i> a_yuliarosdiana
    </a>
    <a href="https://instagram.com/familyhappiness_stifin" target="_blank" rel="noopener noreferrer">
      <i class="fa-brands fa-instagram"></i> familyhappiness_stifin
    </a>

    <!-- TikTok -->
    <a href="https://tiktok.com/@daynrosdiana" target="_blank" rel="noopener noreferrer">
      <i class="fa-brands fa-tiktok"></i> daynrosdiana
    </a>

    <!-- WhatsApp (Ganti nomor dengan format internasional tanpa +) -->
    <a href="https://wa.me/6282127747105?text=Halo,%20saya%20ingin%20bertanya" target="_blank" rel="noopener noreferrer">
      <i class="fa-brands fa-whatsapp"></i> WhatsApp Admin
    </a>
  </div>
</div>
      </div>
     <div class="footer-bottom">
  <div class="footer-copy">
    © 2026 Manik Trisula Design by <a href="profill.html" target="_blank">Manik Trisula</a>
  </div>
</div>
    </div>
  </footer>

  <script src="{{ asset('assets/js/templatemo-622-clearwave.js') }}"></script>
  
</body>
</html>
