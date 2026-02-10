@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">

    {{-- Header --}}
    <div style="margin-bottom:24px;">
        <a href="{{ route('admin.users.index') }}"
           style="color:#4f46e5; font-size:14px; text-decoration:none; font-weight:600;">
            ← Kembali ke Daftar Karyawan
        </a>
        <h1 style="font-size:24px; font-weight:700; color:#1f2937; margin:8px 0 0;">➕ Tambah Karyawan Baru</h1>
    </div>

    {{-- Info Banner --}}
    <div style="background:#eff6ff; border:1px solid #93c5fd; color:#1e40af; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13px;">
        ℹ️ Password default untuk karyawan baru: <strong>12345678</strong>
        <br>Karyawan dapat mengganti password setelah login pertama.
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div style="background:#fef2f2; border:1px solid #f87171; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.08); padding:24px;">
        <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Nama Lengkap --}}
            <div style="margin-bottom:16px;">
                <label for="name" style="display:block; font-size:14px; font-weight:600; color:#374151; margin-bottom:6px;">
                    Nama Lengkap <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       style="width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;"
                       placeholder="Masukkan nama lengkap">
            </div>

            {{-- Email --}}
            <div style="margin-bottom:16px;">
                <label for="email" style="display:block; font-size:14px; font-weight:600; color:#374151; margin-bottom:6px;">
                    Email <span style="color:#ef4444;">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       style="width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;"
                       placeholder="contoh@email.com">
            </div>

            {{-- Username --}}
            <div style="margin-bottom:16px;">
                <label for="username" style="display:block; font-size:14px; font-weight:600; color:#374151; margin-bottom:6px;">
                    Username <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required
                       style="width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;"
                       placeholder="username_unik">
            </div>

            {{-- Jabatan --}}
            <div style="margin-bottom:16px;">
                <label for="jabatan" style="display:block; font-size:14px; font-weight:600; color:#374151; margin-bottom:6px;">
                    Jabatan <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" required
                       style="width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; outline:none; box-sizing:border-box;"
                       placeholder="Contoh: Staff IT, Manager, HRD">
            </div>

            {{-- Foto --}}
            <div style="margin-bottom:24px;">
                <label for="foto" style="display:block; font-size:14px; font-weight:600; color:#374151; margin-bottom:6px;">
                    Foto Profil
                </label>
                <input type="file" id="foto" name="foto" accept="image/*"
                       style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; box-sizing:border-box;">
                <p style="font-size:12px; color:#9ca3af; margin:4px 0 0;">Format: JPG, PNG, WEBP. Maks 2MB.</p>
            </div>

            {{-- Submit --}}
            <div style="display:flex; gap:12px;">
                <button type="submit"
                        style="background:#4f46e5; color:#fff; padding:12px 28px; border-radius:8px; font-weight:700; font-size:14px; border:none; cursor:pointer;">
                    💾 Simpan Karyawan
                </button>
                <a href="{{ route('admin.users.index') }}"
                   style="background:#f3f4f6; color:#374151; padding:12px 28px; border-radius:8px; font-weight:600; font-size:14px; text-decoration:none; text-align:center;">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
