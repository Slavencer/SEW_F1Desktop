'use strict';
class Memoria{
    elements= [
        {
            element:"RedBull",
            source:"https://upload.wikimedia.org/wikipedia/de/c/c4/Red_Bull_Racing_logo.svg"
        },
        {
            element:"RedBull",
            source:"https://upload.wikimedia.org/wikipedia/de/c/c4/Red_Bull_Racing_logo.svg"
        },
        {
            element:"McLaren",
            source:"https://upload.wikimedia.org/wikipedia/en/6/66/McLaren_Racing_logo.svg"
        },
        {
            element:"McLaren",
            source:"https://upload.wikimedia.org/wikipedia/en/6/66/McLaren_Racing_logo.svg"
        },
        {
            element:"Alpine",
            source:"https://upload.wikimedia.org/wikipedia/fr/b/b7/Alpine_F1_Team_2021_Logo.svg"
        },
        {
            element:"Alpine",
            source:"https://upload.wikimedia.org/wikipedia/fr/b/b7/Alpine_F1_Team_2021_Logo.svg"
        },
        {
            element:"AstonMartin",
            source:"https://upload.wikimedia.org/wikipedia/fr/7/72/Aston_Martin_Aramco_Cognizant_F1.svg"
        },
        {
            element:"AstonMartin",
            source:"https://upload.wikimedia.org/wikipedia/fr/7/72/Aston_Martin_Aramco_Cognizant_F1.svg"
        },
        {
            element:"Ferrari",
            source:"https://upload.wikimedia.org/wikipedia/de/c/c0/Scuderia_Ferrari_Logo.svg"
        },
        {
            element:"Ferrari",
            source:"https://upload.wikimedia.org/wikipedia/de/c/c0/Scuderia_Ferrari_Logo.svg"
        },
        {
            element:"Mercedes",
            source:"https://upload.wikimedia.org/wikipedia/commons/f/fb/Mercedes_AMG_Petronas_F1_Logo.svg"
        },
        {
            element:"Mercedes",
            source:"https://upload.wikimedia.org/wikipedia/commons/f/fb/Mercedes_AMG_Petronas_F1_Logo.svg"
        }
    ]

    constructor(){
        this.hasFlippedCard=false;
        this.lockBoard=false;
        this.firstCard=null;
        this.secondCard=null;
        this.shuffleElements();
        this.createElements();
        this.addHelp();
        this.addEventListeners();
    }
    addHelp(){
        let h2 = document.querySelector("main section h2");
        let helpButton = document.createElement("button");
        helpButton.textContent="Cómo jugar?";
        helpButton.onclick=this.showHelp.bind(this);
        h2.after(helpButton);
    }
    showHelp(){
        let dialog = document.querySelector("dialog");
        if(dialog===null){
            dialog=document.createElement("dialog");
            let help = `<h3>Reglas del juego de memoria</h3>
            <p>En la pantalla aparecen 12 cartas que por un lado tienen "Tarjeta de memoria" y por otro lado una imagen.
            Esas 12 cartas forman 6 parejas, puesto que las imagenes están repetidas, cada imagen aparece dos veces.</p>
            <p>Solo puedes tener dos cartas boca arriba al mismo tiempo, sin contar las parejas ya hechas, asi que necesitarás recordar dónde están las parejas, 
            y si te equivocas, se volverán a poner boca abajo.</p>
            <p>El objetivo del juego, es mostrar todas las parejas, pulsando cada carta y su pareja para voltearlas.</p>`;
            dialog.insertAdjacentHTML("beforeend",help);
            let dialogButton=document.createElement("button");
            dialogButton.textContent="Cerrar";
            dialog.appendChild(dialogButton);
            document.body.appendChild(dialog);
            document.querySelector("dialog button").onclick=() => document.querySelector("dialog").close();
        }
        dialog.showModal();
    }

    addEventListeners(){
        let nodes = document.querySelectorAll("article");
        let func = function (article){article.onclick= this.flipCard.bind(article,this);};
        nodes.forEach(func.bind(this));
    }

    flipCard(game){
        if(this['data-state']=="revealed") return;
        if(game.lockBoard==true) return;
        if(this===game.firstCard) return;
        this.setAttribute("data-state","flip");

        if(!game.hasFlippedCard){
            game.hasFlippedCard=true;
            game.firstCard=this;
        }else{
            game.lockBoard=true;
            game.secondCard=this;
            setTimeout(game.checkForMatch.bind(game),1000);
        }
    }

    shuffleElements(){
        for (var i = this.elements.length - 1; i >= 0; i--) {
            var j = Math.floor(Math.random() * (i + 1));
            var temp = this.elements[i];
            this.elements[i] = this.elements[j];
            this.elements[j] = temp;
        }
    }
    unflipCards(){
        this.lockBoard=true;
        this.firstCard.removeAttribute('data-state');
        this.secondCard.removeAttribute('data-state');
        this.resetBoard();
    }
    resetBoard(){
        this.firstCard=null;
        this.secondCard=null;
        this.hasFlippedCard=false;
        this.lockBoard=false;
    }
    checkForMatch(){
        this.firstCard['data-element']===this.secondCard['data-element']?this.disableCards():this.unflipCards();
    }
    disableCards(){
        this.firstCard['data-state']='revealed';
        this.secondCard['data-state']='revealed';
        this.resetBoard();
    }
    createElements(){
        let section = document.createElement("section");
        let header = document.createElement("h2");
        header.appendChild(document.createTextNode("Juego de Memoria"));
        section.appendChild(header);
        for(var i=0;i<this.elements.length;i++){
            let article= document.createElement("article");
            article["data-element"]=this.elements[i].element;
            let title= document.createElement("h3");
            title.appendChild(document.createTextNode("Tarjeta de memoria"))
            let image= document.createElement("img");
            image.src=this.elements[i].source;
            image.alt=this.elements[i].element;
            article.appendChild(title);
            article.appendChild(image);
            section.appendChild(article);
        }
        document.querySelector("main").appendChild(section);
    }
}