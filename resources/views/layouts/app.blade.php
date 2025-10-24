<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine.js for interactivity -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>

    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <div class="drawer lg:drawer-open">
            <input id="drawer-toggle" type="checkbox" class="drawer-toggle" />
            
            <!-- Page content here -->
            <div class="drawer-content flex flex-col bg-gray-50 dark:bg-gray-900 min-h-screen">
                @include('layouts.navigation')
                
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                        <div class="max-w-7xl mx-auto py-8 px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 bg-gray-50 dark:bg-gray-900">
                    <div class="max-w-7xl mx-auto px-6 lg:px-8 py-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
            
            <!-- Sidebar -->
            @include('layouts.sidebar')
        </div>

        <!-- Dark mode toggle script -->
        <script>
            // Theme toggle functionality
            function toggleTheme() {
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            }
            
            // Load saved theme
            document.addEventListener('DOMContentLoaded', function() {
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
            });
        </script>
    </body>
</html>
