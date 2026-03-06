<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Ghost - Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; }
    </style>
</head>
<body class="text-slate-300 antialiased">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="flex justify-between items-center mb-10 pb-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h1 class="text-2xl font-bold text-white tracking-tight">Price Ghost <span class="text-slate-500 font-medium tracking-normal">| Analytics</span></h1>
            </div>
            
            <form action="/admin/clear" method="POST">
                @csrf 
                <button type="submit" class="flex items-center px-4 py-2 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Purger l'historique
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <h3 class="text-slate-400 text-sm font-medium">Total des requêtes</h3>
                </div>
                <p class="text-3xl font-bold text-white">{{ $totalSearches }}</p>
            </div>
            
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-slate-400 text-sm font-medium">Prix Moyen (Amazon)</h3>
                </div>
                <p class="text-3xl font-bold text-white">{{ number_format($averagePrice, 2) }} <span class="text-lg text-slate-500 font-normal">MAD</span></p>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <h3 class="text-slate-400 text-sm font-medium">Prix Maximum Scanné</h3>
                </div>
                <p class="text-3xl font-bold text-white">{{ number_format($maxPrice, 2) }} <span class="text-lg text-slate-500 font-normal">MAD</span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-base font-semibold text-white">Évolution des prix (Amazon vs Maroc)</h2>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="priceChart"></canvas>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 shadow-sm">
                <h2 class="text-base font-semibold text-white mb-6">Activité récente</h2>
                <div class="space-y-4">
                    @forelse($products->take(5) as $product)
                        @if($product->best_store)
                        <div class="flex flex-col p-4 bg-slate-800/50 rounded-lg border border-slate-700/50">
                            <p class="text-sm font-medium text-white truncate mb-1" title="{{ $product->title }}">{{ $product->title }}</p>
                            <div class="flex justify-between items-end">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-500/10 text-blue-400">
                                    {{ $product->best_store }}
                                </span>
                                <span class="text-sm font-bold text-white">{{ number_format($product->market_min, 0, ',', ' ') }} MAD</span>
                            </div>
                        </div>
                        @endif
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">Aucune donnée récente.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-8 bg-slate-900 border border-slate-800 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-800">
                <h2 class="text-base font-semibold text-white">Registre des recherches</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-900 border-b border-slate-800 text-slate-400 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-medium">Produit</th>
                            <th class="px-6 py-4 font-medium">Prix Référence (Amazon)</th>
                            <th class="px-6 py-4 font-medium">Meilleur Prix Local</th>
                            <th class="px-6 py-4 font-medium">Fournisseur</th>
                            <th class="px-6 py-4 font-medium text-right">Horodatage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($products as $product)
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-white max-w-xs truncate" title="{{ $product->title }}">
                                {{ $product->title }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-400">
                                {{ number_format($product->amazon_price, 2) }} MAD
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($product->market_min)
                                    <span class="text-emerald-400 font-medium">{{ number_format($product->market_min, 2) }} MAD</span>
                                @else
                                    <span class="text-slate-500 italic">Non listé</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-300">
                                {{ $product->best_store ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 text-right">
                                {{ \Carbon\Carbon::parse($product->created_at)->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">
                                La base de données est vide. Effectuez une recherche via l'extension.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        const productsData = @json($products->take(5)->reverse()->values());
        
        // Si aucune donnée, on arrête le script pour éviter une erreur
        if(productsData.length > 0) {
            const labels = productsData.map(p => p.title.substring(0, 20) + '...');
            const amazonPrices = productsData.map(p => p.amazon_price);
            const marketPrices = productsData.map(p => p.market_min || 0);

            const ctx = document.getElementById('priceChart').getContext('2d');
            new Chart(ctx, {
                type: 'line', // Changé en 'line' pour un look plus "Analytics"
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Prix Référence (MAD)',
                            data: amazonPrices,
                            borderColor: '#3b82f6', // blue-500
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        },
                        {
                            label: 'Prix Local (MAD)',
                            data: marketPrices,
                            borderColor: '#10b981', // emerald-500
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            position: 'top',
                            labels: { color: '#94a3b8', usePointStyle: true, boxWidth: 8 } 
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                            ticks: { color: '#64748b' }
                        },
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { color: '#64748b', maxRotation: 45, minRotation: 45 }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });
        }
    </script>
</body>
</html>