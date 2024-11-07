<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Student Portal') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        .navbar-nav .nav-link.active {
            color: #0d6efd;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md bg-white shadow-sm">
        <div class="container">
            <!-- Brand -->
            @if(!Auth::user())
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Student Portal') }}
            </a>
            @endif

            <!-- Toggler -->
            <button class="navbar-toggler shadow-none" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side -->
                @auth
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @if(Auth::user()->role->name === 'student')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('student.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('student.enrollments') ? 'active' : '' }}" 
                                   href="{{ route('student.enrollments') }}">
                                    My Enrollments
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->role->name === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                                   href="{{ route('admin.dashboard') }}">
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.students') ? 'active' : '' }}" 
                                   href="{{ route('admin.students') }}">
                                    Students
                                </a>
                            </li>
                        @endif
                    </ul>

                    <!-- Right Side For Authenticated Users -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" 
                               href="#" 
                               role="button" 
                               data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        Profile
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @else
                    <!-- Right Side For Guests -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" 
                               href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" 
                               href="{{ route('register') }}">Register</a>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
      <!-- Add jQuery if you haven't already -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</body>