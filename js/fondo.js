class Fondo{
    constructor(nombrePaís, capital, nombreCircuito){
        this.nombrePaís=nombrePaís;
        this.capital=capital;
        this.nombreCircuito=nombreCircuito;
        this.image = this.getImage();
    }
    getImage(){
        let searchAPI = "https://www.flickr.com/services/rest/?";
        $.getJSON(searchAPI, 
                {
                    method: "flickr.photos.search",
                    api_key:"exampleKeyId",
                    text:this.nombreCircuito,
                    tags:"F1",
                    per_page:1,
                    page:1,
                    format: "json",
                    nojsoncallback:1
                }).done(function(data) {
                    var image_back = "https://live.staticflickr.com/"+data.photos.photo[0].server+"/"+data.photos.photo[0].id+"_"+data.photos.photo[0].secret+"_b.jpg";
                    $("body").css("background-image", "url('"+image_back+"')").css("background-repeat","no-repeat").css("height","100%").css("background-position","center").css("background-size","cover");
                    return;
        });
    }
}
