@props([
    'numero' => null,
    'date' => null,
])

<div>
    <div class="flex  justify-content-between justify-between">
        <div class="">
            <img src="{{ asset('storage/logo_light.png') }}" alt="Logo sporafric" class="w-[210px] h-[20px] object-contain">
            <br>
            <span>Avenue Georges Dumond, </span>
            <span>Pointe-Noire</span>
            <div class="">
                <span>https://www.sporafric.net - 05 208 80 03</span>
            </div>
        </div>
        <div class="text-right">
            <h3 class="font-bold text-base">SERVICE TECHNIQUE</h3>
            <p class="text-sm">Pointe-Noire: 05 208 80 08</p>
            <p class="text-sm">Brazzaville: 05 208 80 13</p>
            @if ($numero != null)
                <div class="p-3 bg-gray-200 mb-2 text-center" style="border: solid 1px black">
                <p class="font-bold">N° : {{ $numero }}</p>
                <p class="">{{$date }}</p>
            </div>
            @endif
            
        </div>
    </div>
    <hr>
</div>