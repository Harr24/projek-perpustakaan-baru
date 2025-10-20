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
        }

        .btn-logout:hover {
            background-color: #f8f9fa;
        }

        .main-wrapper {
            min-height: calc(100vh - 100px);
        }
        
        /* ========================================================== */
        /* STYLE BARU: Untuk Lonceng Notifikasi */
        /* ========================================================== */
        .notification-dropdown .dropdown-toggle::after {
            display: none; /* Sembunyikan panah default dropdown */
        }
        .notification-icon {
            position: relative;
            font-size: 1.5rem;
            color: white;
            cursor: pointer;
        }
        .notification-count {
            position: absolute;
            top: -5px;
            right: -8px;
            background-color: #ffc107;
            color: #212529;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--primary-red);
        }
        .notification-dropdown .dropdown-menu {
            width: 350px;
            padding: 0;
            border-radius: 0.75rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            border: 1px solid #dee2e6;
        }
        .notification-header {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .notification-item {
            display: flex;
            align-items: flex-start;
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            text-decoration: none;
            color: #212529;
            transition: background-color 0.2s ease;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        .notification-item.unread {
            background-color: #fff9e6;
        }
        .notification-item .icon {
            font-size: 1.25rem;
            margin-right: 1rem;
            color: var(--primary-red);
        }
        .notification-item .message {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .notification-item .time {
            font-size: 0.75rem;
            color: #6c757d;
        }
        .notification-footer {
            display: block;
            text-align: center;
            padding: 0.75rem;
            font-weight: 600;
            color: var(--primary-red);
            text-decoration: none;
        }

        {{-- Menghapus style yang tidak perlu atau duplikat --}}

    </style>

    @yield('styles')
</head>
<body>
    <div id="app">
        {{-- Top Navigation Bar --}}
        <nav class="top-navbar">
            <div class="container-fluid px-3 px-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    {{-- Left: Logo & Title --}}
                    <div class="d-flex align-items-center gap-3">
                        <div class="logo-circle">LP</div>
                        <div class="navbar-title text-white">
                            {{-- Memastikan Auth::user() ada sebelum mengakses propertinya --}}
                            @auth
                                <h1>Selamat Datang, {{ strtok(Auth::user()->name, " ") }}!</h1>
                                <p>Dashboard {{ ucfirst(Auth::user()->role) }}</p>
                            @endauth
                        </div>
                    </div>

                    {{-- Right: User & Logout --}}
                    <div class="d-flex align-items-center gap-4">
                        @auth
                            <span class="user-info d-none d-md-inline text-white fw-semibold">
                                {{ Auth::user()->name }}
                            </span>

                            {{-- ========================================================== --}}
                            {{-- KODE BARU: Lonceng Notifikasi --}}
                            {{-- ========================================================== --}}
                            @php
                                // Ambil 5 notifikasi terbaru yang belum dibaca
                                $unreadNotifications = Auth::user()->notifications()->whereNull('read_at')->latest()->take(5)->get();
                                // Hitung semua notifikasi yang belum dibaca
                                $unreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
                            @endphp
                            <div class="dropdown notification-dropdown">
                                <a class="notification-icon" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-bell-fill"></i>
                                    @if($unreadCount > 0)
                                        <span class="notification-count">{{ $unreadCount }}</span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                    <div class="notification-header">
                                        <h6 class="mb-0 fw-bold">Notifikasi</h6>
                                    </div>
                                    <div class="notification-list">
                                        @forelse ($unreadNotifications as $notification)
                                            <a href="{{ $notification->link ?? '#' }}" class="notification-item unread">
                                                <div class="icon"><i class="bi bi-book-fill"></i></div>
                                                <div class="flex-grow-1">
                                                    <p class="message mb-1">{{ $notification->message }}</p>
                                                    <p class="time mb-0">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="text-center p-4 text-muted">
                                                <i class="bi bi-check2-circle d-block fs-1 mb-2"></i>
                                                Tidak ada notifikasi baru.
                                            </div>
                                        @endforelse
                                    </div>
                                    <a href="{{ route('borrow.history') }}" class="notification-footer">Lihat Semua Riwayat</a>
                                </div>
                            </div>

                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn-logout">LOGOUT</button>
                            </form>
                        @endauth
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
    
    @yield('scripts')
</body>
</html>

