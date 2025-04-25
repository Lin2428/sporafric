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

<div class="container-1">
    <div class="w-full">
        {{-- Date de prise d'appel --}}
        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-phone')</i>
                Date de prise d'appel:
            </span>
            <span>{{ \App\Utils\DateUtils::format($getRecord()->date_prise_appel) }}</span>
        </div>

        {{-- Date planifiée --}}
        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-calendar')</i>
                Date planifiée:
            </span>
            <span>{{ \App\Utils\DateUtils::format($getRecord()->date_planifiee) }}</span>
        </div>

        {{-- Type d'intervention --}}
        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-wrench')</i>
                Type d’intervention:
            </span>
            <span>{{\App\Enum\InterventionType::from($getRecord()->type)->label() }}</span>
        </div>

        {{-- Numéro de bon de livraison --}}
        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-document-text')</i>
                Numéro de bon de livraison:
            </span>
            <span>{{ $getRecord()->identifiant }}</span>
        </div>

        {{-- Description panne / travaux --}}
        <div class="">
            <span class="label">
                <i class="icon">@svg('heroicon-s-clipboard-document')</i>
                Description :
            </span>
            <span>{{ $getRecord()->description_panne }}</span>
        </div>
    </div>

    <x-filament::button color="warning">
        Modifier
    </x-filament::button>

</div>
<br>
<hr>
<br>

<div class="container-1">
    <div class="data-block">
        <span class="label">
            <i class="icon">@svg('heroicon-s-user-circle')</i>
            Techniciens affectés :
        </span>
        <table class="techniciens-table">
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($getRecord()->interventionTechniciens as $technicien)
                <tr>
                    <td><img src="{{ asset('storage/' . $technicien->photo) }}" alt="Photo" class="techniciens-photo">
                    </td>
                    <td>{{ $technicien->name }}</td>
                    <td>{{ $technicien->phone }}</td>
                    <td>{{ $technicien->email }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div>

    </div>
    <x-filament::button color="warning">
        Modifier
    </x-filament::button>
</div>
<br>
<hr>
<br>
<div class="container-1">
    <div class="w-full">

        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-clock')</i>
                Délai d'intervention:
            </span>
            <span>{{ \App\Utils\DateUtils::format($getRecord()->start_date) }} - {{
                \App\Utils\DateUtils::format($getRecord()->end_date) }}</span>
        </div>


        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-cog')</i>
                Heure de fonctionnement du GE:
            </span>
            <span>{{$getRecord()->contract->generator->houres}}h</span>
        </div>


        <div class="data">
            <span class="label">
                <i class="icon">@svg('heroicon-s-cog-6-tooth')</i>
                Matériel livré/installé
            </span>
            <table class="materiel-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Désignation</th>
                        <th>Image</th>
                        <th>Usure</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>GEN-001</td>
                        <td>Filtre à air moteur diesel</td>
                        <td><img class="piece-img"
                                src="https://tpdemain.com/wp-content/uploads/2023/02/2ef0c05e-b2d7-4a9f-b4e4-d22ed634a236.jpeg"
                                alt="Filtre à air"></td>
                        <td>5000h</td>
                    </tr>
                    <tr>
                        <td>GEN-002</td>
                        <td>Alternateur pour générateur 7kVA</td>
                        <td><img class="piece-img"
                                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjNVKGPmfV3xdPNO87dkZNUA67nr9YB_Lkug&s"
                                alt="Alternateur"></td>
                        <td>8000h</td>
                    </tr>
                    <tr>
                        <td>GEN-003</td>
                        <td>Démarreur électrique 12V</td>
                        <td><img class="piece-img"
                                src="https://www.valeo.com/wp-content/uploads/2023/06/Starter-_-ReStarter.png"
                                alt="Démarreur"></td>
                        <td>4300h</td>
                    </tr>
                    <tr>
                        <td>GEN-004</td>
                        <td>Pompe à carburant pour moteur Perkins</td>
                        <td><img class="piece-img"
                                src="https://tracteur-market.fr/media/images/pompe-alimenttion-moteur-perkins-3-152.400x400.jpg"
                                alt="Pompe à carburant"></td>
                        <td>5000h</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="container-2">
        <x-filament::button color="warning">
            Modifier
        </x-filament::button>
        <br><br>
        <div id="pdf-viewer"
            style="width: 300px; height: 400px;cursor: pointer; overflow: hidden; background: white; border: 1px solid #ccc; border-radius: 8px;">
        </div>
    </div>

</div>
<br>
<hr>
<br>
<div class="container-1">
    <div class="w-full">

        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-credit-card')</i>
                Mode de facturation:
            </span>
            <span>Hors conrat</span>
        </div>


        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-clock')</i>
                Horaire:
            </span>
            <span>Journée normale (2h-30min)</span>
        </div>

        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-clipboard-document-list')</i>
                Devis:
            </span>
            <span>N°12378 du 17/04/2024</span>
        </div>

        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-bookmark')</i>
                Bon de commande:
            </span>
            <span>N°12378 du 17/04/2024</span>
        </div>

        <div class="data-block">
            <span class="label">
                <i class="icon">@svg('heroicon-s-banknotes')</i>
                Montant facturé:
            </span>
            <span><b>120 000 Fcfa</b></span>
        </div>

    </div>

    <div class="container-2">
        <x-filament::button color="warning">
            Modifier
        </x-filament::button>
        <br><br>
        <div class="pdf-flex">
            <div id="pdf-viewer-2"
                style="width: 250px; height: 350px; overflow: hidden; background: white; border: 1px solid #ccc; border-radius: 8px;  cursor: pointer;">
            </div>
            <div id="pdf-viewer-3"
                style="width: 250px; height: 350px; overflow: hidden; background: white; border: 1px solid #ccc; border-radius: 8px;cursor: pointer;">
            </div>
        </div>
    </div>

</div>

<!-- PDF.js Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.14.305/pdf.min.js"></script>

<script>
    function renderPdfPage({
    url,             // URL du PDF (ex: 'storage/mon-fichier.pdf')
    targetElementId, // ID de l'élément où afficher le canvas
    pageNumber = 1,  // Numéro de la page à afficher (par défaut 1)
    scale = 0.5      // Échelle du rendu (0.5 = réduit, 1 = taille normale, etc.)
}) {
    const loadingTask = pdfjsLib.getDocument(url);

    loadingTask.promise.then(pdf => {
        if (pageNumber > pdf.numPages) {
            console.error(`Page ${pageNumber} dépasse le nombre de pages (${pdf.numPages}).`);
            return;
        }

        pdf.getPage(pageNumber).then(page => {
            const viewport = page.getViewport({ scale });

            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Vide l'élément si besoin
            const target = document.getElementById(targetElementId);
            target.innerHTML = "";
            target.appendChild(canvas);

            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            page.render(renderContext);
        });
    }).catch(error => {
        console.error("Erreur de chargement du PDF :", error);
    });
}

    renderPdfPage({
    url: "{{ asset('storage/interventions/test.pdf') }}",
    targetElementId: "pdf-viewer",
    pageNumber: 1,
    scale: 0.5
    });

    renderPdfPage({
    url: "{{ asset('storage/devis/test.pdf') }}",
    targetElementId: "pdf-viewer-2",
    pageNumber: 1,
    scale: 0.43
    });

    renderPdfPage({
    url: "{{ asset('storage/bon_de_commande/test.pdf') }}",
    targetElementId: "pdf-viewer-3",
    pageNumber: 1,
    scale: 0.43
    });

    function openPdfModal({
    pdfUrl,
    modalId = 'pdf-modal',
    viewerId = 'pdf-full',
    scale = 1.5,
}) {
    const modal = document.getElementById(modalId);
    const viewer = document.getElementById(viewerId);

    if (!modal || !viewer) {
        console.error("Modal ou viewer non trouvé.");
        return;
    }
let pdfLink = document.querySelector('#pdf-link');
pdfLink.href = pdfUrl;
    // Affiche le modal
    modal.style.display = "flex";

    // Vide le contenu précédent
    viewer.innerHTML = "";


    // Charge le PDF avec pdf.js
    const loadingTask = pdfjsLib.getDocument(pdfUrl);
    loadingTask.promise.then(pdf => {
        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
            pdf.getPage(pageNum).then(page => {
                const viewport = page.getViewport({ scale });

                const canvas = document.createElement("canvas");
                const context = canvas.getContext("2d");
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                page.render({ canvasContext: context, viewport });
                viewer.appendChild(canvas);
            });
        }
    }).catch(error => {
        console.error("Erreur lors du chargement du PDF :", error);
    });
}

// Fonction de fermeture
function closePdfModal(modalId = 'pdf-modal') {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = "none";
    }
}

document.getElementById("pdf-viewer").onclick = () => {
    openPdfModal({
        pdfUrl: "{{ asset('storage/interventions/test.pdf') }}",
        modalId: "pdf-modal",
        viewerId: "pdf-full",
        scale: 1.5
    });
};

document.getElementById("pdf-viewer-2").onclick = () => {
    openPdfModal({
        pdfUrl: "{{ asset('storage/devis/test.pdf') }}",
        modalId: "pdf-modal",
        viewerId: "pdf-full",
        scale: 1.5
    });
};

document.getElementById("pdf-viewer-3").onclick = () => {
    openPdfModal({
        pdfUrl: "{{ asset('storage/bon_de_commande/test.pdf') }}",
        modalId: "pdf-modal",
        viewerId: "pdf-full",
        scale: 1.5
    });
};


</script>


<!-- Modale PDF -->
<div id="pdf-modal" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    justify-content: center;
    align-items: center;
">
    <div style="position: relative; background: white; padding: 10px; border-radius: 8px;">
        <button onclick="closePdfModal()" style="
            position: absolute;
            top: 5px; right: 10px;
            background: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-weight: bold;
            border-radius: 4px;
        ">X</button>

        <a id="pdf-link" href="" target="_blank">
            <div id="pdf-full" style="width: auto; height: 90vh; overflow: auto;"></div>
    </div>
    </a>
</div>
