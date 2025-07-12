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
        color: rgb(107 114 128);
        font-weight: 400;
        margin-bottom: 0.5rem;
    }

    .icon {
        margin-right: 0.5rem;
        color: rgb(107 114 128);
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
</style>
<span class="text-red-800">
   {{ $getRecord()->type_service === 1 ? 'Maintenance' : ($getRecord()->type_service === 0 ? 'Location' : '') }}
</span>

<span class="text-blue-800">
{{ $getRecord()->type_activite === 1 ? '- Sous contrat' : ($getRecord()->type_activite === 0 ? '- Hors contrat' : '') }}
</span>
<br>
<br>
<div class="container-1">
    <div class="w-full">

           <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-user')</i>
                Client:
            </span>
            <span>{{ $getRecord()->contract?->customer?->name ?? ($getRecord()->devis != null ? $getRecord()->devis?->customer?->name .' '. $getRecord()->devis?->customer?->customer_name:null) ?? $getRecord()->customer?->name }}</span>
        </div>
<br>

 <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-phone')</i>
                Téléphone:
            </span>
            <span>{{ $getRecord()->contract?->customer?->contact_c_phone ??  $getRecord()->devis?->customer?->contact_c_phone ?? $getRecord()->customer?->contact_c_phone }}</span>
        </div>
<br>
        {{-- Date de prise d'appel --}}
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-phone')</i>
                Date de prise d'appel:
            </span>
            <span>{{ \App\Utils\DateUtils::format($getRecord()->date_prise_appel) }}</span>
        </div>
<br>
        {{-- Date planifiée --}}
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-calendar')</i>
                Date planifiée:
            </span>
            <span>{{ \App\Utils\DateUtils::format($getRecord()->date_planifiee) }}</span>
        </div>
<br>
        {{-- Type d'intervention --}}
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-wrench')</i>
                Type d’intervention:
            </span>
            <span>{{\App\Enum\InterventionType::from($getRecord()->type)->label() }}</span>
        </div>

    @if ($getRecord()->type == \App\Enum\InterventionType::REMPLACEMENT->value)
    <br>
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
<br>
    @endif
<br>
        {{-- Numéro de bon de livraison --}}
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-document-text')</i>
                Numéro de bon de livraison:
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
            <span>{{ $getRecord()->description_panne }}</span>
        </div>
    </div>

</div>
<br>
<hr>
<br>

<div class="container-1">
    <div class="">
        <span class="label">
            <i class="icon">@svg('heroicon-s-user-circle')</i>
            Techniciens affectés :
        </span>
        <table class="techniciens-table">
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
    </div>
    <div>

    </div>
</div>
<br>
<hr>
<br>
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

<br>
        <div class="container-1">
            <span class="label">
                <i class="icon">@svg('heroicon-s-cog')</i>
                Heure de fonctionnement du GE:
            </span>
            <span>{{$getRecord()->generator?->houres?? 0}}h</span>
        </div>

<br>
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
                        <th>Image</th>
                        <th>Prix</th>
                        <th>Usure</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($getRecord()->pieces as $piece)
                    <tr>
                        <td>{{$piece->designation}}</td>
                        <td>{{$piece->reference}}</td>
                        <td><img class="piece-img" src="{{asset('storage/'.$piece->image)}}" alt="">
                        </td>
                        <td>
                            {{\App\Utils\NumberUtils::format($piece->pv)}} FCFA
                        </td>

                        <td>
                            {{ $piece->duree_vie}} h
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
