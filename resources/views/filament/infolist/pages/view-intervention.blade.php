<style>
    .container-1 {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
    }

    .label {
        display: flex;
        align-items: center;
        font-size: 1rem;
        line-height: 1.25rem;
        color: black;
        font-weight: 400;
        margin-bottom: 0.5rem;
    }

    .icon {
        margin-right: 0.5rem;
        color: black;
        display: flex;
        align-items: center;
    }

    .icon svg {
        width: 18px;
        height: 18px;
    }

    .data-block {
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 2.5rem;
    }

    /* Technicien */
    <style>.techniciens-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.5rem;
        font-size: 14px;
    }

    .techniciens-table th,
    .techniciens-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .techniciens-table th {
        background-color: #f3f4f6;
        color: #374151;
    }

    .techniciens-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    /*PIECES*/
    .materiel-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.5rem;
        font-size: 14px;
    }

    .materiel-table th,
    .materiel-table td {
        border: 1px solid #d1d5db;
        padding: 6px 8px;
        text-align: left;
    }

    .materiel-table th {
        background-color: #2563eb;
        /* bleu */
        color: #ffffff;
        font-weight: 600;
    }

    .materiel-table tbody tr:hover {
        background-color: #f3f4f6;
        /* gris très clair au hover */
    }

    .piece-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
    }

    .container-2 {
        display: flex;
        flex-direction: column;
        justify-content: end;
        align-items: end;

        margin-left: 1rem;
    }

    .pdf-flex {
        display: flex;
        gap: 1rem;
    }

@media print {
    .fi-header {
        display: none;
    }

    @page {
        margin: 20px 40px 10px 40px; /* top, right, bottom, left */
    }

    body {
        margin: 0; /* Réinitialise les marges internes */
    }
}
</style>
<strong style="font-size: 1.3em;">
   {{ 
    $getRecord()->contract?->customer?->name 
    ?? (
        $getRecord()->devis?->customer?->name || $getRecord()->devis?->customer_name 
            ? trim(
                ($getRecord()->devis?->customer?->name ?? '') 
                . ' ' . 
                ($getRecord()->devis?->customer_name ?? '')
              )
            : null
    ) 
    ?? $getRecord()->customer?->name 
     }}
