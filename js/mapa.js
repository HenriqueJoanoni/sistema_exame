/*
google.maps.event.addDomListener(window, 'load', init);

function init() {
    var directions = new google.maps.DirectionsService();
    directions.route({
        origin: (-15.5931236,-56.1144773),
        destination: "Várzea Grande MT Brazil",
        travelMode: google.maps.TravelMode.DRIVING
    }, rotaDisponivel);
}

function rotaDisponivel(dados, status) {
    var url = "http://maps.googleapis.com/maps/api/staticmap?center=Cuiabá%20MT%20Brasil&zoom=12&size=800x800&maptype=roadmap&sensor=false&path=color:0x0000ff|weight:5|enc:"
    if(dados.routes[0]) {
        var div = document.getElementById('mapa');
        var img = document.createElement('img');
        img.src = url + dados.routes[0].overview_polyline;
        div.appendChild(img);
    } else {
        // erro ao obter rota
    }
}*/