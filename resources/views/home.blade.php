{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Basic -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- Site Metas -->
    <title>Beranda - LSP PLGM</title>
    <meta name="keywords" content="">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Site Icons -->
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" type="image/x-icon" />
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!--Bootstrap Icon-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Site Fonts -->
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flaticon.css') }}">
    <!-- Slick Slider CSS -->
    <link rel="stylesheet" href="{{ asset('css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('css/slick-theme.css') }}">
    <!-- PrettyPhoto CSS -->
    <link rel="stylesheet" href="{{ asset('css/prettyPhoto.css') }}">
    <!-- CSS template lama -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <!-- CSS KHUSUS LSP (punyamu yang dipisah) -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <script src="{{ asset('js/modernizr.js') }}"></script>
</head>

<body id="page-top" class="politics_version">
<div class="page-wrapper">

    {{-- TOP BAR --}}
    <nav class="navbar top-navbar py-2">
        <div class="container d-flex justify-content-between align-items-center">

            <!-- Kiri: Sosial Media -->
            <div class="navbar-nav flex-row">
                <a href="#" class="nav-link px-0 pr-3 top-icon">
                    <i class="fa fa-facebook"></i>
                </a>
                <a href="#" class="nav-link px-0 pr-3 top-icon">
                    <i class="fa fa-instagram"></i>
                </a>
                <a href="#" class="nav-link px-0 top-icon">
                    <i class="fa fa-youtube-play"></i>
                </a>
            </div>

            <!-- Kanan: Login -->
            <div class="navbar-nav">
                <a href="{{ route('login') }}" class="nav-link px-0 top-icon">
                    Login
                </a>
            </div>
        </div>
    </nav>
    {{-- END TOP BAR --}}

    {{-- NAVBAR UTAMA --}}
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="#page-top">
                <img class="img-fluid nav-logo" src="{{ asset('images/logo.png') }}" alt="LSP" />
            </a>

            <button class="navbar-toggler navbar-toggler-right" type="button"
                    data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
                    aria-controls="navbarResponsive" aria-expanded="false"
                    aria-label="Toggle navigation">
                Menu <i class="fa fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ms-auto">
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="{{ url('/') }}">Beranda</a>
                    </li>

                    <!-- Dropdown PROFIL -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="profilDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <div class="dropdown-menu" aria-labelledby="profilDropdown">
                            <a class="dropdown-item" href="{{ url('tentang-kami') }}">Tentang LSP</a>
                            <a class="dropdown-item" href="{{ url('visi-misi') }}">Visi &amp; Misi</a>
                            <a class="dropdown-item" href="{{ url('kebijakan-mutu') }}">Kebijakan &amp; Sasaran Mutu</a>
                            <a class="dropdown-item" href="{{ url('struktur-organisasi') }}">Struktur Organisasi</a>
                        </div>
                    </li>

                    <!-- Dropdown SERTIFIKASI -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" id="sertifikasiDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Sertifikasi
                        </a>
                        <div class="dropdown-menu" aria-labelledby="sertifikasiDropdown">
                            <a class="dropdown-item" href="{{ route('skema.index') }}">Skema Sertifikasi</a>
                            <a class="dropdown-item" href="{{ url('tempat-sertifikasi') }}">Tempat Uji Kompetensi</a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="{{ url('berita') }}">Berita</a>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="{{ url('pendaftaran') }}">Daftar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    {{-- END NAVBAR --}}

    {{-- BANNER / HERO --}}
    <div class="ct-header tablex item">
        <img src="{{ asset('images/banner.png') }}"
             alt="Pelatihan Sertifikasi Profesi"
             class="slider-banner-img">
    </div>

    {{-- TENTANG KAMI (HOME) --}}
    <div class="tentang py-5">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-md-6">
                    <img src="{{ asset('images/lab.png') }}"
                         alt="Suasana Laboratorium Komputer"
                         class="img-fluid rounded-4 img-shadow">
                </div>

                <div class="col-md-6">
                    <h1 class="mb-4">Tentang Kami</h1>

                    <p>
                        LPK LP3I Mandiri Center (LMC) adalah lembaga pelatihan kerja non-formal di lingkungan
                        Politeknik LP3I Bandung yang berfokus pada peningkatan kompetensi di bidang komputer
                        dan administrasi perkantoran.
                    </p>
                    <p>
                        Pelatihan disusun berbasis kompetensi dengan pendekatan praktik langsung, dibimbing oleh
                        instruktur berpengalaman, serta didukung fasilitas pelatihan kampus LP3I.
                    </p>
                    <p>
                        LMC berkomitmen membantu peserta menjadi tenaga kerja profesional yang siap bersaing di dunia industri.
                    </p>
                    <p>
                        Legalitas: K.T.03.04.04/0042/LT-01-Disnaker/X/2023, 19 Oktober 2023.
                    </p>
                    <a href="{{ url('tentang-kami') }}" class="btn btn-sm btn-tentang">Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>

    {{-- MENGAPA KAMI --}}
    <div id="services" class="section lb">
        <div class="container">
            <div class="section-title text-center">
                <h2>Mengapa Kami ?</h2>
                <p>
                    Lembaga telah memiliki legalitas resmi dari instansi pemerintah serta mengikuti standar
                    BNSP/ISO 17024, sehingga layanan pelatihan dan sertifikasi dijamin kredibel.
                </p>
            </div>

            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="services-inner-box">
                        <div class="ser-icon">
                            <img src="{{ asset('images/icon-book.png') }}" alt="">
                        </div>
                        <h2>Skema Kredibel</h2>
                        <p>
                            Skema sertifikasi disusun mengacu standar BNSP/ISO 17024
                            sehingga pengakuan kompetensi lebih kuat dan kredibel.
                        </p>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="services-inner-box">
                        <div class="ser-icon">
                            <img src="{{ asset('images/icon-mentor.png') }}" alt="">
                        </div>
                        <h2>Instruktur &amp; Asesor</h2>
                        <p>
                            Dibimbing instruktur dan asesor berpengalaman di industri,
                            dengan pendekatan praktik langsung di laboratorium.
                        </p>
                    </div>
                </div>

                <div class="col-md-4 mb-4">
                    <div class="services-inner-box">
                        <div class="ser-icon">
                            <img src="{{ asset('images/jalur-karir.png') }}" alt="">
                        </div>
                        <h2>Jalur Karier</h2>
                        <p>
                            Sertifikat kompetensi membantu lulusan terserap di dunia kerja
                            dan meningkatkan daya saing profesional.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PROGRAM PELATIHAN --}}
    <div class="section-title text-center mt-4">
        <h2>Program Pelatihan</h2>
        <p>Pilih bidang pelatihan sesuai kebutuhan kompetensi Anda.</p>
    </div>

    <div class="kategori-strip">
        <div class="container d-flex justify-content-between align-items-center px-4">
            <div class="d-flex align-items-center justify-content-center flex-grow-1 kategori-list">
               <button type="button" class="kategori-link active" data-filter="informasi-dan-komunikasi">
                Informasi Dan Komunikasi
                </button>
                <button type="button" class="kategori-link" data-filter="perkantoran-digital">
                Administrasi Profesional
                </button>
                <button type="button" class="kategori-link" data-filter="digital-marketing">
                Pemasaran
                </button>
                <a href="{{ route('skema.index') }}" class="kategori-seeall">Lihat Semua →</a>
            </div>
        </div>
    </div>

    <section class="py-4">
        <div class="container">
            <div class="row g-4">

                {{-- CONTOH: program dari database --}}
                @foreach ($programs as $program)
                    <div class="col-md-4 kategori-item"
                         data-kategori="{{ $program->kategori_slug }}">
                        <div class="card skema-card h-100 shadow-sm border-0">
                           @if ($program->gambar)
                            <img src="{{ asset('storage/' . $program->gambar) }}"
                                 class="card-img-top"
                                 alt="{{ $program->nama }}">
                        @else
                            <img src="{{ asset('images/default-skema.png') }}"
                                 class="card-img-top"
                                 alt="{{ $program->nama }}">
                        @endif

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-1">{{ $program->nama }}</h5>
                                <p class="card-text text-muted small mb-3">
                                    {{ $program->deskripsi_singkat }}
                                </p>
                                <a href="{{ route('skema.show', $program->slug) }}"
                                   class="btn btn-sm btn-primary mt-auto">
                                    Lihat Skema
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- kalau mau contoh statis tetap ada, tinggal hapus foreach di atas
                     dan pakai 3 card statis seperti HTML awalmu --}}
            </div>
        </div>
    </section>

    {{-- ARTIKEL --}}
    <section id="blog" class="section lb">
        <div class="container">
            <div class="section-title text-center">
                <h2>Artikel</h2>
                <p>Update informasi seputar pelatihan dan sertifikasi kompetensi.</p>
            </div>

            <div class="row">
                @foreach ($beritas as $berita)
                    <div class="col-md-4 col-sm-6 mb-4">
                        <a href="{{ route('berita.show', $berita->slug) }}" class="blog-card d-block">
                            <figure class="mb-0">
                                {{-- asumsikan $article->thumbnail menyimpan path storage --}}
                                <img
                                    src="{{ Storage::url($berita->gambar) }}"
                                    alt="{{ $berita->judul }}"
                                    class="img-fluid"
                                />

                                <figcaption>
                                    <h3 class="blog-title">{{ $berita->judul }}</h3>
                                    <p class="blog-excerpt">
                                        {{ $berita->ringkasan }}
                                    </p>
                                </figcaption>
                            </figure>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div> {{-- .page-wrapper --}}

