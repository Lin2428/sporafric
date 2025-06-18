
<table class="w-full text-sm border border-gray-200 rounded">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2 text-left">id</th>
            <th class="px-4 py-2 text-left">Product</th>
            <th class="px-4 py-2 text-left">Date d'écheance</th>
        </tr>
    </thead>
    <tbody>
        @if (!empty($getLivewire()->devis))
        @foreach ($getLivewire()->devis['orders'] as $product)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-2">{{ $product['id']}}</td>
            <td class="px-4 py-2">{{ $product['name'] }}</td>
            <td class="px-4 py-2">{{ \App\Utils\DateUtils::format($product['expected_date']) }}</td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>

<style>
    .techniciens-photo {
        width: 70px;
        height: 60px;
        margin-left: 1rem;
        object-fit: cover;
    }
</style>
