<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Perpustakaan Multicomp') }}</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Custom CSS --}}
    <style>
        :root {
            --primary-red: #d9534f;
            --primary-red-dark: #c9302c;
            --primary-red-light: #e57373;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f6fa;
            min-height: 100vh;
        }

        /* Top Header */
        .top-navbar {
            background: linear-gradient(135deg, var(--primary-red) 0%, var(--primary-red-dark) 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .logo-circle {
            width: 48px;
            height: 48px;
            background-color: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            color: white;
        }

        .navbar-title {
            color: white;
        }

        .navbar-title h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            line-height: 1.2;
        }

        .navbar-title p {
            font-size: 0.875rem;
            margin: 0;
            opacity: 0.95;
        }

        .user-info {
            color: white;
            font-weight: 500;
        }

        .btn-logout {
            background-color: white;
            color: var(--primary-red);
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }

        .btn-logout:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* Main Content Area */
        .main-wrapper {
            min-height: calc(100vh - 100px);
        }

        /* Enhanced Card Styles */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }

        .card-header {
            background-color: white;
            border-bottom: 2px solid #f0f0f0;
            padding: 1.25rem 1.5rem;
            border-radius: 0.75rem 0.75rem 0 0 !important;
        }

        /* Table Enhancements */
        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            font-size: 0.9375rem;
        }

        .table-hover tbody tr {
            transition: background-color 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Button Enhancements */
        .btn {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-sm {
            padding: 0.375rem 0.875rem;
            font-size: 0.875rem;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #218838 0%, #1aa179 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
            border-color: #6c757d;
        }

        /* Badge Styles */
        .badge {
            padding: 0.5rem 0.875rem;
            font-weight: 600;
            border-radius: 0.375rem;
            font-size: 0.8125rem;
        }

        /* Alert Styles */
        .alert {
            border-radius: 0.75rem;
            border: none;
            padding: 1rem 1.25rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Avatar Circle */
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar-title h1 {
                font-size: 1.125rem;
            }
            
            .navbar-title p {
                font-size: 0.75rem;
            }
            
            .user-info {
                display: none;
            }
            
            .btn-logout {
                padding: 0.4rem 0.875rem;
                font-size: 0.8125rem;
            }
        }

        /* Loading State */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Page Title */
        .page-title {
            color: var(--primary-red);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        /* Empty State */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            opacity: 0.3;
            margin-bottom: 1.5rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app">
        {{-- Top Navigation Bar --}}
        <nav class="top-navbar">
            <div class="container-fluid px-3 px-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    {{-- Left: Logo & Title --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="logo-circle">
                            LP
                        </div>
                        <div class="navbar-title">
                            <h1>Selamat Datang, {{ Auth::user()->role ?? 'petugas' }}!</h1>
                            <p>Dashboard Petugas</p>
                        </div>
                    </div>

                    {{-- Right: User & Logout --}}
                    <div class="d-flex align-items-center gap-3">
                        <span class="user-info d-none d-md-inline">
                            {{ Auth::user()->name ?? 'Petugas' }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn-logout">
                                LOGOUT
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Main Content --}}
        <main class="main-wrapper">
            @yield('content')
        </main>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>