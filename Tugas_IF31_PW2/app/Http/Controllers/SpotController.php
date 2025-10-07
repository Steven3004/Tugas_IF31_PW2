<?php

namespace App\Http\Controllers;

use App\Models\Spot;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpotController extends Controller
{
    /**
     * Menampilkan daftar semua resource (Spot).
     * Method ini akan diakses melalui request GET ke endpoint /api/spots.
     */
    public function index()
    {
        // yang mungkin terjadi selama proses eksekusi kode.
        try {
            // Memulai query untuk mengambil data dari model Spot.
            $spots = Spot::with([
                // 'with' digunakan untuk Eager Loading. Ini sangat penting untuk performa
                // karena menghindari masalah N+1 query. Kita memuat relasi 'categories' dan 'user'.
                // 'categories:category,spot_id' berarti kita hanya mengambil kolom 'category' dan 'spot_id' dari tabel categories.
                'categories:category,spot_id',
                // Sama seperti di atas, kita hanya mengambil kolom 'id' dan 'name' dari tabel user yang berelasi.
                'user:id,name'
            ])
                // 'withCount' akan menghitung jumlah data pada relasi 'reviews' dan menyimpannya
                // dalam properti baru bernama 'reviews_count' pada setiap objek spot.
                ->withCount(['reviews'])
                // 'withSum' akan menjumlahkan nilai dari kolom 'rating' pada relasi 'reviews'.
                // Hasilnya akan disimpan dalam properti 'reviews_sum_rating'.
                ->withSum('reviews', 'rating')
                // Mengurutkan hasil berdasarkan kolom 'created_at' dari yang terbaru (descending).
                ->orderBy('created_at', 'desc')
                // 'paginate' akan membagi hasil query menjadi beberapa halaman.
                // request('size', 10) berarti kita mengambil 'size' dari query parameter URL (?size=5),
                // jika tidak ada, defaultnya adalah 10 item per halaman.
                ->paginate(request('size', 10));

            // Mengembalikan respons dalam format JSON dengan data spots yang berhasil diambil.
            // Status code 200 (OK) menandakan request berhasil.
            return Response::json([
                'message' => "Spots retrieved successfully",
                'data' => $spots
            ], 200);
        } catch (Exception $e) {
            // Jika terjadi error di dalam blok 'try', eksekusi akan lompat ke blok 'catch'.
            // Kita mengembalikan respons JSON yang berisi pesan error.
            // Status code 500 (Internal Server Error) menandakan ada masalah di server.
            return Response::json([
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    // ... method lainnya
}