{{-- FOOTER --}}
<footer class="footer-lmc">
    <div class="container">
        <div class="row align-items-center py-4">

            <div class="col-md-4 mb-3 mb-md-0">
                <h5 class="footer-title">Kontak</h5>

                <p class="footer-item">
                    <i class="bi bi-geo-alt-fill me-2"></i>
                    <a href="https://maps.app.goo.gl/JRRkSUwuwzCuUWHt6" target="_blank">
                        Politeknik LP3I Bandung
                    </a>
                </p>

                <p class="footer-item">
                    <i class="bi bi-whatsapp me-2"></i>
                    <a href="https://wa.me/6285220199772" target="_blank">
                        0852-2019-9772
                    </a>
                </p>
            </div>

            <div class="col-md-4 text-center mb-3 mb-md-0">
                <img src="{{ asset('images/logo.png') }}" width="120" alt="LSP Logo" class="footer-logo">
            </div>

            <div class="col-md-4 text-md-right text-center">
                <div class="footer-sosmed">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                    <a href="#"><i class="bi bi-youtube"></i></a>
                </div>
            </div>

        </div>
    </div>
</footer>

<div class="footer-copy" style="margin-bottom: 0; padding-bottom: 0;">
    <div class="container-fluid text-center py-2">
        <p class="m-0">© {{ date('Y') }} LSP PLGM. All Rights Reserved.</p>
    </div>
