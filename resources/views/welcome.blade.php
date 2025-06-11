<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite('resources/css/app.css')
    <title>Audit Observation Tracker</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    <style>
        .bg-dots-darker {
            background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E")
        }

        @media (prefers-color-scheme: dark) {
            .dark\:bg-dots-lighter {
                background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E")
            }
        }
    </style>
</head>

<body class="antialiased">
    <header>
        @if (Route::has('login'))
        <nav class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
            @auth
            <a href="{{ url('/home') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Home</a>
            @else
            <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>
            @if (Route::has('register'))
            <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
            @endif
            @endauth
        </nav>
        @endif
    </header>

    <main>
        <div class="relative flex flex-col sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <div class="flex flex-col lg:flex-row justify-center items-center">
                    <img
                        class="mx-auto lg:w-1/3"
                        src="{{ asset('images/inner-logo.png') }}"
                        alt="Audit Service Ghana Logo"
                        width="150" />
                    <div
                        class="flex items-center justify-center flex-col mt-8 p-12 text-center w-full lg:w-1/2 sm:rounded-xl bg-green-700 lg:bg-transparent">
                        <h1 class="text-white lg:text-green-900 dark:text-gray-50 text-2xl mb-6">
                            Audit Service, Ghana
                        </h1>
                        <h2 class="text-white lg:text-green-900 dark:text-gray-50 text-6xl hidden md:block">
                            Audit Observation Tracker
                        </h2>
                        <h2 class="text-white text-4xl md:hidden">Monitoring</h2>
                        <div class="mt-5 flex flex-col sm:flex-row gap-4">
                            @auth
                            <a
                                href="/admin"
                                class="mt-4 sm:mt-12 text-gray-700 lg:text-white text-2xl lg:text-3xl bg-white lg:bg-green-800 hover:bg-green-900 hover:text-white focus:outline-none hover:ring-green-600 rounded-lg px-8 py-3.5 text-center tracking-widest transition">
                                Dashboard
                            </a>
                            @else
                            <a
                                href="/admin/login"
                                class="mt-4 sm:mt-12 text-green-800 dark:text-gray-50 text-2xl lg:text-3xl bg-white dark:bg-transparent focus:outline-none hover:ring-2 ring-green-700 hover:ring-green-600 dark:hover:ring-gray-50 rounded-lg px-8 py-3.5 text-center tracking-widest transition border border-green-700">
                                Login
                            </a>
                            <a
                                href="/admin/register"
                                class="mt-4 sm:mt-12 text-white bg-green-800 dark:bg-green-700 text-2xl lg:text-3xl focus:outline-none hover:bg-green-900 hover:text-white rounded-lg px-8 py-3.5 text-center tracking-widest transition border border-green-800 ml-0 sm:ml-4">
                                Register
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="flex justify-center mt-16 px-0 sm:items-center sm:justify-between">
            <div class="text-center text-sm sm:text-left">
                &nbsp;
            </div>
            <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0">
                Observations v0.01 &copy; {{ date('Y') }} Audit Service, Ghana
            </div>
        </div>
    </footer>
</body>

</html>