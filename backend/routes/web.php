<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/* ---------------------------------------------------
   1. LE MOTEUR DE RECHERCHE MULTI-STORES & CATÉGORIES
--------------------------------------------------- */
Route::post('/check-price', function (Request $request) {
    $title = $request->input('title');
    $amazonPrice = (float)$request->input('price');
    $category = strtolower($request->input('category', '')); // On récupère la catégorie envoyée par l'extension

    // Nettoyage du titre (3 mots max pour une meilleure recherche)
    $words = explode(' ', preg_replace('/[^A-Za-z0-9\- ]/', '', $title));
    $shortTitle = implode(' ', array_slice($words, 0, 3));
    $searchQuery = urlencode($shortTitle);

    // Fonction pour nettoyer les prix
    $cleanPrice = function($priceStr) {
        $str = str_replace(['Dhs', 'DH', 'MAD', ' ', "\xc2\xa0", "\xa0"], '', $priceStr);
        $str = preg_replace('/[,.]00$/', '', $str); // Enlève les faux centimes
        $str = str_replace([',', '.'], '', $str); // Enlève les séparateurs de milliers
        return (float) $str;
    };

    // 📚 LE GRAND CATALOGUE DES BOUTIQUES MAROCAINES
    $allStores = [
        'Jumia'         => ['url' => "https://www.jumia.ma/catalog/?q=$searchQuery", 'regex' => '/<div class="prc">([^<]+)<\/div>/i'],
        'MarjaneMall'   => ['url' => "https://www.marjanemall.ma/catalogsearch/result/?q=$searchQuery", 'regex' => '/<span class="price">([^<]+)<\/span>/i'],
        'Electroplanet' => ['url' => "https://www.electroplanet.ma/catalogsearch/result/?q=$searchQuery", 'regex' => '/<span class="price">([^<]+)<\/span>/i'],
        'Iris.ma'       => ['url' => "https://www.iris.ma/recherche?s=$searchQuery", 'regex' => '/<span class="price">([^<]+)<\/span>/i'],
        'SetupGame'     => ['url' => "https://setupgame.ma/?s=$searchQuery&post_type=product", 'regex' => '/<bdi>([^<&]+)/i'],
        'Bricoma'       => ['url' => "https://www.bricoma.ma/catalogsearch/result/?q=$searchQuery", 'regex' => '/<span class="price">([^<]+)<\/span>/i'],
        'Kitea'         => ['url' => "https://www.kitea.com/catalogsearch/result/?q=$searchQuery", 'regex' => '/<span class="price">([^<]+)<\/span>/i']
    ];

    // 🤖 L'INTELLIGENCE DU ROUTAGE : On choisit les magasins selon la catégorie Amazon
    $storesToScan = ['Jumia', 'MarjaneMall']; // Par défaut (Généralistes)

    // Si on est dans la tech, les téléphones ou les jeux
    if (str_contains($category, 'electronic') || str_contains($category, 'computer') || str_contains($category, 'cell phone') || str_contains($category, 'gaming') || str_contains($category, 'video games')) {
        $storesToScan = ['Jumia', 'Electroplanet', 'Iris.ma', 'SetupGame'];
    } 
    // Si on est dans la maison, cuisine ou bricolage
    elseif (str_contains($category, 'home') || str_contains($category, 'kitchen') || str_contains($category, 'tools') || str_contains($category, 'garden')) {
        $storesToScan = ['Jumia', 'MarjaneMall', 'Bricoma', 'Kitea'];
    }

    $foundPrices = [];

    // On scanne UNIQUEMENT les magasins sélectionnés
    foreach ($storesToScan as $storeName) {
        $data = $allStores[$storeName];
        try {
            // Requête HTTP avec un timeout court pour ne pas bloquer le serveur
            $html = Http::timeout(3)->get($data['url'])->body();
            if (preg_match($data['regex'], $html, $matches)) {
                $val = $cleanPrice($matches[1]);
                if ($val > 50) { // Filtre de sécurité anti-câbles
                    $foundPrices[$storeName] = $val;
                }
            }
        } catch (\Exception $e) {
            continue; // Si un site plante, on l'ignore et on passe au suivant
        }
    }

    // Analyse des gagnants
    $minPrice = count($foundPrices) > 0 ? min($foundPrices) : null;
    $bestStore = count($foundPrices) > 0 ? array_search($minPrice, $foundPrices) : null;
    $bestLink = $bestStore ? $allStores[$bestStore]['url'] : null;

    // Sauvegarde en base de données
    try {
        DB::table('searched_products')->insert([
            'title' => $title,
            'amazon_price' => $amazonPrice,
            'market_min' => $minPrice,
            'best_store' => $bestStore,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } catch (\Exception $e) {}

    // Réponse
    return response()->json([
        'product' => $shortTitle,
        'market_min' => $minPrice,
        'best_store' => $bestStore,
        'best_link' => $bestLink,
        'scanned_stores' => $storesToScan, // Optionnel : pour voir dans la console qui a été scanné
        'message' => $minPrice ? "Produit trouvé au Maroc !" : "Aucun revendeur local trouvé."
    ]);
});

/* ---------------------------------------------------
   2. LES ROUTES DU DASHBOARD ADMIN
--------------------------------------------------- */
Route::get('/admin', function () {
    $products = DB::table('searched_products')->orderBy('created_at', 'desc')->get();
    return view('dashboard', [
        'products' => $products,
        'totalSearches' => DB::table('searched_products')->count(),
        'averagePrice' => DB::table('searched_products')->avg('amazon_price') ?: 0,
        'maxPrice' => DB::table('searched_products')->max('amazon_price') ?: 0
    ]);
});

Route::post('/admin/clear', function () {
    DB::table('searched_products')->truncate();
    return redirect('/admin');
});