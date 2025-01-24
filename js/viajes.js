class Viajes{
    constructor(){
        this.setupCarrusel();
        $("main > button").on("click",this.cargarMapas.bind(this));
    }
    cargarMapas(){
        $("main > button").attr("disabled","");
        $("main").append("<script defer src='https://maps.googleapis.com/maps/api/js?key=exampleKeyId&loading=async&libraries=maps,marker&callback=viajes.initMap'></script>");
    }
    getMapaEstaticoGoogle(pos){
        var ubicacion=$("main");
        var url = "https://maps.googleapis.com/maps/api/staticmap?";
        var centro = "center=" + pos.lat + "," + pos.lng;
        var zoom ="&zoom=15";
        let width = Math.round(ubicacion.width());
        var tamaño= "&size="+width+"x"+width;
        var marcador = "&markers=color:red%7Clabel:S%7C" + pos.lat + "," + pos.lng;
        var sensor = "&sensor=false"; 
        var apiKey="&key=exampleKeyId";
        
        this.imagenMapa = url + centro + zoom + tamaño + marcador + sensor + apiKey;
        ubicacion.append("<h3>Posición actual:</h3>")
        ubicacion.append("<img src='"+this.imagenMapa+"' alt='mapa estático google' />");
    }
    initMap(){  
        var infoWindow = new google.maps.InfoWindow({});
        var pos = {
            lat: 0,
            lng: 0
        }
        var mapaDinámico = new google.maps.Map(document.getElementsByTagName('div')[0],{
            zoom: 8,
            center:pos,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoomControl: true,
            scaleControl:true,
            fullscreenControl:true,
            mapId:"DYNAMIC_MAP"
        });
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                viajes.getMapaEstaticoGoogle(pos);
                new google.maps.marker.AdvancedMarkerElement({
                    map:mapaDinámico,
                    position: pos,
                });
                infoWindow.setPosition(pos);
                infoWindow.setContent('Localización encontrada');
                infoWindow.open(mapaDinámico);
                mapaDinámico.setCenter(pos);
            }, function() {
                viajes.handleLocationError(true, infoWindow,  mapaDinámico);
            });
        } else {
            // Browser doesn't support Geolocation
            viajes.handleLocationError(false, infoWindow, mapaDinámico);
        }
    }
    handleLocationError(browserHasGeolocation, infoWindow, mapaDinámico) {
        infoWindow.setPosition(mapaDinámico.getCenter());
        infoWindow.setContent(browserHasGeolocation ?
                                'Error: Ha fallado la geolocalización' :
                                'Error: Su navegador no soporta geolocalización');
        infoWindow.open(mapaDinámico);
    }
    setupCarrusel(){
        this.images = document.querySelectorAll("img");
        let next = document.querySelector("section button:nth-of-type(1)");
        let previous = document.querySelector("section button:nth-of-type(2)");
        this.slide = 0;
        this.maxImage = this.images.length-1;
        next.addEventListener("click",function () {
            if (this.slide === this.maxImage) {
                this.slide = 0;
            } else {
                this.slide++;
            }
            this.updateCarrusel();
        }.bind(this));
        previous.addEventListener("click", function () {
            if (this.slide === 0) {
                this.slide = this.maxImage;
            } else {
                this.slide--;
            }
            this.updateCarrusel();
        }.bind(this));
    }
    updateCarrusel(){
        this.images.forEach((image, indx) => {
            var trans = 100 * (indx - this.slide);
            $(image).css('transform', 'translateX(' + trans + '%)')
        });
    }
}
var viajes = new Viajes();
