@if (!empty($getLivewire()->products))
<table class="w-full text-sm border border-gray-200 rounded">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2 text-left">Image</th>
            <th class="px-4 py-2 text-left">Modèle</th>
            <th class="px-4 py-2 text-left">Marque</th>
            <th class="px-4 py-2 text-left">Puissance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($getLivewire()->products as $product)
        <tr class="hover:bg-gray-50">
            <td><img src="{{ asset('storage/' . $product['image']) }}" alt="Photo" class="techniciens-photo">
            <td class="px-4 py-2">{{ $product['modele']}}</td>
            <td class="px-4 py-2">{{ $product['name'] }}</td>
            <td class="px-4 py-2">{{ $product['power'] }} KVA</td>
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
