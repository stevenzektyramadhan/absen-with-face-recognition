@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">

    {{-- ================= ADMIN HEADER ================= --}}
    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); padding:24px; margin-bottom:24px;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
            <span style="background:#ef4444; color:#fff; padding:5px 16px; border-radius:6px; font-size:13px; font-weight:700; letter-spacing:0.5px;">
                🛡️ ADMIN DASHBOARD
            </span>
        </div>
        <h1 style="font-size:24px; font-weight:700; color:#1f2937; margin:0;">
            Selamat datang, {{ Auth::user()->name }}!
        </h1>
        <p style="color:#6b7280; margin-top:4px;">Anda memiliki akses penuh sebagai Administrator.</p>
        <div style="margin-top:12px; display:flex; gap:10px;">
            <a href="{{ route('admin.users.index') }}"
               style="background:#4f46e5; color:#fff; padding:8px 18px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none;">
                👥 Kelola Karyawan
            </a>
        </div>
    </div>

    {{-- ================= STATISTIK CARDS ================= --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:24px;">

        {{-- Total Karyawan --}}
        <div style="background:#eef2ff; border-radius:12px; padding:20px; text-align:center;">
            <p style="font-size:13px; font-weight:600; color:#4f46e5; margin:0;">Total Karyawan</p>
            <p style="font-size:36px; font-weight:800; color:#312e81; margin:8px 0 0;">
                {{ \App\Models\User::where('role', 'karyawan')->count() }}
            </p>
        </div>

        {{-- Total Admin --}}
        <div style="background:#fef2f2; border-radius:12px; padding:20px; text-align:center;">
            <p style="font-size:13px; font-weight:600; color:#dc2626; margin:0;">Total Admin</p>
            <p style="font-size:36px; font-weight:800; color:#991b1b; margin:8px 0 0;">
                {{ \App\Models\User::where('role', 'admin')->count() }}
            </p>
        </div>

        {{-- Hadir Hari Ini --}}
        <div style="background:#ecfdf5; border-radius:12px; padding:20px; text-align:center;">
            <p style="font-size:13px; font-weight:600; color:#059669; margin:0;">Hadir Hari Ini</p>
            <p style="font-size:36px; font-weight:800; color:#065f46; margin:8px 0 0;">
                {{ \App\Models\Attendance::whereDate('tanggal', today())->count() }}
            </p>
        </div>

        {{-- Total User --}}
        <div style="background:#fffbeb; border-radius:12px; padding:20px; text-align:center;">
            <p style="font-size:13px; font-weight:600; color:#d97706; margin:0;">Total Semua User</p>
            <p style="font-size:36px; font-weight:800; color:#92400e; margin:8px 0 0;">
                {{ \App\Models\User::count() }}
            </p>
        </div>

    </div>

    {{-- ================= DAFTAR KARYAWAN ================= --}}
    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); padding:24px;">
        <h3 style="font-size:18px; font-weight:700; color:#1f2937; margin:0 0 16px;">📋 Daftar Karyawan</h3>

        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#f9fafb; border-bottom:2px solid #e5e7eb;">
                    <th style="padding:10px 12px; text-align:left; color:#6b7280;">No</th>
                    <th style="padding:10px 12px; text-align:left; color:#6b7280;">Nama</th>
                    <th style="padding:10px 12px; text-align:left; color:#6b7280;">Username</th>
                    <th style="padding:10px 12px; text-align:left; color:#6b7280;">Email</th>
                    <th style="padding:10px 12px; text-align:left; color:#6b7280;">Jabatan</th>
                    <th style="padding:10px 12px; text-align:center; color:#6b7280;">Role</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\User::orderBy('name')->get() as $i => $u)
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:10px 12px;">{{ $i + 1 }}</td>
                    <td style="padding:10px 12px; font-weight:600;">{{ $u->name }}</td>
                    <td style="padding:10px 12px;">{{ $u->username ?? '-' }}</td>
                    <td style="padding:10px 12px;">{{ $u->email }}</td>
                    <td style="padding:10px 12px;">{{ $u->jabatan ?? '-' }}</td>
                    <td style="padding:10px 12px; text-align:center;">
                        @if($u->role === 'admin')
                            <span style="background:#fef2f2; color:#dc2626; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600;">Admin</span>
                        @else
                            <span style="background:#eef2ff; color:#4f46e5; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600;">Karyawan</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
