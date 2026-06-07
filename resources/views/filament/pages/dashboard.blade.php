@php
use App\Models\Employee;
use App\Models\Transaction;

$adminCount = Employee::where('is_admin', true)->count();
$employeeCount = Employee::where('is_admin', false)->count();
$transactionCount = Transaction::count();
$todayRevenue = Transaction::whereDate('created_at', now()->toDateString())->sum('total_amount');
@endphp

@push('styles')
    <style>
        .dashboard-shell {
            position: relative;
            padding: 2.5rem 1.5rem 3rem;
            max-width: 1160px;
            margin: 0 auto;
            color: #e5e7eb;
        }

        .dashboard-shell::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top left, rgba(124, 58, 237, 0.14), transparent 20%),
                        radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.1), transparent 18%);
            pointer-events: none;
            z-index: 0;
        }

        .dashboard-shell > * {
            position: relative;
            z-index: 1;
        }

        .dashboard-hero {
            margin-bottom: 2.5rem;
        }

        .dashboard-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.65rem 1rem;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.03);
            color: #d8b4fe;
            font-size: 0.85rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        .dashboard-title {
            font-size: clamp(2.25rem, 2rem + 1vw, 3.25rem);
            font-weight: 800;
            line-height: 1.05;
            margin: 0;
            color: #ffffff;
        }

        .dashboard-subtitle {
            margin-top: 1rem;
            max-width: 680px;
            color: #a5b4fc;
            line-height: 1.75;
            font-size: 1rem;
        }

        .dashboard-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }

        .dashboard-card {
            padding: 1.75rem;
            border-radius: 1.75rem;
            background: rgba(15, 15, 27, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 28px 80px rgba(0, 0, 0, 0.22);
        }

        .dashboard-card h2 {
            margin: 0 0 0.85rem;
            font-size: 0.9rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #c7d2fe;
            font-weight: 700;
        }

        .dashboard-card .metric {
            font-size: 2.85rem;
            font-weight: 800;
            margin: 0;
            color: #e9d5ff;
        }

        .dashboard-card .metric.accent {
            color: #a5f3fc;
        }

        .dashboard-card p {
            margin: 1rem 0 0;
            color: #9ca3af;
            line-height: 1.75;
        }

        .dashboard-actions {
            display: grid;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .dashboard-action-link {
            display: block;
            padding: 1.15rem 1.3rem;
            border-radius: 1.5rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #f8fafc;
            text-decoration: none;
            transition: background 0.2s ease, transform 0.2s ease;
        }

        .dashboard-action-link:hover {
            background: rgba(124, 58, 237, 0.17);
            transform: translateY(-1px);
        }
    </style>
@endpush

<div class="dashboard-shell">
    <section class="dashboard-hero">
        <div class="dashboard-badge">Admin Dashboard</div>
        <h1 class="dashboard-title">Manage your store with clarity.</h1>
        <p class="dashboard-subtitle">A unified admin experience styled to match the cashier interface, with quick access to your users and transaction overview.</p>
    </section>

    <section class="dashboard-grid">
        <article class="dashboard-card">
            <h2>Active Administrators</h2>
            <p class="metric">{{ $adminCount }}</p>
            <p>Admins who can access the dashboard and approve transactions.</p>
        </article>

        <article class="dashboard-card">
            <h2>Active Employees</h2>
            <p class="metric accent">{{ $employeeCount }}</p>
            <p>Employees currently registered for cashier access.</p>
        </article>

        <article class="dashboard-card">
            <h2>Total Transactions</h2>
            <p class="metric">{{ $transactionCount }}</p>
            <p>All completed sales recorded in the system.</p>
        </article>
    </section>

    <section class="dashboard-grid" style="margin-top: 1.5rem; grid-template-columns: minmax(240px, 1fr) 1.2fr;">
        <article class="dashboard-card">
            <h2>Revenue Today</h2>
            <p class="metric">${{ number_format($todayRevenue, 2) }}</p>
            <p>Transactions processed today. Keep the register moving and monitor daily performance from here.</p>
        </article>

        <article class="dashboard-card">
            <h2>Quick Actions</h2>
            <div class="dashboard-actions">
                <a class="dashboard-action-link" href="{{ url('/admin/resources/admins') }}">Manage Administrators</a>
                <a class="dashboard-action-link" href="{{ url('/admin/resources/employees') }}">Manage Employees</a>
            </div>
        </article>
    </section>
</div>
