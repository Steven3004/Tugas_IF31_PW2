<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function login(LoginRequest $request){
        try {
            $validated = $request->safe()->all();

            if (!Auth::attempt($validated)){
                return Response::json([
				            'message' => 'Invalid credentials',
				            'data' => null
				        ], 401);
            }
            $user = $request->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return Response::json([
				        'message' => 'Login successful',
				        'user' => [
				            'id' => $user->id,
				            'name' => $user->name,
				            'email' => $user->email,
				        ],
				        'access_token' => $token,
				    ], 200);

        }catch (Exception $e) {
				    // Jika terjadi error tak terduga (misalnya, koneksi database gagal) di dalam blok `try`.
				    return Response::json([
				        'message' => $e->getMessage(), // Kirim pesan error spesifik dari sistem.
				        'data' => null
				    ], 500);
        }
    }

    public function register(RegisterRequest $request)
    {
        // Memulai blok try-catch untuk menangani potensi error selama proses registrasi.
				try {
				    // 1. Mengambil semua data dari request yang sudah lolos validasi.
				    // Metode `safe()` memastikan hanya data yang terdefinisi dalam aturan validasi yang diambil.
				    $validated = $request->safe()->all();

				    // 2. Melakukan hashing (enkripsi) pada password yang diterima dari inputan user untuk keamanan.
				    $passwordHash = Hash::make($validated['password']);

				    // 3. Mengganti nilai password di dalam array `$validated` dengan password yang sudah di-hash.
				    $validated['password'] = $passwordHash;

				    // 4. Membuat record user baru di dalam database menggunakan data dari array `$validated`.
				    $response = User::create($validated);

				    // 5. Memeriksa apakah proses pembuatan user berhasil.
				    if ($response) {
				        // Jika berhasil, kirimkan response JSON dengan pesan sukses dan data user yang baru dibuat.
				        // Status HTTP 201 menandakan "Created" (sumber daya baru berhasil dibuat).
				        return Response::json([
				            'message' => 'User registered successfully',
				            'data' => null
				        ], 201);
				    }
				} catch (Exception $e) {
				    // Jika terjadi error di dalam blok `try`, blok `catch` akan dieksekusi.
				    // Variabel `$e` berisi detail dari error yang terjadi.
				    return Response::json([
				        'message' => $e->getMessage(), // Pesan error spesifik dari sistem.
				        'data' => null
				    ], 500); // Status HTTP 500 menandakan "Internal Server Error".
				}
    }

    public function logout(Request $request)
    {
        try {
            // Ambil user yang sedang login
            // ambil tokennya terus hapus
            $request->user()->currentAccessToken()->delete();

            // berikan response jika berhasil logout
            return response()->json([
                'message' => 'Berhasil Logout',
                'data' => null
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

}
