<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
            'attendanceToday'   => $attendanceToday,
            'attendanceHistory' => $attendanceHistory,
            'tanggal'           => $tanggal,
        ]);
    }

    /**
     * Halaman scan wajah untuk absensi
     */
    public function scan()
    {
        return view('attendance.scan');
    }

    /**
     * Proses absensi via face recognition
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required',
        ]);

        $user = Auth::user();

        try {
            // Decode base64 image
            $base64Image = $request->input('image');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

            $tempPath = tempnam(sys_get_temp_dir(), 'att_') . '.jpg';
            file_put_contents($tempPath, $imageData);

            // Send to Python AI service for recognition
            $response = Http::timeout(15)
                ->attach('frame', file_get_contents($tempPath), 'frame.jpg')
                ->post('http://ai-service:5000/recognize_frame');

            // Clean up
            unlink($tempPath);

            if (!$response->successful()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'AI Service tidak merespons.',
                ], 500);
            }

            $data = $response->json();
            $detectedName = $data['name'] ?? 'unknown';
            $score = $data['score'] ?? 0;
            $aiStatus = $data['status'] ?? 'rejected';

            // Check if face was recognized
            if ($detectedName === 'unknown' || $aiStatus === 'rejected') {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Wajah tidak dikenali! Pastikan wajah Anda sudah terdaftar.',
                ]);
            }

            // Verify detected face matches the authenticated user
            // Match by slug of name (same logic as FaceController registration)
            $expectedName = Str::slug($user->name, '_');

            if ($detectedName !== $expectedName) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Wajah tidak sesuai dengan akun Anda. Terdeteksi: ' . $detectedName,
                ]);
            }

            // ================================
            // ATTENDANCE LOGIC
            // ================================
            $today = Carbon::today()->toDateString();

            $attendance = Attendance::where('user_id', $user->id)
                ->where('tanggal', $today)
                ->first();

            // Case 1: No record yet → Clock In
            if (!$attendance) {
                $now = Carbon::now();
                $batasMasuk = Carbon::today()->setHour(8)->setMinute(0);

                $status = $now->greaterThan($batasMasuk) ? 'terlambat' : 'hadir';

                Attendance::create([
                    'user_id'   => $user->id,
                    'tanggal'   => $today,
                    'jam_masuk' => $now->toTimeString(),
                    'status'    => $status,
                ]);

                return response()->json([
                    'status'  => 'success',
                    'type'    => 'masuk',
                    'message' => '✅ Berhasil Absen Masuk! (' . $now->format('H:i') . ')',
                ]);
            }

            // Case 2: Record exists, no time_out → Clock Out
            if (!$attendance->jam_keluar) {
                $now = Carbon::now();
                $attendance->update([
                    'jam_keluar' => $now->toTimeString(),
                ]);

                return response()->json([
                    'status'  => 'success',
                    'type'    => 'pulang',
                    'message' => '✅ Berhasil Absen Pulang! (' . $now->format('H:i') . ')',
                ]);
            }

            // Case 3: Already clocked in and out
            return response()->json([
                'status'  => 'info',
                'message' => 'Anda sudah selesai absen hari ini.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghubungi AI Service: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tombol Absen Masuk (legacy)
     */
    public function absenMasuk()
    {
        return redirect()->route('attendance.scan');
    }

    /**
     * Tombol Absen Keluar (legacy)
     */
    public function absenKeluar()
    {
        return redirect()->route('attendance.scan');
    }
}
