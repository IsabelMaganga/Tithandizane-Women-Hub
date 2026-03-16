<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Tithandizane Women Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #7C3D5E;
            --primary-lt: #A85080;
            --accent: #E8976A;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #FDF6F0;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--primary) !important;
        }

        .navbar {
            background: #fff;
            border-bottom: 1px solid #EDE0D8;
            box-shadow: 0 2px 4px rgba(124,61,94,.08);
        }

        .nav-link {
            color: #6B3D57 !important;
            font-weight: 500;
            transition: color .2s;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .nav-link.active {
            color: var(--primary) !important;
            font-weight: 600;
        }

        .btn-primary-hub {
            background: linear-gradient(135deg, var(--primary), var(--primary-lt));
            border: none;
            color: #fff;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: opacity .2s;
        }

        .btn-primary-hub:hover {
            opacity: .9;
            color: #fff;
        }

        .page-header {
            background: #fff;
            padding: 24px 0;
            border-bottom: 1px solid #EDE0D8;
            margin-bottom: 24px;
        }

        .page-header h2 {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .page-header p {
            color: #9A7A8E;
            font-size: .9rem;
        }

        .form-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #EDE0D8;
            box-shadow: 0 4px 12px rgba(124,61,94,.06);
        }

        .section-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #EDE0D8;
            overflow: hidden;
        }

        .section-card-header {
            background: linear-gradient(135deg, var(--primary-lt), var(--accent));
            color: #fff;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-card-header h6 {
            margin: 0;
            font-weight: 600;
        }

        .invalid-feedback {
            font-size: .78rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-heart-fill me-2"></i>
                Tithandizane Women Hub
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                           href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.mentors*') ? 'active' : '' }}" 
                           href="{{ route('admin.mentors.index') }}">
                            <i class="bi bi-people me-1"></i> Mentors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" 
                           href="{{ route('admin.reports.index') }}">
                            <i class="bi bi-exclamation-triangle me-1"></i> Reports
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    @guest('admin')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                {{ Auth::guard('admin')->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.register') }}">
                                    <i class="bi bi-person-plus me-2"></i>Add Admin
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.logout') }}">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a></li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    @hasSection('page-title')
    <div class="page-header">
        <div class="container-fluid">
            <h2>@yield('page-title')</h2>
            @hasSection('page-subtitle')
                <p>@yield('page-subtitle')</p>
            @endif
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main class="container-fluid py-4">
        @yield('content')
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
