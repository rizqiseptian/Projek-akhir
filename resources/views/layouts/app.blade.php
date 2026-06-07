<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name'))</title>
        
        <!-- Tailwind CSS (using CDN for standalone simplicity) -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        @stack('styles')
        @livewireStyles
    </head>
    <body class="@yield('body-class', 'bg-[#0a0a0f] font-sans antialiased text-[#EDEDEC]')">
        <div class="min-h-screen">
            <!-- Navigation -->
            @auth
            <nav class="bg-[#0f0f18]/80 backdrop-blur-md border-b border-[#7c3aed]/10 sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center gap-3">
                            <span class="text-xl font-extrabold bg-gradient-to-r from-violet-400 to-indigo-500 bg-clip-text text-transparent">{{ config('app.name') }}</span>
                            @if(auth()->user()->is_admin)
                                <span class="bg-violet-500/10 border border-violet-500/30 text-violet-300 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">Admin</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="text-sm text-gray-400">
                                Cashier: <span class="font-semibold text-white">{{ auth()->user()->name }}</span>
                            </div>
                            @if(auth()->user()->is_admin && !request()->is('admin*'))
                                <a href="{{ url('/admin') }}" class="text-sm font-medium text-violet-400 hover:text-violet-300 transition-colors flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                                    Admin Panel
                                </a>
                            @endif
                            <form action="{{ route('employee.logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-gray-400 hover:text-red-400 transition-colors flex items-center gap-1.5 cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" /></svg>
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
            @endauth

            <!-- Page Content -->
            <main>
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
