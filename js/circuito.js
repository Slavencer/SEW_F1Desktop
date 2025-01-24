class Circuito{
    constructor(){
        $("input[name='uploadXml']").on("change",this.processXml.bind(this,$("input[name='uploadXml']")));
        $("input[name='uploadKml']").on("change",this.processKml.bind(this,$("input[name='uploadKml']")));
        $("input[name='uploadSvg']").on("change",this.processSvg.bind(this,$("input[name='uploadSvg']")));
    }
    processXml(input){
        $("svg").remove();
        $("article").remove();
        $("div").remove();
        var file=input.prop("files")[0];
        if(file===undefined)
            return;
        if(file.type.match('.\.xml'))
            this.printXml(file);
        else{
            $("main").append("<p>Fichero inválido, debe ser de tipo xml.</p>");
        }
    }
    processKml(input){
        $("svg").remove();
        $("article").remove();
        $("div").remove();
        var file=input.prop("files")[0];
        if(file===undefined)
            return;
        if(file.type.match('.\.kml'))
            this.showKml(file);
        else{
            $("main").append("<p>Fichero inválido, debe ser de tipo kml.</p>");
        }
    }
    processSvg(input){
        $("svg").remove();
        $("article").remove();
        $("div").remove();
        var file=input.prop("files")[0];
        if(file===undefined)
            return;
        if(file.type.match('.\.svg')){
            var lector = new FileReader();
            lector.onload = function (evento) {
                $("main").append($(lector.result).attr("preserveAspectRatio","xMinYMin slice").attr("viewBox","0 0 1000 1000"));
            }
            lector.readAsText(file);
        }else{
            $("main").append("<p>Fichero inválido, debe ser de tipo svg.</p>");
        }
    }
    printXml(file){
        var lector = new FileReader();
        lector.onload = function (evento) {
            var xmlText =$.parseXML(lector.result);
            var name=$("<h2></h2>").append($("nombre",xmlText));
            var location="<p>"+$("localidad",xmlText).text()+", "+$("país",xmlText).text()+"</p>";
            var coordsC = $("coordenadas",xmlText)[0];
            var coordenadasCircuito = "<ul>Coordenadas de salida:<li>lat:"+$("latitud",coordsC).text()+"</li><li>long:"+$("longitud",coordsC).text()+"</li><li>alt:"+$("altitud",coordsC).text()+$("altitud",coordsC).attr("unidades")+"</li></ul>";
            var datosCircuito = $("<ul></ul>").append("Datos del circuito:");
            datosCircuito.append("<li>Longitud: "+$("longitud_circuito",xmlText).text()+" "+$("longitud_circuito",xmlText).attr("unidades")+"</li>");
            datosCircuito.append("<li>Anchura media: "+$("anchura_media",xmlText).text()+" "+$("anchura_media",xmlText).attr("unidades")+"</li>");
            datosCircuito.append("<li>Vueltas: "+$("vueltas",xmlText).text()+" vueltas </li>");
            var datosEvento = $("<ul></ul>").append("Datos del evento:");
            datosEvento.append("<li>Fecha: "+$("fecha",xmlText).text()+"</li>");
            datosEvento.append("<li>Hora: "+$("hora",xmlText).text()+"</li>");
            var imágenes=$("<p></p>");
            $("fotografías foto",xmlText).each(function(index,domObject) {
                imágenes.append("<img src='"+$(domObject).text()+"' alt='Imagen subida por el usuario'/>");
            });
            var vídeos=$("<p></p>");
            $("vídeos vídeo",xmlText).each(function(index,domObject) {
                var extension = $(domObject).text().split('.');
                vídeos.append('<video controls><source src="'+$(domObject).text()+'" type="video/'+extension[extension.length-1]+'"/></video>');
            });
            var bibliografía=$("<ul>Bibliografía:</ul>");
            $("bibliografía referencia",xmlText).each(function(index,domObject) {
                var ref = $(domObject).text();
                bibliografía.append("<li><a href='"+ref+"'>"+ref+"</a></li>");
            });
            var tramos = $("<section></section>").append("<h3>Tramos del circuito:</h3>");
            $("tramos tramo",xmlText).each(function(index,domObject){
                
                var sector= "<h4>Tramo "+(index+1)+" - Sector: "+$("sector",domObject).text()+"</h4>";
                var distancia = "<p>Distancia del tramo: "+$("distancia",domObject).text()+" "+$("distancia",domObject).attr("unidades")+"</p>";
                var coords = $("coordenadas",domObject);
                var coordenadas = "<p>Coordenadas del tramo:</p><p>lat:"+$("latitud",coords).text()+"</p><p>long:"+$("longitud",coords).text()+"</p><p>alt:"+$("altitud",coords).text()+$("altitud",coords).attr("unidades")+"</p>";
                tramos.append($("<div></div>").append(sector,distancia,coordenadas));
            });
            var article = $("<article></article>").append(name,location,coordenadasCircuito,datosCircuito,datosEvento,imágenes,vídeos,bibliografía,tramos);
            $("main").remove("article");
            $("main").append(article);
        }      
        lector.readAsText(file);
    }
    showKml(file){
        $("body").append("<script async src='https://maps.googleapis.com/maps/api/js?key=exampleKeyId&loading=async&libraries=core,maps,marker&callback=circuito.initMap'></script>");
        this.kmlFile=file;
    }
    initMap(){
        $("main").append("<div id='map'></div>");
        var lector = new FileReader();
        lector.onload = function (evento) {
            var kmlText =$.parseXML(lector.result);
            var coordinates = $("Placemark coordinates",kmlText).first().text().split(',');
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat:Number(coordinates[1]), lng:Number(coordinates[0])},
                zoom: 14,
                mapTypeId: 'satellite',
                mapId: "kmlMap"
            });
            var marker = new google.maps.marker.AdvancedMarkerElement({
                map,
                position:{lat:Number(coordinates[1]), lng:Number(coordinates[0]), altitude:Number(coordinates[2])}
            });
            var circuitoLines=[];
            $("Placemark coordinates",kmlText).eq(1).text().trim().split('\n').forEach(line => {
                var coords = line.split(',');
                circuitoLines.push({lat:Number(coords[1]),lng:Number(coords[0]),altitude:Number(coords[2])});
            });
            var circuito = new google.maps.Polyline({
                path: circuitoLines,
                geodesic: true,
                strokeColor: "#FF0000",
                strokeOpacity: 1.0,
                strokeWeight: 2,
            });
            circuito.setMap(map);
        }
        lector.readAsText(this.kmlFile);
    }
}
