<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

Route::post('/check-price', function (Request $request) {
    $title = $request->input('title');
    $amazonPrice = (float)$request->input('price');

    // Nettoyage du titre (3 mots max pour la recherche)
    $words = explode(' ', preg_replace('/[^A-Za-z0-9\- ]/', '', $title));
    $shortTitle = implode(' ', array_slice($words, 0, 3));
    $searchQuery = urlencode($shortTitle);

    // Fonction de nettoyage de prix robuste
    $cleanPrice = function($priceStr) {
        $priceStr = str_replace(['Dhs', 'DH', 'MAD', ' ', "\xc2\xa0", "\xa0"], '', $priceStr);
        $priceStr = str_replace(',', '.', $priceStr);
        $priceStr = preg_replace('/[^0-9.]/', '', $priceStr);
        return (float) $priceStr;
    };

    $foundPrices = [];

    // --- SCRAPING JUMIA ---
    try {
        $htmlJumia = Http::timeout(5)->get("https://www.jumia.ma/catalog/?q=" . $searchQuery)->body();
        if (preg_match('/<div class="prc">([^<]+)<\/div>/i', $htmlJumia, $matches)) {
            $val = $cleanPrice($matches[1]);
            if ($val > 0) $foundPrices['Jumia'] = $val;
        }
    } catch (\Exception $e) {}

    // --- SCRAPING IRIS.MA ---
    try {
        $htmlIris = Http::timeout(5)->get("https://www.iris.ma/recherche?s=" . $searchQuery)->body();
        if (preg_match('/<span class="price">([^<]+)<\/span>/i', $htmlIris, $matches)) {
            $val = $cleanPrice($matches[1]);
            if ($val > 0) $foundPrices['Iris.ma'] = $val;
        }
    } catch (\Exception $e) {}

    // Analyse des résultats
    $minPrice = count($foundPrices) > 0 ? min($foundPrices) : null;
    $maxPrice = count($foundPrices) > 0 ? max($foundPrices) : null;
    $bestStore = count($foundPrices) > 0 ? array_search($minPrice, $foundPrices) : null;

    // Sauvegarde dans MySQL
    try {
        DB::table('searched_products')->insert([
            'title' => $title,
            'amazon_price' => $amazonPrice,
            'market_min' => $minPrice,
            'market_max' => $maxPrice,
            'best_store' => $bestStore,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } catch (\Exception $e) {}

    // Réponse JSON envoyée à l'extension
    return response()->json([
        'product' => $shortTitle,
        'amazon_price' => $amazonPrice,
        'market_min' => $minPrice,
        'market_max' => $maxPrice,
        'best_store' => $bestStore,
        'message' => $minPrice ? "Analyse du marché marocain terminée !" : "Aucun prix local trouvé."
    ]);
});