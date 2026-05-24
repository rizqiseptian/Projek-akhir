<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Cashier POS</title>
        
        <!-- Tailwind CSS (using CDN for standalone simplicity) -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        @livewireStyles
    </head>
    <body class="bg-gray-100 font-sans antialiased text-gray-900">
        <div class="min-h-screen">
            <!-- Navigation -->
            <nav class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <span class="text-xl font-bold text-blue-600">POS Cashier</span>
                        </div>
                        <div class="flex items-center">
                            <a href="{{ url('/admin') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Back to Admin</a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
    </body>
</html>
