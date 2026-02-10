@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="font-size:24px; font-weight:700; color:#1f2937; margin:0;">👥 Kelola Karyawan</h1>
            <p style="color:#6b7280; margin:4px 0 0; font-size:14px;">Daftar seluruh karyawan terdaftar dalam sistem.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.dashboard') }}"
               style="background:#6b7280; color:#fff; padding:10px 20px; border-radius:8px; font-weight:600; font-size:14px; text-decoration:none;">
                ← Dashboard
            </a>
            <a href="{{ route('admin.users.create') }}"
               style="background:#4f46e5; color:#fff; padding:10px 20px; border-radius:8px; font-weight:600; font-size:14px; text-decoration:none;">
                + Tambah Karyawan
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div style="background:#ecfdf5; border:1px solid #34d399; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#fef2f2; border:1px solid #f87171; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Data Table --}}
    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#f9fafb; border-bottom:2px solid #e5e7eb;">
                    <th style="padding:12px; text-align:left; color:#6b7280;">No</th>
                    <th style="padding:12px; text-align:left; color:#6b7280;">Foto</th>
                    <th style="padding:12px; text-align:left; color:#6b7280;">Nama</th>
                    <th style="padding:12px; text-align:left; color:#6b7280;">Username</th>
                    <th style="padding:12px; text-align:left; color:#6b7280;">Email</th>
                    <th style="padding:12px; text-align:left; color:#6b7280;">Jabatan</th>
                    <th style="padding:12px; text-align:center; color:#6b7280;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $u)
                <tr style="border-bottom:1px solid #f3f4f6;">
                    <td style="padding:12px;">{{ $i + 1 }}</td>
                    <td style="padding:12px;">
                        @if($u->foto)
                            <img src="{{ asset('storage/' . $u->foto) }}"
                                 alt="Foto {{ $u->name }}"
                                 style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid #e5e7eb;">
                        @else
                            <div style="width:40px; height:40px; border-radius:50%; background:#e5e7eb; display:flex; align-items:center; justify-content:center; font-size:16px;">
                                👤
                            </div>
                        @endif
                    </td>
                    <td style="padding:12px; font-weight:600;">{{ $u->name }}</td>
                    <td style="padding:12px;">{{ $u->username ?? '-' }}</td>
                    <td style="padding:12px;">{{ $u->email }}</td>
                    <td style="padding:12px;">{{ $u->jabatan ?? '-' }}</td>
                    <td style="padding:12px; text-align:center;">
                        <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}"
                              onsubmit="return confirm('Yakin ingin menghapus {{ $u->name }}?')"
                              style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="background:#fef2f2; color:#dc2626; border:1px solid #fca5a5; padding:6px 14px; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                                🗑️ Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:24px; text-align:center; color:#9ca3af;">
                        Belum ada data karyawan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p style="font-size:12px; color:#9ca3af; margin-top:12px; text-align:right;">
        Total: {{ $users->count() }} karyawan
    </p>
</div>
@endsection
