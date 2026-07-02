<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="icon" href="{{ asset('favicon.svg') }}">

    <title>Nusantara Jaya Computer</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            min-height: 100vh;
            background: #1e1e2d; /* Warna gelap elegan */
            color: #fff;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            z-index: 1000;
        }
        #sidebar.toggled {
            margin-left: -260px;
        }
        #sidebar .sidebar-header {
            padding: 25px 20px;
            background: #1a1a27;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul li a {
            padding: 12px 25px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            color: #a2a3b7;
            text-decoration: none;
            transition: 0.3s;
        }
        #sidebar ul li a:hover, #sidebar ul li a.active {
            color: #fff;
            background: #2b2b40;
            border-left: 4px solid #0d6efd; /* Aksen biru profesional */
        }
        #sidebar ul li a i {
            margin-right: 12px;
            font-size: 1.2rem;
        }

        /* Content Styling */
        #content {
            width: 100%;
            min-height: 100vh;
            transition: all 0.3s;
        }
        .top-navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            padding: 15px 25px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .btn-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #555;
            transition: 0.2s;
        }
        .btn-toggle:hover { color: #0d6efd; }
    </style>
</head>
<body>
    <div class="d-flex">
        
        @include('layouts.navigation')

        <div id="content">
            
            <nav class="top-navbar d-flex justify-content-between align-items-center">
                <div>
                    <button type="button" id="sidebarCollapse" class="btn-toggle">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <a class="text-decoration-none text-dark dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="text-end me-3 d-none d-md-block">
                                <div class="fw-bold fs-6" style="line-height: 1.2;">{{ Auth::user()->name }}</div>
                                <small class="text-primary fw-semibold text-uppercase" style="font-size: 0.75rem;">{{ Auth::user()->peran }}</small>
                            </div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D6EFD&color=fff&bold=true" class="rounded-circle shadow-sm" width="42">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger fw-semibold"><i class="bi bi-box-arrow-right me-2"></i> Keluar Sistem</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="p-4">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('sidebarCollapse').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('toggled');
        });
    </script>
</body>
</html>