@php

    if ($this->data == null) {
        $groupes = collect();
    } else {
        $groupes = $this->data->groupBy(function ($generator) {
            // Retourne le customer_id selon la relation existante
            return $generator->contractGenerator?->contract?->customer_id
                ?? $generator->devisGenerator?->devis?->customer_id;
        });
    }
@endphp

<div id="printable">
 <style>
    table.generator-table {
        border-collapse: collapse;
        width: 100%;
        margin: 0px;
    }

.table-item:hover {
    background-color: #a9caf48f;
}

    table.generator-table th,
    table.generator-table td {
        border: 1px solid gray; /* équivalent de slate-400 */
        padding: 8px;
        font-size: 12px;
        text-align: left;
    }

    table.generator-table th {
        font-weight: bold;
        color: #1e293b; /* équivalent de slate-800 */
        background-color: #f1f5f9;
    }

    .text-blue {
        color: #2563eb; /* équivalent de blue-600 */
    }

    @media print {
    .fi-header, .form {
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
<div class="flex justify-between my-4 fi-header">
    <h3 class="font-bold text-3xl">Impressionde fiche</h3>
   <div>
    <x-filament::button onclick="restoreRow()" id="restore-btn" style="background-color: blue;" class="mt-2 px-4 py-1 bg-blue-500 text-white rounded hidden">Annuler la dernière suppression</x-filament::button>

    <x-filament::button id="print-form-etat" class="flex justify-between" icon="heroicon-s-printer"><span>Imprimer</span></x-filament::button>

   </div>
</div>
@include('components.report-header')

    <div class="form mt-2">
       {{$this->form}}
    </div>

<h3 class="font-bold text-lg mt-3">Fiche de visite technique</h3>
<p class="mt-3">Technicien : {{ $this->selectTechniciens }}</p>
 <br>
    @foreach ($groupes as $key => $client)
    <div class="text-center border border-b-0  bg-[#f1f5f9] border-gray-600 m-0 p-2">
    {{ $client->first()->contractGenerator?->contract?->customer?->name ?? $client->first()->devisGenerator?->devis?->customer?->name }}
</div>


<table class="generator-table">
    <thead>
        <tr>
            <th>Site</th>
            <th>Adresse</th>
            <th>Code</th>
            <th>GE</th>
            <th>P (kVA)</th>
            <th>HF</th>
            <th>VP</th>
            <th>TAV</th>
            <th class="fi-header">Action</th>
        </tr>
    </thead>
    <tbody>
        @if ($data->isNotEmpty())
            @foreach ($client as $index => $item)
                <tr id="row-{{$key}}-{{ $index }}" class="table-item">
                    <td class="text-blue">
                        {{ $item->contractGenerator?->site ?? $item->devisGenerator?->devis?->site }}
                    </td>
                    <td>{{ $item->adresse }}</td>
                    <td>{{ $item->contractGenerator?->code_site ?? $item->devisGenerator?->devis?->code_site }}</td>
                    <td>{{ $item->name }}</td>
                    <td style="text-align: right;">{{ $item->power }}</td>
                    <td></td>
                    <td style="text-align: right;">{{ $item->prochain_visite }}</td>
                    <td style="text-align: right;">{{ $item->next_vidange }}</td>
                    <td class="fi-header">
                        <button onclick="removeRow('row-{{$key}}-{{ $index }}')"
                            class="text-red-500 hover:underline" style="text-align: center; color: red;">Supprimer</button>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
 <br>
    @endforeach

    <script>
    let lastDeletedRow = null;

    function removeRow(id) {
        const row = document.getElementById(id);
        if (row) {
            row.classList.add('fi-header');
            lastDeletedRow = row.cloneNode(true);
            console.log(lastDeletedRow);
            row.remove();
            document.getElementById('restore-btn').classList.remove('hidden');
        }
    }

    function restoreRow() {
        if (lastDeletedRow) {
            const tbody = document.querySelector(".generator-table tbody");
            tbody.appendChild(lastDeletedRow);
            lastDeletedRow = null;
            document.getElementById('restore-btn').classList.add('hidden');
        }
    }

</script>
<script src="{{ asset('js/pub.js') }}"></script>
</div>
