class Semáforo{
    levels=[0.2,0.5,0.8];
    constructor(){
        this.changeDifficulty();
        this.lights=4;
        this.unload_moment=null;
        this.click_moment=null;
        this.createStructure();
    }
    changeDifficulty(){
        this.difficulty= this.levels[Math.floor(Math.random()*3)];
    }
    createStructure(){
        let main = document.querySelector("main");
        let h2 = document.createElement("h2");
        h2.appendChild(document.createTextNode("Juego del semáforo"));
        main.appendChild(h2);
        for(let i=0;i<this.lights;i++){
            let div = document.createElement("div");
            main.appendChild(div);
        }
        let button1 = document.createElement("button");
        button1.setAttribute("type","button");
        button1.onclick=this.initSequence.bind(this);
        button1.appendChild(document.createTextNode("Arranque"));
        let button2 = document.createElement("button");
        button2.setAttribute("type","button");
        button2.appendChild(document.createTextNode("Reacción"));
        button2.setAttribute("disabled","disabled");
        button2.onclick= this.stopReaction.bind(this);
        main.appendChild(button1);
        main.appendChild(button2);
    }
    initSequence(){
        this.changeDifficulty();
        let main = document.querySelector("main");
        main.classList.add("load");
        let button = document.querySelector("button");
        button.setAttribute("disabled","disabled");
        setTimeout(()=>{
            this.unload_moment=new Date();
            this.endSequence();
        },(this.difficulty*1000)+2000);
    }
    endSequence(){
        let button2 = document.querySelector("button:nth-of-type(2)");
        button2.removeAttribute("disabled");
        let main = document.querySelector("main");
        main.classList.add("unload");
    }
    stopReaction(){
        this.click_moment = new Date();
        this.reaction_time = ((this.click_moment - this.unload_moment)/1000);
        let main = document.querySelector("main");
        if(this.played===true){
            main.removeChild(document.querySelector("main > p"));
            main.removeChild(document.querySelector("main form"));
            main.removeChild(document.querySelector("ol"));
        }else{
            this.played=true;
        }
        let paragraph = document.createElement("p");
        this.createRecordForm();
        paragraph.appendChild(document.createTextNode("Your reaction time was: "+this.reaction_time+" s"));
        main.appendChild(paragraph);
        main.classList.remove("load");
        main.classList.remove("unload");
        let begin = document.querySelector("button");
        let reaction = document.querySelector("button:nth-of-type(2)");
        begin.removeAttribute("disabled");
        reaction.setAttribute("disabled","disabled");
    }
    createRecordForm(){
        let form = $("<form action='#' method='post' name='recordSaving'><form>");
        form.append("<label for='formName'>Nombre:</label><input id='formName' type='text' name='nombre'/>");
        form.append("<label for='formApellidos'>Apellidos:</label><input id='formApellidos' type='text' name='apellidos'/>");
        form.append("<label for='formDiff'>Nivel completado:</label><input id='formDiff' type='text' value='"+this.difficulty+"' readonly name='nivel'/>");
        form.append("<label for='formTime'>Tiempo de reacción:</label><input id='formTime' type='text' value='"+this.reaction_time+"' readonly name='tiempo'/>");
        form.append("<input type='submit' value='enviar'/>");
        $("main").append(form);
    }
}