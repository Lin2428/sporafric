<?php
namespace App\Filament\Resources\TechnicalVisitsResource\Pages;

use App\Filament\Resources\TechnicalVisitsResource;
use Filament\Actions;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditTechnicalVisits extends EditRecord
{
    protected static string $resource = TechnicalVisitsResource::class;

    protected static ?string $title = 'Modifier la visite technique';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            Actions\Action::make('print')
                ->label('Imprimer')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->action(function ($record, $data, $livewire) {
               
                    function isTrueString($value) {
                        return $value ? 'Oui' : 'Non';
                    }

                    $control1 = $record->control_1 ? 'Oui' : 'Non';
                    $control2 = $record->control_2 ? 'Oui' : 'Non';
                    $control3 = $record->control_3 ? 'Oui' : 'Non';
                    $control4 = $record->control_4 ? 'Oui' : 'Non';
                    $control5 = $record->control_5 ? 'Oui' : 'Non';
                    $control6 = $record->control_6 ? 'Oui' : 'Non';
                    $control7 = $record->control_7 ? 'Oui' : 'Non';
                    $control8 = $record->control_8 ? 'Oui' : 'Non';
                    $control9 = $record->control_9 ? 'Oui' : 'Non';
                    $control10 = $record->control_10 ? 'Oui' : 'Non';
                    $control11 = $record->control_11 ? 'Oui' : 'Non';
                    $control12 = $record->control_12 ? 'Oui' : 'Non';
                    $control13 = $record->control_13 ? 'Oui' : 'Non';
                    $control14 = $record->control_14 ? 'Oui' : 'Non';
                    $control15 = $record->control_15 ? 'Oui' : 'Non';
                    $control16 = $record->control_16 ? 'Oui' : 'Non';

                    $controlTension1 = $record->control_tension['v1'];
                    $controlTension2 = $record->control_tension['v2'];
                    $controlTension3 = $record->control_tension['v3'];

                    $controlTension4 = $record->control_tension_2['u1'];
                    $controlTension5 = $record->control_tension_2['u2'];
                    $controlTension6 = $record->control_tension_2['u3'];

                    $controlIntensite1 = $record->control_intensite['i1'];
                    $controlIntensite2 = $record->control_intensite['i2'];
                    $controlIntensite3 = $record->control_intensite['i3'];

        $livewire->js(<<<JS

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
                            <tr><td>Niveau d’huile Moteur</td><td>$control1</td></tr>
                            <tr><td>Niveau du Liquide de Refroidissement</td><td>$control2</td></tr>
                            <tr><td>Niveau de l’électrolyte Batterie</td><td>$control3</td></tr>
                        </table>

                        <div class="section-title">Visite :</div>
                        <table class="grid-table">
                            <tr><td>Contrôle du filtre à huile</td><td>$control4</td></tr>
                            <tr><td>Contrôle du filtre à air</td><td>$control5</td></tr>
                            <tr><td>Contrôle du filtre à carburant</td><td>$control6</td></tr>
                            <tr><td>Contrôle du circuit carburant</td><td>$control7</td></tr>
                            <tr><td>Contrôle du circuit de refroidissement</td><td>$control8</td></tr>
                            <tr><td>Contrôle de la batterie et de la densité (3ans)</td><td>1,26 à 1,28</td><td>1,22 à 1,26</td><td>&lt; 1,22</td></tr>
                            <tr><td>Contrôle de l’état et de la présence des courroies</td><td>$control9</td></tr>
                            <tr><td>Contrôle de charge de batterie</td><td>$control10</td></tr>
                            <tr><td>Contrôle de la résistance chauffante</td><td>$control11</td></tr>
                        </table>

                        <div class="section-title">Contrôle après visite :</div>
                        <table class="grid-table">
                            <tr><td>Démarrage du GE</td><td>$control12</td></tr>
                            <tr><td>Contrôle de fonctionnement du démarreur</td><td>$control13</td></tr>
                            <tr><td>Contrôle du circuit de charge moteur</td><td style="text-align:end;">$record->control_circuit V</td></tr>
                            <tr><td>Contrôle de la tension de sortie 230V</td><td style="text-align:start;">V1n $controlTension1</td><td style="text-align:start;">V2n $controlTension2</td><td style="text-align:start;">V3n $controlTension3</td></tr>
                            <tr><td>Contrôle de la tension de sortie 400V</td><td style="text-align:start;">U12 $controlTension4</td><td style="text-align:start;">U13 $controlTension5</td><td style="text-align:start;">U23 $controlTension6</td></tr>
                            <tr><td>Contrôle de l’intensité par phase</td><td style="text-align:start;">I1 $controlIntensite1</td><td style="text-align:start;">I2 $controlIntensite2</td><td style="text-align:start;">I3 $controlIntensite3</td></tr>
                            <tr><td>Contrôle de la fréquence</td><td style="text-align:end;">$record->control_frequence HZ</td></tr>
                            <tr><td>État du Groupe électrogène et du local</td><td>$control14</td></tr>
                        </table>
                        <div class="section-title">Contrôle fin de visite:</div>
                        <table class="grid-table">
                            <tr><td>'Etat de l'arret d'urgence</td><td>$control15</td></tr>
                            <tr><td>Mode de fonctionnement</td><td>$control16</td></tr>
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
    })
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['control_1']  = in_array('control_1', $data['checklist_1']);
        $data['control_2']  = in_array('control_2', $data['checklist_1']);
        $data['control_3']  = in_array('control_3', $data['checklist_1']);
        $data['control_4']  = in_array('control_4', $data['checklist_2']);
        $data['control_5']  = in_array('control_5', $data['checklist_2']);
        $data['control_6']  = in_array('control_6', $data['checklist_2']);
        $data['control_7']  = in_array('control_7', $data['checklist_2']);
        $data['control_8']  = in_array('control_8', $data['checklist_2']);
        $data['control_9']  = in_array('control_9', $data['checklist_2']);
        $data['control_10'] = in_array('control_10', $data['checklist_2']);
        $data['control_11'] = in_array('control_11', $data['checklist_2']);
        $data['control_12'] = in_array('control_12', $data['checklist_3']);
        $data['control_13'] = in_array('control_13', $data['checklist_3']);
        $data['control_14'] = in_array('control_14', $data['checklist_3']);
        $data['control_15'] = in_array('control_15', $data['checklist_4']);
        $data['control_16'] = in_array('control_16', $data['checklist_4']);

        unset($data['checklist_1']);
        unset($data['checklist_2']);
        unset($data['checklist_3']);
        unset($data['checklist_4']);

        return $data;
    }

    public function getProductViewField(): ViewField
    {
        return ViewField::make('devis_table')->view('filament.infolist.components.technical-visits');
    }
}
