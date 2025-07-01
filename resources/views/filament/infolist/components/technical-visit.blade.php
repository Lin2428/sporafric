<style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
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
            text-align: center;
        }
        .signature-section {
            margin-top: 30px;
        }
    </style>
<div id="technical-visit">

<h2>VISITE TECHNIQUE</h2>

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
    <li>Niveau d’huile Moteur</li>
    <li>Niveau du Liquide de Refroidissement</li>
    <li>Niveau de l’électrolyte Batterie</li>
</ul>

<div class="section-title">Visite :</div>
<table class="grid-table">
    <tr><td>Contrôle du filtre à huile</td><td></td></tr>
    <tr><td>Contrôle du filtre à air</td><td></td></tr>
    <tr><td>Contrôle du filtre à carburant</td><td></td></tr>
    <tr><td>Contrôle du circuit carburant</td><td></td></tr>
    <tr><td>Contrôle du circuit de refroidissement</td><td></td></tr>
    <tr><td>Contrôle de la batterie et de la densité (3ans)</td><td></td></tr>
    <tr><td>Contrôle de l’état et de la présence des courroies</td><td></td></tr>
    <tr><td>Contrôle de charge de batterie</td><td></td></tr>
    <tr><td>Contrôle de la résistance chauffante</td><td></td></tr>
</table>

<div class="section-title">Contrôle après visite :</div>
<table class="grid-table">
    <tr><td>Démarrage du GE</td><td></td></tr>
    <tr><td>Contrôle de fonctionnement du démarreur</td><td></td></tr>
    <tr><td>Contrôle du circuit de charge moteur</td><td></td></tr>
</table>

<table class="grid-table" style="margin-top:10px;">
    <tr>
        <th colspan="4">Contrôle de la tension de sortie</th>
    </tr>
    <tr>
        <th></th><th>230V</th><th colspan="2">400V</th>
    </tr>
    <tr>
        <td></td>
        <td>V</td>
        <td>U<sub>12</sub></td>
        <td>U<sub>23</sub></td>
        <td>U<sub>31</sub></td>
    </tr>
    <tr>
        <td></td><td></td><td></td><td></td><td></td>
    </tr>
</table>

<table class="grid-table" style="margin-top:10px;">
    <tr>
        <td>Contrôle de l’intensité par phase</td><td></td>
    </tr>
    <tr>
        <td>Contrôle de la fréquence</td><td>HZ</td>
    </tr>
</table>

<p><strong>État du Groupe électrogène et du local :</strong></p>
<textarea style="width: 100%; height: 50px;"></textarea>

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
</div>