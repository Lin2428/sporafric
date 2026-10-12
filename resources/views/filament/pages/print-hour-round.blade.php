<x-filament-panels::page>
<div id="printable">
 <style>
    table.generator-table {
        border-collapse: collapse;
        width: 100%;
        margin: 0px;
    }

    table.generator-table th,
    table.generator-table td {
        border: 1px solid gray;
        padding: 8px;
        font-size: 12px;
        text-align: left;
    }

    table.generator-table th {
        font-weight: bold;
        color: #1e293b;
        background-color: #f1f5f9;
    }

    .text-blue {
        color: #2563eb;
    }

    @media print {
        .fi-header {
            display: none;
        }

        @page {
            margin: 20px 40px 10px 40px; /* top, right, bottom, left */
        }

        body {
            margin: 0;
        }
    }
</style>

<div class="flex justify-end my-4 fi-header">
    <x-filament::button onclick="printHourRound()" icon="heroicon-s-printer">Imprimer</x-filament::button>
</div>

@include('components.report-header')

<h3 class="font-bold text-lg mt-3">Fiche de relevés d'heures</h3>
<p class="mt-3">
    Technicien : {{ $this->record->technicien?->name }}
    &nbsp;·&nbsp; Date : {{ $this->record->date?->format('d/m/Y') }}
</p>
@if ($this->record->notes)
    <p class="mt-1">Consignes : {{ $this->record->notes }}</p>
@endif
<br>

@foreach ($groupes as $client => $readings)
    <div class="text-center border border-b-0 bg-[#f1f5f9] border-gray-600 m-0 p-2">{{ $client }}</div>

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
            </tr>
        </thead>
        <tbody>
            @foreach ($readings as $reading)
                <tr>
                    <td class="text-blue">{{ $reading->site }}</td>
                    <td>{{ $reading->generator?->adresse }}</td>
                    <td>{{ $reading->code_site }}</td>
                    <td>{{ $reading->generator?->name }}</td>
                    <td style="text-align: right;">{{ $reading->generator?->power }}</td>
                    <td style="text-align: right;">{{ $reading->houres }}</td>
                    <td style="text-align: right;">{{ $reading->prochain_visite ?? $reading->generator?->prochain_visite }}</td>
                    <td style="text-align: right;">{{ $reading->next_vidange ?? $reading->generator?->next_vidange }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
@endforeach

<script>
    // Inline (et non pub.js) : le panel est en ->spa(), l'événement « load » ne se redéclenche pas.
    function printHourRound() {
        const printWindow = window.open('', '', 'height=1200,width=800');
        printWindow.document.write('<html lang="fr"><head>' + document.head.innerHTML + '</head><body>');
        printWindow.document.write(document.querySelector('#printable').innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.addEventListener('load', function () {
            printWindow.focus();
            printWindow.print();
        });
    }
</script>
</div>
</x-filament-panels::page>
