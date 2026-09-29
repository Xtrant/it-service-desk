<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        @yield('title', 'IT Service Desk')
    </title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])
</head>

<body class="@yield('body_class', 'dashboard-page')">

    <div class="dashboard-container">

        {{-- TOPBAR --}}
        <header class="dashboard-topbar">

            {{-- BRAND --}}
            <div class="dashboard-brand">

                <div class="dashboard-brand-icon">
                    🛠
                </div>

                <div>
                    <div class="dashboard-brand-name">
                        IT Service Desk
                    </div>

                    <div class="dashboard-brand-subtitle">
                        MIS Support Portal
                    </div>
                </div>

            </div>


            {{-- ROLE NAVIGATION --}}
            @if (auth()->check())

            <nav class="dashboard-navigation">

                @if (auth()->user()->hasRole('employee'))

                <a
                    href="{{ route('dashboard') }}"
                    class="dashboard-navigation-link">
                    Dashboard
                </a>

                <a
                    href="{{ route('tickets.index') }}"
                    class="dashboard-navigation-link">
                    Ticket Saya
                </a>

                @elseif (auth()->user()->hasRole('it_staff'))

                <a
                    href="{{ route('it.dashboard') }}"
                    class="dashboard-navigation-link">
                    Dashboard IT
                </a>

                <a
                    href="{{ route('tickets.index') }}"
                    class="dashboard-navigation-link">
                    Ticket Queue
                </a>

                @elseif (auth()->user()->hasRole('mis_supervisor'))

                <a
                    href="{{ route('tickets.index') }}"
                    class="dashboard-navigation-link">
                    Approval
                </a>

                @elseif (auth()->user()->hasRole('admin'))

                <a
                    href="{{ route('it.dashboard') }}"
                    class="dashboard-navigation-link">
                    Dashboard IT
                </a>

                <a
                    href="{{ route('tickets.index') }}"
                    class="dashboard-navigation-link">
                    Ticket Queue
                </a>

                @endif

            </nav>

            @endif


            {{-- PROFILE --}}
            @if (auth()->check())

            <div class="dashboard-profile">

                <div class="dashboard-profile-info">

                    <div class="dashboard-profile-name">
                        {{ auth()->user()->name }}
                    </div>

                    <div class="dashboard-profile-department">
                        {{ auth()->user()->department?->name }}
                    </div>

                </div>

                <div class="dashboard-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

            </div>

            @endif

        </header>

        @yield('content')

        {{-- FOOTER --}}
        <footer class="dashboard-footer">

            <div>
                IT Service Desk · MIS
            </div>

            <form
                method="POST"
                action="{{ route('logout') }}">

                @csrf

                <button
                    type="submit"
                    class="dashboard-logout">
                    Logout
                </button>

            </form>

        </footer>

    </div>

</body>

</html>