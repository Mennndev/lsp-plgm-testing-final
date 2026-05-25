<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pilih Skema Sertifikasi | LSP PLGM</title>

    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon" />
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .pilih-skema-wrapper {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            position: relative;
            overflow: hidden;
        }

        /* Background Pattern Overlay */
        .pilih-skema-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 0;
        }

        .pilih-skema-wrapper::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 400px;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><circle cx="50" cy="50" r="40" fill="white" opacity="0.05"/></svg>');
            z-index: 1;
        }

        .container-wrapper {
            position: relative;
            z-index: 2;
            padding: 40px 0;
        }

        /* Header Section */
        .pilih-skema-header {
            color: white;
            padding: 40px 0 60px;
            margin-bottom: 0;
        }

        .breadcrumb-nav {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 20px;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 25px;
            backdrop-filter: blur(10px);
        }

        .breadcrumb-nav a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .breadcrumb-nav a:hover {
            transform: translateX(-5px);
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 15px;
        }

        .header-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            backdrop-filter: blur(10px);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .header-text h1 {
            margin: 0;
            font-size: 42px;
            font-weight: 800;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
            letter-spacing: -0.5px;
        }

        .header-text p {
            margin: 5px 0 0;
            opacity: 0.95;
            font-size: 18px;
            font-weight: 300;
        }

        /* Stats Counter */
        .stats-counter {
            background: rgba(255, 255, 255, 0.15);
            padding: 15px 30px;
            border-radius: 15px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 15px;
        }

        .stats-counter i {
            font-size: 24px;
        }

        .stats-counter .stats-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .stats-counter .stats-number {
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
        }

        .stats-counter .stats-label {
            font-size: 13px;
            opacity: 0.9;
        }

        /* Search Box */
        .search-box {
            margin: -30px 0 40px;
            position: relative;
            z-index: 3;
        }

        .search-wrapper {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-wrapper i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #999;
            z-index: 1;
        }

        .search-box input {
            width: 100%;
            border-radius: 50px;
            padding: 18px 60px 18px 55px;
            border: none;
            font-size: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            background: white;
        }

        .search-box input:focus {
            outline: none;
            box-shadow: 0 15px 50px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .search-box input::placeholder {
            color: #aaa;
        }

        /* Filter Buttons */
        .filter-section {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 30px;
        }

        .filter-btn {
            padding: 10px 25px;
            border-radius: 50px;
            border: 2px solid #e0e0e0;
            background: white;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 14px;
        }

        .filter-btn:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }

        /* Card Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 40px;
        }

        @media (max-width: 992px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 576px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Skema Card */
        .skema-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .skema-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .skema-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }

        .skema-card:hover::before {
            transform: scaleX(1);
        }

        .card-header-section {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }

        .card-title-section {
            flex: 1;
        }

        .skema-name {
            font-weight: 700;
            color: #2d3748;
            font-size: 18px;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .kode-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .card-body-section {
            flex: 1;
            margin-bottom: 20px;
        }

        .card-description {
            color: #718096;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .card-footer-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: auto;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }

        .status-submitted {
            background: #fff3cd;
            color: #856404;
        }

        .btn-ajukan {
            padding: 12px 28px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-ajukan:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .btn-ajukan i {
            font-size: 16px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .empty-state-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 50px;
            color: white;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .empty-state h3 {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 24px;
        }

        .empty-state p {
            color: #718096;
            font-size: 16px;
        }

        /* Loading State */
        .loading-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .loading-title {
            height: 20px;
            background: #e0e0e0;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .loading-text {
            height: 14px;
            background: #e0e0e0;
            border-radius: 4px;
            width: 60%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-text h1 {
                font-size: 32px;
            }

            .header-icon {
                width: 60px;
                height: 60px;
                font-size: 28px;
            }

            .stats-counter {
                padding: 12px 20px;
            }

            .stats-counter .stats-number {
                font-size: 24px;
            }
        }

        @media (max-width: 576px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-icon {
                margin: 0 auto;
            }

            .header-text h1 {
                font-size: 28px;
            }

            .header-text p {
                font-size: 16px;
            }

            .stats-counter {
                width: 100%;
                justify-content: center;
            }

            .filter-section {
                flex-direction: column;
            }

            .filter-btn {
                width: 100%;
            }
        }

        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <div class="pilih-skema-wrapper">
        <div class="container-wrapper">
            <!-- Header -->
            <div class="pilih-skema-header">
                <div class="container">
                    <!-- Breadcrumb -->
                    <div class="breadcrumb-nav">
                        <a href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i>
                            <span>Kembali ke Dashboard</span>
                        </a>
                    </div>

                    <!-- Header Content -->
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <div class="header-text">
                            <h1>Daftar Skema Sertifikasi</h1>
                            <p>Pilih skema sertifikasi yang sesuai dengan kebutuhan Anda</p>
                        </div>
                    </div>

                    <!-- Stats Counter -->
                    <div class="stats-counter">
                        <i class="bi bi-collection"></i>
                        <div class="stats-text">
                            <div class="stats-number" id="totalSkema">{{ $programs->count() }}</div>
                            <div class="stats-label">Skema Tersedia</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="container">
                <!-- Search Box -->
                <div class="search-box">
                    <div class="search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text"
                               id="searchSkema"
                               class="form-control"
                               placeholder="Cari skema sertifikasi berdasarkan nama atau kode...">
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="filter-section">
                    <button class="filter-btn active" data-filter="all">
                        <i class="bi bi-grid"></i> Semua Skema
                    </button>
                    <button class="filter-btn" data-filter="available">
                        <i class="bi bi-check-circle"></i> Tersedia
                    </button>
                    <button class="filter-btn" data-filter="submitted">
                        <i class="bi bi-clock-history"></i> Sudah Diajukan
                    </button>
                </div>

                <!-- Cards Grid -->
                <div class="cards-grid" id="cardsGrid">
                    @forelse($programs as $program)
                    <div class="skema-card" data-status="{{ in_array($program->id, $pengajuanUser) ? 'submitted' : 'available' }}">
                        <!-- Card Header -->
                        <div class="card-header-section">
                            <div class="card-icon">
                                <i class="bi bi-patch-check"></i>
                            </div>
                            <div class="card-title-section">
                                <div class="skema-name">{{ $program->nama }}</div>
                                <span class="kode-badge">{{ $program->kode_skema }}</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body-section">
                            <div class="card-description">
                                {{ $program->deskripsi_singkat ?? 'Program sertifikasi kompetensi profesional yang diakui secara nasional sesuai standar industri.' }}
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer-section">
                            @if(in_array($program->id, $pengajuanUser))
                                <span class="status-badge status-submitted">
                                    <i class="bi bi-clock-history"></i>
                                    Sudah Diajukan
                                </span>
                            @else
                                <span class="status-badge status-available">
                                    <i class="bi bi-check-circle"></i>
                                    Tersedia
                                </span>
                                <a href="{{ route('pengajuan.create', $program->id) }}"
                                   class="btn-ajukan">
                                    <i class="bi bi-send"></i>
                                    Ajukan
                                </a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <div class="empty-state-icon">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <h3>Belum Ada Skema Tersedia</h3>
                        <p>Saat ini belum ada skema sertifikasi yang dapat diajukan. Silakan cek kembali nanti.</p>
                    </div>
                    @endforelse
                </div>

                <!-- No Results Message (Hidden by default) -->
                <div id="noResults" class="empty-state" style="display: none;">
                    <div class="empty-state-icon">
                        <i class="bi bi-search"></i>
                    </div>
                    <h3>Tidak Ada Hasil</h3>
                    <p>Tidak ditemukan skema sertifikasi yang sesuai dengan pencarian Anda.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>

    <script>
        // State management
        let currentSearchTerm = '';
        let currentFilter = 'all';

        // Helper function to apply both search and filter
        function applyFiltersAndSearch() {
            const cards = document.querySelectorAll('.skema-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                const status = card.getAttribute('data-status');

                // Check if card matches search term
                const matchesSearch = !currentSearchTerm || text.includes(currentSearchTerm);

                // Check if card matches filter
                const matchesFilter = currentFilter === 'all' || currentFilter === status;

                // Show card only if it matches both conditions
                if (matchesSearch && matchesFilter) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update no results display
            const cardsGrid = document.getElementById('cardsGrid');
            const noResults = document.getElementById('noResults');

            if (visibleCount === 0 && cards.length > 0) {
                cardsGrid.style.display = 'none';
                noResults.style.display = 'block';
            } else {
                cardsGrid.style.display = 'grid';
                noResults.style.display = 'none';
            }
        }

        // Search functionality
        const searchInput = document.getElementById('searchSkema');

        searchInput.addEventListener('keyup', function() {
            currentSearchTerm = this.value.toLowerCase();
            applyFiltersAndSearch();
        });

        // Filter functionality
        const filterButtons = document.querySelectorAll('.filter-btn');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Update active state
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                currentFilter = this.getAttribute('data-filter');
                applyFiltersAndSearch();
            });
        });

        // Animate cards on load
        window.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.skema-card');

            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';

                // Stagger the animation for each card
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
