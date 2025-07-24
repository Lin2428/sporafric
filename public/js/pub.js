
console.log('Hello from app.js');
window.addEventListener('load', function () {
    const printReportButton = document.querySelector('#print-form-etat');
    if (printReportButton) {
        printReportButton.addEventListener('click', () => {
            
            const printableArea = document.querySelector('#printable').innerHTML;
            const printWindow = window.open('', '', 'height=1200,width=800');

            printWindow.document.write('<html lang="fr"><head>');
            // Ajoute les styles CSS pour le format de papier
            printWindow.document.write('<style>@media print {tfoot { display: table-footer-group;} @page { size:landscape; marks:none;size: 21.0cm;margin: 0cm ;   body {margin: 0;} }  .no-print { display: none !important; } .invisible {display: block!important;} } </style>');
            // Copie le contenu de la balise <head> actuelle
            printWindow.document.write(document.head.innerHTML);
            printWindow.document.write('</head><body>');
            // Copie le contenu de la zone imprimable
            printWindow.document.write(printableArea);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.addEventListener('load', function() {
                printWindow.focus();
                printWindow.print();
            });
        });

    const printReportButton2 = document.querySelector('#print-form-etat-vide');
    if (printReportButton2) {
        printReportButton2.addEventListener('click', () => {
            
            const printableArea = document.querySelector('#printable_vide').innerHTML;
            const printWindow = window.open('', '', 'height=1200,width=800');

            printWindow.document.write('<html lang="fr"><head>');
            // Ajoute les styles CSS pour le format de papier
            printWindow.document.write('<style>@media print {tfoot { display: table-footer-group;} @page { size:landscape; marks:none;size: 21.0cm;margin: 0cm ;   body {margin: 0;} }  .no-print { display: none !important; } .invisible {display: block!important;} } </style>');
            // Copie le contenu de la balise <head> actuelle
            printWindow.document.write(document.head.innerHTML);
            printWindow.document.write('</head><body>');
            // Copie le contenu de la zone imprimable
            printWindow.document.write(printableArea);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.addEventListener('load', function() {
                printWindow.focus();
                printWindow.print();
            });
        });
    }
    }



    
});