</strong><br>
<br>
<div class="container-1">
    <div class="w-full">

           <div class="container-1">
            <span class="label">
                <i class="icon">@svg('icon-generator')</i>
                GE:
            </span>
            <span>{{ $getRecord()->generator?->name ?? $getRecord()->generator_name }}</span>
            <!-- <span>{{ $getRecord()->contract?->customer?->name ?? ($getRecord()->devis != null ? $getRecord()->devis?->customer?->name .' '. $getRecord()->devis?->customer?->customer_name:null) ?? $getRecord()->customer?->name }}</span> -->
        </div>
          {{-- Type d'intervention --}}
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-wrench')</i>
                Type d’intervention:
            </span>
            <span>{{\App\Enum\InterventionType::from($getRecord()->type)->label() }}</span>
        </div>
<div class="container-1">
    <div class="w-full">

           <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-home')</i>
                Site:
            </span>
            <span>{{ $getRecord()->generator?->contractGenerator?->site ?? $getRecord()->generator?->devisGenerator?->site }}</span>
            <!-- <span>{{ $getRecord()->contract?->customer?->name ?? ($getRecord()->devis != null ? $getRecord()->devis?->customer?->name .' '. $getRecord()->devis?->customer?->customer_name:null) ?? $getRecord()->customer?->name }}</span> -->
        </div>
 <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-phone')</i>
                Téléphone:
            </span>
            <span>{{ $getRecord()->contract?->customer?->contact_c_phone ??  $getRecord()->devis?->customer?->contact_c_phone ?? $getRecord()->customer?->contact_c_phone }}</span>
        </div>
        {{-- Date de prise d'appel --}}
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-calendar')</i>
                Date de prise d'appel:
            </span>
            <span>{{ \App\Utils\DateUtils::format($getRecord()->date_prise_appel) }}</span>
        </div>
        {{-- Date planifiée --}}
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-calendar')</i>
                Date planifiée:
            </span>
            <span>{{ \App\Utils\DateUtils::format($getRecord()->date_planifiee) }}</span>
        </div>
      
    @if ($getRecord()->type == \App\Enum\InterventionType::REMPLACEMENT->value)
        {{-- Numéro de bon de livraison --}}
        <a href="{{ url('admin/generators/' . $getRecord()->newGenerator->id) }}">
            <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-arrow-path')</i>
                Nouveau GE:
            </span>
            <span>{{ $getRecord()->newGenerator->name }}</span>
        </div>
        </a>
    @endif
        {{-- Numéro de bon de livraison --}}
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-document-text')</i>
                Numéro de bon de travaux:
            </span>
            <span>{{ $getRecord()->identifiant }}</span>
        </div>
        <br>

        {{-- Description panne / travaux --}}
        <div class="">
            <span class="label">
                <i class="icon">@svg('heroicon-s-clipboard-document')</i>
                Description :
            </span>
            <div>{!! $getRecord()->description_panne !!}</div>
        </div>
        <br>
            {{-- Description panne / travaux --}}
        <div class="">
            <span class="label">
                <i class="icon">@svg('heroicon-s-clipboard-document')</i>
                Travaux effectué  :
            </span>
            <div>{!!   $getRecord()->travaux !!}</div>
        </div>
    </div>

     
    

</div>
<br>
<hr>
<br>

<div class="data">
    <div class="">
        <span class="label">
            <i class="icon">@svg('heroicon-s-user-circle')</i>
            Techniciens affectés :
        </span>
        <table class="materiel-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Fonction</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($getRecord()->interventionTechniciens as $technicien)
                <tr>
                    </td>
                    <td>{{ $technicien->name }}</td>
                    <td>{{ $technicien->job }}</td>
                    <td>{{ $technicien->phone }}</td>
                    <td>{{ $technicien->email }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    </div>
    <div>

    </div>
</div>
<div class="container-1">
    <div class="w-full">

        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-clock')</i>
                Délai d'intervention:
            </span>
            <span>{{ \App\Utils\DateUtils::format($getRecord()->start_date) }} - {{
                \App\Utils\DateUtils::format($getRecord()->end_date) }}</span>
        </div>
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-cog')</i>
                Heure de fonctionnement du GE:
            </span>
            <span>{{$getRecord()->generator?->houres?? 0}}h</span>
        </div>
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-arrow-path-rounded-square')</i>
                Temps restant avant vidange:
            </span>
            <span>{{$getRecord()->generator?->next_vidange?? 0}}h</span>
        </div>
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-clipboard-document-check')</i>
                Vidange programmée:
            </span>
            <span>{{$getRecord()->generator?->prochain_visite ?? 0}}h</span>
        </div>
        <div class="data">
            <span class="label">
                <i class="icon">@svg('heroicon-s-cog-6-tooth')</i>
                Matériel livré/installé
            </span>
            @if($getRecord()->pieces->isNotEmpty())
            <table class="materiel-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Désignation</th>
                        <th>Quantité</th>
                        <th>Prix Unitaire</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($getRecord()->pieces as $piece)
            
                    <tr>
                        <td>{{$piece->reference}}</td>
                        <td>{{$piece->designation}}</td>
                        <!-- <td><img class="piece-img" src="{{asset('storage/'.$piece->image)}}" alt=""> -->
                        </td>
                        
                        <td>
                            {{ $piece->pivot->qty}}
                        </td>
                        <td>
                            {{\App\Utils\NumberUtils::format($piece->pivot->price)}} FCFA
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <span>Aucune pièce livrée</span>
            @endif
        </div>
    </div>

</div>
