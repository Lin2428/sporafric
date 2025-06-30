<?php
namespace App\Filament\Resources\TechnicalVisitsResource\Pages;

use App\Filament\Resources\TechnicalVisitsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTechnicalVisits extends ListRecords
{
    protected static string $resource = TechnicalVisitsResource::class;
    protected static ?string $title   = 'Visites techniques';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Imprimer une fiche vide')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function ($record, $data, $livewire) {
                    $livewire->js(<<<'JS'

            const printWindow = window.open('', '', 'width=800,height=600');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Impression</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin-left: 30px;
                                margin-right: 30px;
                                margin-top: 10px;
                                margin-bottom: 10px;
                            }
                                *{
                                    font-size: 12px;
                                }
                            h2 {
                                text-align: center;
                                text-transform: uppercase;
                                margin-bottom: 20px;
                            }
                            table {
                                width: 100%;
                                border-collapse: collapse;
                            }
                            .section-title {
                                font-weight: bold;
                                margin-top: 20px;
                                margin-bottom: 10px;
                            }
                            .half {
                                width: 48%;
                                display: inline-block;
                                vertical-align: top;
                            }
                            .field {
                                margin-bottom: 5px;
                            }
                            .field label {
                                display: inline-block;
                                width: 150px;
                            }
                            .field input {
                                width: 200px;
                            }
                            .grid-table td, .grid-table th {
                                border: 1px solid #000;
                                padding: 4px;
                                text-align: start;
                            }
                            .signature-section {
                                margin-top: 30px;
                            }
                        </style>
                    </head>
                    <body>
                      <h2 style="font-size: 22px;">VISITE TECHNIQUE</h2>

                        <div class="half">
                            <div class="field"><label>MARQUE:</label><input type="text"></div>
                            <div class="field"><label>PUISSANCE:</label><input type="text"></div>
                            <div class="field"><label>N° Série:</label><input type="text"></div>
                            <div class="field"><label>Horamètre:</label><input type="text"></div>
                            <div class="field"><label>Date:</label><input type="text"></div>
                        </div>

                        <div class="half">
                            <div class="field"><label>MOTEUR N° Série:</label><input type="text"></div>
                            <div class="field"><label>ALTERNATEUR N° Série:</label><input type="text"></div>
                            <div class="field"><label>CARTE PUPITRE N° Série:</label><input type="text"></div>
                            <div class="field"><label>Inverseur:</label><input type="text"></div>
                        </div>

                        <div class="section-title">Action préalable à la visite :</div>

                        <ul>
                            <li>Arrêt du groupe électrogène</li>
                            <li>Mise en place d’une ardoise</li>
                        </ul>

                        <table class="grid-table">
                            <tr><td>Niveau d’huile Moteur</td><td></td></tr>
                            <tr><td>Niveau du Liquide de Refroidissement</td><td></td></tr>
                            <tr><td>Niveau de l’électrolyte Batterie</td><td></td></tr>
                        </table>

                        <div class="section-title">Visite :</div>
                        <table class="grid-table">
                            <tr><td>Contrôle du filtre à huile</td><td></td></tr>
                            <tr><td>Contrôle du filtre à air</td><td></td></tr>
                            <tr><td>Contrôle du filtre à carburant</td><td></td></tr>
                            <tr><td>Contrôle du circuit carburant</td><td></td></tr>
                            <tr><td>Contrôle du circuit de refroidissement</td><td></td></tr>
                            <tr><td>Contrôle de la batterie et de la densité (3ans)</td><td>1,26 à 1,28</td><td>1,22 à 1,26</td><td>&lt; 1,22</td></tr>
                            <tr><td>Contrôle de l’état et de la présence des courroies</td><td></td></tr>
                            <tr><td>Contrôle de charge de batterie</td><td></td></tr>
                            <tr><td>Contrôle de la résistance chauffante</td><td></td></tr>
                        </table>

                        <div class="section-title">Contrôle après visite :</div>
                        <table class="grid-table">
                            <tr><td>Démarrage du GE</td><td></td></tr>
                            <tr><td>Contrôle de fonctionnement du démarreur</td><td></td></tr>
                            <tr><td>Contrôle du circuit de charge moteur</td><td style="text-align:end;">V</td></tr>
                            <tr><td>Contrôle de la tension de sortie 230V</td><td style="text-align:start;">V1n</td><td style="text-align:start;">V2n</td><td style="text-align:start;">V3n</td></tr>
                            <tr><td>Contrôle de la tension de sortie 400V</td><td style="text-align:start;">U12</td><td style="text-align:start;">U13</td><td style="text-align:start;">U23</td></tr>
                            <tr><td>Contrôle de l’intensité par phase</td><td style="text-align:start;">I1</td><td style="text-align:start;">I2</td><td style="text-align:start;">I3</td></tr>
                            <tr><td>Contrôle de la fréquence</td><td style="text-align:end;">HZ</td></tr>
                            <tr><td>État du Groupe électrogène et du local</td><td></td></tr>
                        </table>
                        <div class="section-title">Contrôle fin de visite:</div>
                        <table class="grid-table">
                            <tr><td>'Etat de l'arret d'urgence</td><td></td></tr>
                            <tr><td>Mode de fonctionnement</td><td></td></tr>
                        </table>

                        <p><strong>Prochaine vidange à :</strong> <input type="text" style="width: 200px;"></p>

                        <div class="signature-section">
                            <div class="half">
                                <div class="field"><label>CLIENT:</label><input type="text"></div>
                                <div class="field"><label>ADRESSE:</label><input type="text" style="width: 80%;"></div>
                            </div>
                            <div class="half" style="text-align: right;">
                                <p>Signature:</p>
                                <br><br>
                            </div>
                        </div>
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
            JS);
                }),

            Actions\CreateAction::make(),
            // PrintAction::make(),
        ];
    }
}
