@props([
    'livewire' => null,
])

@php
    $renderHookScopes = $livewire?->getRenderHookScopes();
@endphp

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    @class([
        'fi',
        'dark' => filament()->hasDarkMode() && filament()->hasDarkModeForced(),
    ])
>
    <head>
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_START, scopes: $renderHookScopes) }}

        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        @if ($favicon = filament()->getFavicon())
            <link rel="icon" href="{{ $favicon }}" />
        @endif

        @php
            $title = trim(strip_tags($livewire?->getTitle() ?? ''));
            $brandName = trim(strip_tags(filament()->getBrandName()));
        @endphp

        <title>
            {{ filled($title) ? $title : null }}
            {{ filled($brandName) && filled($title) ? ' - ' : null }}
            {{ filled($brandName) ? $brandName : null }}
        </title>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_BEFORE, scopes: $renderHookScopes) }}

        <style>
            [x-cloak=''],
            [x-cloak='x-cloak'],
            [x-cloak='1'] {
                display: none !important;
            }

            [x-cloak='inline-flex'] {
                display: inline-flex !important;
            }

            @media (max-width: 1023px) {
                [x-cloak='-lg'] {
                    display: none !important;
                }
            }

            @media (min-width: 1024px) {
                [x-cloak='lg'] {
                    display: none !important;
                }
            }
        </style>

        @filamentStyles

        <style>
            html.fi {
                background: radial-gradient(circle at top left, rgba(124, 58, 237, 0.22), transparent 22%),
                            radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.16), transparent 20%),
                            linear-gradient(180deg, #04050a 0%, #070b13 100%);
                color-scheme: dark;
            }

            .fi-body {
                background: transparent;
            }

            .fi-shell {
                min-height: 100vh;
                background: transparent;
            }

            .filament-sidebar {
                background: rgba(10, 10, 18, 0.94) !important;
                border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
            }

            .filament-sidebar .fi-sidebar-item,
            .filament-sidebar .fi-sidebar-item a {
                color: #d6d6e0 !important;
            }

            .filament-sidebar .fi-sidebar-item.is-active,
            .filament-sidebar .fi-sidebar-item:hover {
                background: rgba(124, 58, 237, 0.18) !important;
            }

            .fi-topbar,
            .fi-page-heading,
            .filament-page-heading {
                background: rgba(10, 10, 18, 0.92) !important;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.16) !important;
            }

            .filament-page-heading-heading,
            .fi-heading-title {
                color: #f8fafc !important;
            }

            .fi-page-description,
            .filament-page-description,
            .fi-heading-description {
                color: #a5b4fc !important;
            }

            .filament-card,
            .filament-form-card,
            .filament-widget,
            .fi-card,
            .fi-table,
            .filament-table,
            .fi-filters,
            .filament-breadcrumbs {
                background: rgba(12, 12, 22, 0.92) !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                box-shadow: 0 30px 90px rgba(0, 0, 0, 0.14) !important;
                backdrop-filter: blur(18px);
            }

            .filament-input input,
            .filament-input textarea,
            .filament-input select,
            .fi-input input,
            .fi-input textarea,
            .fi-input select {
                background: rgba(255, 255, 255, 0.04) !important;
                border-color: rgba(255, 255, 255, 0.12) !important;
                color: #f8fafc !important;
            }

            .filament-button,
            .fi-button,
            .fi-primary-button,
            button[type='submit'],
            .filament-form button {
                background: linear-gradient(90deg, #7c3aed 0%, #6366f1 100%) !important;
                border-color: transparent !important;
                color: #ffffff !important;
            }

            .filament-button:hover,
            .fi-button:hover,
            .fi-primary-button:hover {
                transform: translateY(-1px);
            }

            .filament-table,
            .fi-table,
            .filament-tables-table,
            .fi-tables-table {
                border-radius: 1.25rem !important;
                overflow: hidden;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                background: rgba(10, 10, 18, 0.94) !important;
            }

            .filament-table thead th,
            .fi-table thead th,
            .filament-table thead td,
            .fi-table thead td {
                background: rgba(255, 255, 255, 0.04) !important;
                color: #e2e8f0 !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            }

            .filament-table tbody tr,
            .fi-table tbody tr {
                background: rgba(255, 255, 255, 0.02) !important;
            }

            .filament-table tbody tr:hover,
            .fi-table tbody tr:hover {
                background: rgba(255, 255, 255, 0.05) !important;
            }

            .filament-table td,
            .fi-table td,
            .filament-table th,
            .fi-table th {
                color: #e5e7eb !important;
                border-color: rgba(255, 255, 255, 0.08) !important;
            }

            .filament-tables-header,
            .filament-tables-toolbar,
            .fi-tables-header,
            .fi-tables-toolbar,
            .filament-filters,
            .fi-filters {
                background: rgba(15, 15, 27, 0.92) !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                border-radius: 1.25rem;
                box-shadow: 0 24px 60px rgba(0, 0, 0, 0.1);
            }

            .filament-tables-toolbar .filament-button,
            .fi-tables-toolbar .fi-button {
                background: rgba(124, 58, 237, 0.18) !important;
            }

            .filament-tables-header-left,
            .filament-tables-header-right,
            .fi-tables-header-left,
            .fi-tables-header-right {
                gap: 1rem;
            }

            .filament-badge,
            .fi-badge,
            .filament-pill,
            .fi-pill {
                background: rgba(124, 58, 237, 0.18) !important;
                color: #ede9fe !important;
            }

            .filament-pagination,
            .fi-pagination {
                background: rgba(15, 15, 27, 0.92) !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                border-radius: 1rem;
            }

            .filament-pagination li a,
            .fi-pagination li a {
                color: #e2e8f0 !important;
            }

            .filament-pagination li a:hover,
            .fi-pagination li a:hover {
                background: rgba(124, 58, 237, 0.18) !important;
            }
        </style>

        <style>
            /* dashboard-scoped table theme (applies only to admin resource pages) */
            .dashboard-theme .filament-table,
            .dashboard-theme .fi-table,
            .dashboard-theme .filament-tables-table,
            .dashboard-theme .fi-tables-table {
                border-radius: 1.25rem !important;
                overflow: hidden;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                background: rgba(10, 10, 18, 0.94) !important;
            }

            .dashboard-theme .filament-table thead th,
            .dashboard-theme .fi-table thead th,
            .dashboard-theme .filament-table thead td,
            .dashboard-theme .fi-table thead td {
                background: rgba(255, 255, 255, 0.04) !important;
                color: #e2e8f0 !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            }

            .dashboard-theme .filament-table tbody tr,
            .dashboard-theme .fi-table tbody tr {
                background: rgba(255, 255, 255, 0.02) !important;
            }

            .dashboard-theme .filament-table tbody tr:hover,
            .dashboard-theme .fi-table tbody tr:hover {
                background: rgba(255, 255, 255, 0.05) !important;
            }

            /* Dashboard primary button (e.g. New Administrator) */
            .dashboard-theme .filament-button,
            .dashboard-theme .fi-button,
            .dashboard-theme .filament-tables-toolbar .filament-button,
            .dashboard-theme .fi-tables-toolbar .fi-button {
                background: linear-gradient(90deg, #7c3aed 0%, #6366f1 100%) !important;
                border-color: transparent !important;
                color: #ffffff !important;
                box-shadow: 0 8px 30px rgba(124, 58, 237, 0.18) !important;
                transition: transform .12s ease, box-shadow .12s ease !important;
            }

            .dashboard-theme .filament-button:hover,
            .dashboard-theme .fi-button:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 18px 50px rgba(99, 102, 241, 0.18) !important;
            }
        </style>

        <script>
            (function () {
                var p = location.pathname || '';
                if (p.includes('/admin/resources/admins') || p.includes('/admin/resources/employees')) {
                    document.documentElement.classList.add('dashboard-theme');
                }
            })();
        </script>

        {{ filament()->getTheme()->getHtml() }}
        {{ filament()->getFontPreloadHtml() }}
        {{ filament()->getMonoFontPreloadHtml() }}
        {{ filament()->getSerifFontPreloadHtml() }}
        {{ filament()->getFontHtml() }}
        {{ filament()->getMonoFontHtml() }}
        {{ filament()->getSerifFontHtml() }}

        <style>
            :root {
                --font-family: '{!! filament()->getFontFamily() !!}';
                --mono-font-family: '{!! filament()->getMonoFontFamily() !!}';
                --serif-font-family: '{!! filament()->getSerifFontFamily() !!}';
                --sidebar-width: {{ filament()->getSidebarWidth() }};
                --collapsed-sidebar-width: {{ filament()->getCollapsedSidebarWidth() }};
                --default-theme-mode: {{ filament()->getDefaultThemeMode()->value }};
            }

            html.fi {
                --livewire-progress-bar-color: var(--primary-500);
            }
        </style>

        @stack('styles')

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_AFTER, scopes: $renderHookScopes) }}

        @if (! filament()->hasDarkMode())
            <script>
                localStorage.setItem('theme', 'light')
            </script>
        @elseif (filament()->hasDarkModeForced())
            <script>
                localStorage.setItem('theme', 'dark')
            </script>
        @else
            <script>
                const loadDarkMode = () => {
                    window.theme = localStorage.getItem('theme') ?? @js(filament()->getDefaultThemeMode()->value)

                    if (
                        window.theme === 'dark' ||
                        (window.theme === 'system' &&
                            window.matchMedia('(prefers-color-scheme: dark)')
                                .matches)
                    ) {
                        document.documentElement.classList.add('dark')
                    }
                }

                loadDarkMode()

                document.addEventListener('livewire:navigated', loadDarkMode)
            </script>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_END, scopes: $renderHookScopes) }}
    </head>

    <body
        {{
            $attributes
                ->merge($livewire?->getExtraBodyAttributes() ?? [], escape: false)
                ->class([
                    'fi-body',
                    'fi-panel-' . filament()->getId(),
                ])
        }}
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_START, scopes: $renderHookScopes) }}

        {{ $slot }}

        @livewire(Filament\Livewire\Notifications::class)

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SCRIPTS_BEFORE, scopes: $renderHookScopes) }}

        @filamentScripts(withCore: true)

        @if (filament()->hasBroadcasting() && config('filament.broadcasting.echo'))
            <script data-navigate-once>
                window.Echo = new window.EchoFactory(@js(config('filament.broadcasting.echo')))

                window.dispatchEvent(new CustomEvent('EchoLoaded'))
            </script>
        @endif

        @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
            <script>
                loadDarkMode()
            </script>
        @endif

        @stack('scripts')

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SCRIPTS_AFTER, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_END, scopes: $renderHookScopes) }}
    </body>
</html>
