<div wire:ignore>
    <div id="map" style="height: 400px;"></div>

    <script>
        let map;
        let marker;

        function initMap() {
            const defaultLat = parseFloat(document.querySelector('input[id="data.lat"]')?.value) || -4.2634;
            const defaultLng = parseFloat(document.querySelector('input[id="data.lng"]')?.value) || 15.2429;



            const defaultPosition = { lat: defaultLat, lng: defaultLng };

            map = new google.maps.Map(document.getElementById("map"), {
                center: defaultPosition,
                zoom: 13,
                mapTypeId: 'hybrid'
            });

            marker = new google.maps.Marker({
                position: defaultPosition,
                map: map,
            });

            map.addListener("click", (e) => {
                const lat = e.latLng.lat().toFixed(6);
                const lng = e.latLng.lng().toFixed(6);

                marker.setMap(null);
                marker = new google.maps.Marker({
                    position: e.latLng,
                    map: map,
                });

                const latInput = document.querySelector('input[id="data.lat"]');
                const lngInput = document.querySelector('input[id="data.lng"]');

                if (latInput && lngInput) {
                    latInput.value = lat;
                    lngInput.value = lng;

            
                    latInput.dispatchEvent(new Event('input', { bubbles: true }));
                    lngInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARv0mcL-p4Sb40Nhu0Ntx0A6eTja2B33I&callback=initMap"
        async defer></script>
</div>