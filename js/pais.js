'use strict'
class País{
    constructor(nombre,capital,circuito,población,formaGobierno){
        this.nombre=nombre;
        this.capital=capital;
        this.nombreCircuito=circuito;
        this.población=población;
        this.formaGobierno=formaGobierno;
        this.url = "https://api.openweathermap.org/data/2.5/forecast?";
        this.apiid="exampleApiId";
    }
    ponerMetaCircuito(latMeta,longMeta){
        this.latMeta=latMeta;
        this.longMeta=longMeta;
    }
    religiónMayoritaria(religión){
        this.religión=religión;
    }
    getNombrePaís(){
        return this.nombre;
    }
    getCapital(){
        return this.capital;
    }
    getListaDetallesPaís(){
        return "<ul><li>"+this.nombreCircuito+"</li><li>Población: "+this.población+"</li><li>"+this.formaGobierno+"</li><li>"+this.religión+"</li></ul>";
    }
    escribeCoordenadas(){
        document.write("<p>Coordenadas: "+this.latMeta+","+this.longMeta+"</p>");
    }
    cargarDatos(){
        $.ajax(this.url,{
            data:{
            lat:""+this.latMeta,
            lon:""+this.longMeta,
            appid:this.apiid,
            units:"metric",
            mode: "xml"},
            lang:"es",
            dataType:"xml",
            method: "GET",
            error: function (){
                $("main").append($("<p></p>").text("Could not fetch wheather data."))
            }
        }).done(function(data){
            let days= [];
            $("time",data).each(function() {
                let article ={};
                article.day = (new Date($(this).attr("from"))).getDay();
                article.image=$("<img>");
                article.image.attr("src","https://openweathermap.org/img/wn/"+$("symbol",this).attr("var")+"@4x.png").attr("alt","imagen del tiempo");
                article.minTemp= $("<p></p>").append("Temperatura mínima: "+$("temperature",this).attr("min")+"º");
                article.maxTemp= $("<p></p>").append("Temperatura máxima: "+$("temperature",this).attr("max")+"º");
                article.humedad = Number($("humidity",this).attr("value"));
                let amountLluvia = Number($("precipitation",this).attr("value"));
                article.cantidadLluvia = isNaN(amountLluvia)?0:amountLluvia;
                if(days.length==0)
                    days.push(article);
                if(days[days.length-1].day!=article.day)
                    days.push(article);
                else{
                    if(article.minTemp<days[days.length-1].minTemp)
                        days[days.length-1].minTemp=article.minTemp;
                    if(article.maxTemp>days[days.length-1].maxTemp)
                        days[days.length-1].maxTemp=article.maxTemp;
                    days[days.length-1].humedad=Math.round((days[days.length-1].humedad+article.humedad)/2);
                    days[days.length-1].cantidadLluvia+=article.cantidadLluvia;
                }
            });
            days.forEach(function(day){
                let weekDay=["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
                let cantidadLluvia=$("<p></p>").append("precipitación: "+day.cantidadLluvia);
                let humedad = $("<p></p>").append("Humedad: "+day.humedad+"%");
                $("main").append($("<article></article>")
                .append($("<h3></h3>").append(weekDay[day.day]),day.image,day.minTemp,day.maxTemp,humedad,cantidadLluvia));
            })
        });
    }
}

var pais = new País("China","Beijing","Shanghai International Circuit","1409670000","Unitary Marxist–Leninist one-party socialist republic");
pais.ponerMetaCircuito(31.33722222222222,121.2202777777778);
pais.religiónMayoritaria("Budismo")
document.write("<h2>"+pais.getNombrePaís()+"</h2>")
document.write("<h3>"+pais.getCapital()+"</h3>")
pais.escribeCoordenadas();
document.write(pais.getListaDetallesPaís());
pais.cargarDatos();