</div>

<a href="#" id="scroll-to-top" class="dmtop global-radius">
    <i class="fa fa-angle-up"></i>
</a>

{{-- jQuery (for legacy plugins) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

{{-- Bootstrap 5 Bundle (includes Popper) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

{{-- Other legacy JS plugins --}}
<script src="{{ asset('js/jquery.mobile.customized.min.js') }}"></script>
<script src="{{ asset('js/jquery.easing.1.3.js') }}"></script>
<script src="{{ asset('js/parallaxie.js') }}"></script>
<script src="{{ asset('js/slick.min.js') }}"></script>
<script src="{{ asset('js/animated-slider.js') }}"></script>
<script src="{{ asset('js/jqBootstrapValidation.js') }}"></script>
<script src="{{ asset('js/contact_me.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>

<script>
    // script JS-mu tetap sama, cukup tempel di sini
    // (aku tidak ubah isinya)
    const navToggle = document.querySelector('.mobile-nav-toggle');
    const navMenu = document.querySelector('#navbar ul');


    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('show');

            if (navMenu.classList.contains('show')) {
                navToggle.classList.remove('bi-list');
                navToggle.classList.add('bi-x');
            } else {
                navToggle.classList.remove('bi-x');
                navToggle.classList.add('bi-list');
            }
        });
    }

    document.querySelectorAll('#navbar a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 991 && link.parentElement.classList.contains('dropdown')) {
                return;
            }
            if (navMenu && navMenu.classList.contains('show')) {
                navMenu.classList.remove('show');
                navToggle.classList.remove('bi-x');
                navToggle.classList.add('bi-list');
            }
        });
    });

    document.querySelectorAll('#navbar .dropdown > a').forEach(trigger => {
        trigger.addEventListener('click', function (e) {
            if (window.innerWidth <= 991) {
                e.preventDefault();
                const parentLi = this.parentElement;
                document.querySelectorAll('#navbar .dropdown').forEach(li => {
                    if (li !== parentLi) {
                        li.classList.remove('dropdown-active');
                    }
                });
                parentLi.classList.toggle('dropdown-active');
            }
        });
    });

    window.addEventListener('scroll', function () {
        var scrolled = window.scrollY || window.pageYOffset;
        if (scrolled > 80) {
            document.body.classList.add('hide-top-navbar');
        } else {
            document.body.classList.remove('hide-top-navbar');
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const links = document.querySelectorAll('.kategori-link');
        const items = document.querySelectorAll('.kategori-item');

       function filterKategori(filter) {
    links.forEach(link => {
        link.classList.toggle('active', link.dataset.filter === filter);
    });

    items.forEach(item => {
        if (filter === 'all') {
            item.classList.remove('d-none');
            return;
        }

        const kat = (item.dataset.kategori || '').trim();
        const match = (kat === filter);

        item.classList.toggle('d-none', !match);
    });
}


        links.forEach(link => {
            link.addEventListener('click', function () {
                const filter = this.dataset.filter || 'all';
                filterKategori(filter);
            });
        });

        filterKategori('all');
    });

    window.addEventListener("load", function () {
        const pre = document.getElementById("preloader");
        if (pre) pre.style.display = "none";
    });


</script>
</body>
</html>
