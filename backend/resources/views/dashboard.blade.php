<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Ghost Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans leading-normal tracking-normal text-gray-800">

    <div class="container mx-auto px-4 py-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter">
                    Price Ghost <span class="text-blue-600 underline">Admin</span>
                </h1>
                <p class="text-gray-500 font-medium">Analyse du marché en temps réel</p>
            </div>
            
            <form action="/admin/clear" method="POST" onsubmit="return confirm('Voulez-vous vraiment vider la base ?');">
                @csrf
                <button type="submit" class="bg-white border-2 border-red-500 text-red-500 hover:bg-red-500 hover:text-white px-5 py-2 rounded-xl font-bold transition duration-200">
                    Vider la base
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs uppercase font-bold tracking-widest mb-1">Total Scans</p>
                <p class="text-3xl font-black text-gray-900">{{ $totalSearches }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs uppercase font-bold tracking-widest mb-1">Prix Moyen (Amazon)</p>
                <p class="text-3xl font-black text-blue-600">{{ number_format($averagePrice, 2) }} <span class="text-sm font-normal">MAD</span></p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <p class="text-gray-400 text-xs uppercase font-bold tracking-widest mb-1">Record Prix Max</p>
                <p class="text-3xl font-black text-gray-900">{{ number_format($maxPrice, 2) }} <span class="text-sm font-normal text-gray-400">MAD</span></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-900 text-white uppercase text-xs font-bold tracking-wider">
                            <th class="px-6 py-4">Produit (Lien Amazon)</th>
                            <th class="px-6 py-4 text-center">Prix Amazon</th>
                            <th class="px-6 py-4 text-center text-orange-400">Min Maroc</th>
                            <th class="px-6 py-4 text-center">Boutique</th>
                            <th class="px-6 py-4 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @foreach($products as $product)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4">
                                <a href="https://www.amazon.com/s?k={{ urlencode($product->title) }}" target="_blank" class="font-bold text-blue-600 hover:text-blue-800 hover:underline block">
                                    {{ Str::limit($product->title, 50) }} 🔗
                                </a>
                            </td>
                            
                            <td class="px-6 py-4 text-center font-semibold text-gray-900">
                                {{ number_format($product->amazon_price, 2) }} MAD
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if($product->market_min)
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-bold">
                                        {{ number_format($product->market_min, 2) }} MAD
                                    </span>
                                @else
                                    <span class="text-gray-300 italic">Non trouvé</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if($product->best_store)
                                    <a href="https://www.{{ strtolower($product->best_store) }}.ma/catalog/?q={{ urlencode(Str::limit($product->title, 20, '')) }}" target="_blank" class="text-xs font-bold px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-600 uppercase tracking-tighter transition">
                                        {{ $product->best_store }} ↗️
                                    </a>
                                @else
                                    <span class="text-gray-300">N/A</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-right text-gray-400 text-sm">
                                {{ \Carbon\Carbon::parse($product->created_at)->format('d/m à H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>