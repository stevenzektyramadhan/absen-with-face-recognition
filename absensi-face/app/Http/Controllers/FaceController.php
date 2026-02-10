<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FaceController extends Controller
{
    /**
     * Show the face registration page.
     */
    public function index()
    {
        return view('face.register');
    }

    /**
     * Store the face embedding via the Python AI service.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required',
        ]);

        // Get authenticated user's name and sanitize for filename
        $userName = Auth::user()->name;
        $safeName = Str::slug($userName, '_');

        try {
            // Decode base64 image to a temporary file
            $base64Image = $request->input('image');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));

            $tempPath = tempnam(sys_get_temp_dir(), 'face_') . '.jpg';
            file_put_contents($tempPath, $imageData);

            // Send to Python AI service
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($tempPath), $safeName . '.jpg')
                ->post('http://ai-service:5000/register_face', [
                    'name' => $safeName,
                ]);

            // Clean up temp file
            unlink($tempPath);

            if ($response->successful()) {
                return redirect()->back()->with('success', 'Wajah berhasil didaftarkan!');
            }

            $error = $response->json('error') ?? 'Gagal mendaftarkan wajah.';
            return redirect()->back()->with('error', $error);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Koneksi ke AI Service gagal: ' . $e->getMessage());
        }
    }
}
