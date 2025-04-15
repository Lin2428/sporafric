<div>
    <div id="map" style="height: 400px; width: 100%; border-radius: 10px;"></div>

    <script>
        function initMap() {
            const lat = parseFloat(@json($lat ?? -4.2634));
            const lng = parseFloat(@json($lng ?? 15.2429));
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