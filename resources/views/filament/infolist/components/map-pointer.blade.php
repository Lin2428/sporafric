<div wire:ignore>
    <div id="map" style="height: 400px; width: 100%; border-radius: 10px;"></div>
    <script>
        const lat = parseFloat(@json($getRecord()->lat ?? $getRecord()->contractGenerator->contract->lat ?? $getRecord()->devisGenerator->devis->lat ?? null));
        const lng = parseFloat(@json($getRecord()->lng ?? $getRecord()->contractGenerator->contract->lng ?? $getRecord()->devisGenerator->devis->lng ?? null));
        
        function initMap() {
            
            const position = { lat: lat, lng: lng };

            const map = new google.maps.Map(document.getElementById("map"), {
                center: position,
                zoom: 13,
                mapTypeId: 'hybrid',
            });

            new google.maps.Marker({
                position,
                map,
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            if (typeof google !== 'undefined') {
                initMap();
            }
        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARv0mcL-p4Sb40Nhu0Ntx0A6eTja2B33I&callback=initMap" async defer></script>
</div>