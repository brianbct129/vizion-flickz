<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;

class DrakorController extends Controller
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function index()
    {
        try {
            $page = request('page', 1);
            
            // Get all Korean dramas
            $allKdramas = collect();
            $processedIds = []; // Untuk tracking ID yang sudah diproses
            
            // Ambil data (batasi sampai 5 halaman untuk performa)
            for ($i = 1; $i <= 5; $i++) {
                $dramas = $this->tmdb->getKoreanDramas($i);
                if (!empty($dramas['results'])) {
                    // Filter hanya drama yang belum diproses
                    $uniqueDramas = collect($dramas['results'])->filter(function($drama) use (&$processedIds) {
                        if (in_array($drama->id, $processedIds)) {
                            return false;
                        }
                        $processedIds[] = $drama->id;
                        return true;
                    });
                    
                    $allKdramas = $allKdramas->merge($uniqueDramas);
                }
            }

            // Urutkan berdasarkan popularity
            $allContent = $allKdramas
                ->sortByDesc('popularity')
                ->values();

            // Hitung total halaman yang sebenarnya
            $totalItems = $allContent->count();
            $itemsPerPage = 18;
            $totalPages = ceil($totalItems / $itemsPerPage);

            // Jika halaman yang diminta melebihi total, redirect ke halaman terakhir
            if ($page > $totalPages) {
                return redirect()->route('drakor.index', ['page' => $totalPages]);
            }

            // Ambil konten untuk halaman yang diminta
            $offset = ($page - 1) * $itemsPerPage;
            $pageContent = $allContent->slice($offset, $itemsPerPage)->values();

            return view('drakor.korean-drama', [
                'content' => $pageContent,
                'currentPage' => (int) $page,
                'totalPages' => $totalPages,
                'studioName' => 'Korean Drama'
            ]);

        } catch (\Exception $e) {
            \Log::error('Korean drama page error: ' . $e->getMessage());
            abort(404);
        }
    }
} 