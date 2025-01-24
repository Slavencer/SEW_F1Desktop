class TypeRacer{
    constructor(){
        this.textName="default";
        this.toTypeText="El Campeonato Mundial de Fórmula 1 de la FIA, más conocido como Fórmula 1, F1 o Fórmula Uno, es la principal competición de automovilismo internacional y el campeonato de deportes de motor más popular y prestigioso del mundo. La entidad que la dirige es la Federación Internacional del Automóvil (FIA). Desde septiembre de 2016, tras la adquisición de Formula One Group, la empresa estadounidense Liberty Media es la responsable de gestionar y operar el campeonato.";
        $("body").append("<button name='begin'>Comenzar</button>");
        $("body").append("<label for='uploadText'>Usar texto:</label><input type='file' id='uploadText' name='uploadText' accept='.txt'/>")
        $("body").append("<main><p>Para completar el juego, escribe todo el texto sin equivocarte para hacer avanzar al fórmula, y que así pase la línea de salida al final de la recta!</p></main>");
        $("input[name='uploadText']").on("change",this.setText.bind(this,$("input[name='uploadText']")));
        $("button[name='begin']").on("click",this.setupGame.bind(this));
    }
    setText(input){
        var file=input.prop("files")[0];
        if(file===undefined)
            return;
        if(file.type.match('text/*')){
            var lector = new FileReader();
            let typeRacer=this;
            this.textName=file.name;
            lector.onload = function (evento) {
                typeRacer.toTypeText=lector.result.replaceAll('\t',"").replaceAll('\n',"");
            }
            lector.readAsText(file);
        }else{
            $("dialog").remove();
            $("main").append("<dialog><p>Fichero inválido, se usará el texto por defecto</p><button autofocus>Cerrar</button></dialog>");
            $("dialog button").on("click",() => {document.querySelector("dialog").close();});
            document.querySelector("dialog").showModal();
        }
    }
    setupGame(){
        $("button").attr('disabled','disabled');
        $("input").attr('disabled','disabled');
        $("main").empty();
        $("main").append("<canvas width='"+document.querySelector("main").clientWidth+"' ></canvas>")
        $("main").append("<p oncopy='return false'>"+this.toTypeText+"</p>");
        $("main").append("<textarea autofocus disabled name='typed' rows='10'></textarea>");
        $("textarea[name='typed']").on("input propertychange",this.updateAreas.bind(this));
        this.setupCanvas();
    }
    begin(){
        this.beginTime=new Date();
        $("textarea[name='typed']").removeAttr("disabled");
    }
    setupCanvas(){
        this.img = new Image();
        let canvas = document.querySelector("canvas");
        let ctx = canvas.getContext("2d");
        this.img.addEventListener("load", () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(this.img, 0, 0,canvas.height,canvas.height);
            this.begin();
        });
        this.img.src = "multimedia/imagenes/f1Car.png";
    }
    updateCanvas(){
        let canvas = document.querySelector("canvas");
        let ctx = canvas.getContext("2d");
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        let x = (this.correctLetters/this.toTypeText.length)*(canvas.width);
        ctx.drawImage(this.img, x, 0,canvas.height,canvas.height);
    }
    updateAreas(){
        let writtenText = $("textarea[name='typed']").val();
        let toWriteText = $("main p").text();
        let formattedText="";
        let previousLetterSame=undefined;
        let sameLetter;
        this.correctLetters=0;
        for(var index = 1; index <= writtenText.length; index++){
            if(index>toWriteText.length)
                break;
            if(sameLetter=(writtenText.at(index-1)===toWriteText.at(index-1))){/*The same letter*/
                this.correctLetters++;
                if(!previousLetterSame){
                    formattedText+=(previousLetterSame===undefined)?"<ins>":"</del><ins>";
                }
                formattedText+=this.toTypeText.at(index-1);
            }else{/*Different letter*/
                if(previousLetterSame)
                    formattedText+="</ins><del>";
                if(previousLetterSame===undefined)
                    formattedText+="<del>";
                formattedText+=this.toTypeText.at(index-1);
            }
            previousLetterSame=sameLetter;
        }
        if(previousLetterSame !== undefined)
            formattedText+=previousLetterSame?"</ins>":"</del>";
        formattedText+=this.toTypeText.slice(writtenText.length);
        $("main p").empty();
        $("main p").append(formattedText);
        this.updateCanvas();
        if(writtenText.length>=toWriteText.length)
            this.checkEnding();
    }
    checkEnding(){
        if(this.correctLetters!==this.toTypeText.length)
            return;
        this.endTime=new Date();
        let time = ((this.endTime-this.beginTime)/1000).toFixed(3);
        let lastTime= localStorage.getItem(this.textName);
        localStorage.setItem(this.textName,time);
        $("main").empty();
        $("main").append("<p>Enhorabuena!</p><p>Tu tiempo fue: "+time+" segundos</p>");
        let wpm = (this.toTypeText.split(" ").length/(time/60)).toFixed(2);
        $("main").append("<p>Palabras por minuto: "+wpm+"</p>");
        if(lastTime!=null){
            lastTime=Number(lastTime)
            let comparacion=lastTime>time?"mejorado "+(lastTime-time).toFixed(2):"empeorado "+(time-lastTime).toFixed(2);
            $("main").append("<p>Has "+comparacion+" segundos respecto al último intento.</p>");
        }
        $("button").removeAttr('disabled');
        $("input").removeAttr('disabled');
    }
}