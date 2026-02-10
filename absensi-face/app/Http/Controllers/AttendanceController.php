<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Dashboard karyawan
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // tanggal filter (default hari ini)
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        // absensi hari ini (untuk panel atas)
        $attendanceToday = Attendance::where('user_id', $user->id)
            ->where('tanggal', $tanggal)
            ->first();

        // riwayat absensi user (untuk tabel bawah)
        $attendanceHistory = Attendance::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('dashboard', [
            'user'              => $user,
            'attendanceToday'  => $attendanceToday,
            'attendanceHistory'=> $attendanceHistory,
            'tanggal'          => $tanggal,
        ]);
    }

    /**
     * Tombol Absen Masuk (UI saja)
     * Real logic tetap dari Flask → API
     */
    public function absenMasuk()
    {
        return redirect("http://127.0.0.1:5000/?type=masuk");
    }

    /**
     * Tombol Absen Keluar (UI saja)
     * Real logic tetap dari Flask → API
     */
    public function absenKeluar()
    {
        return redirect("http://127.0.0.1:5000/?type=pulang");
    }
}
