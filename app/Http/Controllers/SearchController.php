<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $tmdb;

    public function __construct(TMDBService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    private function normalizeString($str)
    {
        // Extract year if present
        preg_match('/\((\d{4})\)/', $str, $yearMatches);
        $year = $yearMatches[1] ?? null;
        
        // Remove year from string for normalization
        $strWithoutYear = preg_replace('/\s*\(\d{4}\)\s*/', '', $str);
        
        $normalized = strtolower(
            preg_replace(
                ['/[&]/', '/[-\'\s:]+/'],
                ['and', ''],
                trim($strWithoutYear)
            )
        );
        
        return [
            'text' => $normalized,
            'year' => $year
        ];
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $page = $request->input('page', 1);
        $results = [];

        if ($query) {
            $results = $this->tmdb->multiSearch($query, $page);
            
            // Normalisasi query dan ekstrak tahun
            $normalized = $this->normalizeString($query);
            $normalizedQuery = $normalized['text'];
            $searchYear = $normalized['year'];
            
            $results = array_filter($results, function($item) use ($normalizedQuery, $searchYear) {
                // Cek konten dewasa
                if (isset($item->adult) && $item->adult) {
                    return false;
                }
                
                // Normalisasi judul film/TV untuk perbandingan
                $normalizedTitle = strtolower(
                    preg_replace(
                        ['/[&]/', '/[-\'\s:]+/'],
                        ['and', ''],
                        trim($item->title)
                    )
                );
                
                // Get release year
                $releaseYear = date('Y', strtotime($item->release_date));
                
                // Jika tahun dicari, cocokkan keduanya
                if ($searchYear) {
                    return str_contains($normalizedTitle, $normalizedQuery) && $releaseYear == $searchYear;
                }
                
                // Jika tidak ada tahun, cukup cocokkan judul
                return str_contains($normalizedTitle, $normalizedQuery);
            });
        }

        if ($request->ajax()) {
            return view('partials.search-results', compact('results'));
        }

        return view('search', compact('results'));
    }
}
