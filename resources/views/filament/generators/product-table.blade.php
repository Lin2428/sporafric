@if (!empty($getLivewire()->products))
<table class="w-full text-sm border border-gray-200 rounded">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2 text-left">id</th>
            <th class="px-4 py-2 text-left">Product</th>
            <th class="px-4 py-2 text-left">Réference</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($getLivewire()->products as $product)
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-2">{{ $product['id']}}</td>
            <td class="px-4 py-2">{{ $product['name'] }}</td>
            <td class="px-4 py-2">{{ $product['default_code'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
<style>
    .techniciens-photo {
        width: 70px;
        height: 60px;
        margin-left: 1rem;
        object-fit: cover;
    }
</style>
