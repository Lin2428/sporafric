
console.log('Hello from app.js');
window.addEventListener('load', function () {
    const printReportButton = document.querySelector('#print-form-etat');
    if (printReportButton) {
        printReportButton.addEventListener('click', () => {
            
            const printableArea = document.querySelector('#printable').innerHTML;
            const printWindow = window.open('', '', 'height=1200,width=800');

            printWindow.document.write('<html lang="fr"><head>');
            // Ajoute les styles CSS pour le format de papier
            printWindow.document.write('<style>@media print { /*@page { margin: 0; } body { margin: 1cm; }*/ .no-print { display: none !important; } } </style>');
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
});