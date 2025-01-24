<?php
class Fantasy{
    protected $server;
    protected $user;
    protected $pass;

    public function __construct(){
        $this->server = "localhost";
        $this->user = "DBUSER2024";
        $this->pass = "DBPSWD2024";
        if(count($_POST)>0){
            if($_POST["action"]==="Mostrar ayuda"){
                $this->mostrarAyuda();
            }elseif($_POST["action"]==="Iniciar simulación"){
                $this->crearBd();
                $this->printBeginButton();
            }
        }else{
            $this->printHelpButton();
            $this->printInitBdButton();
        }
    }

    public function mostrarAyuda(){
        echo "<article><h3>Cómo jugar</h3>
        <p>En este juego, se va a simular una competición de f1, en la que participarás con tu equipo contra otros 4, a lo largo de una temporada de 5 carreras.</p>
        <p>Puedes generar aleatoriamente a tu equipo las veces que quieras recargando la página de creación de equipo, y si te gusta mucho, puedes descargarlo una vez creado para usarlo siempre que quieras!</p>
        <p>Los pilotos tienen una serie de atributos que los hacen mejores, y el equipo tendrá un coche de calidad aleatoria. Una vez que elijas tu equipo,
        Se generarán los otros 5 contra los que competirás, y podrás crear las 5 carreras.</p>
        <p>Las carreras tienen una predicción meteorológica que modificará como jugarán los pilotos. Tras cada carrera se muestra el ranking, y al final, 
        la clasificación equipos por la suma de los puntos de sus pilotos.</p>
        <table><tr><th scope='col' id='Pos'>Posición</th><th scope='col' id='Points'>puntos</th></tr>
        <tr><td headers='Pos'>1st</td><td headers='Points'>25</td></tr>
        <tr><td headers='Pos'>2nd</td><td headers='Points'>18</td></tr>
        <tr><td headers='Pos'>3rd</td><td headers='Points'>15</td></tr>
        <tr><td headers='Pos'>4th</td><td headers='Points'>12</td></tr>
        <tr><td headers='Pos'>5th</td><td headers='Points'>10</td></tr>
        <tr><td headers='Pos'>6th</td><td headers='Points'>8</td></tr>
        <tr><td headers='Pos'>7th</td><td headers='Points'>6</td></tr>
        <tr><td headers='Pos'>8th</td><td headers='Points'>4</td></tr>
        <tr><td headers='Pos'>9th</td><td headers='Points'>2</td></tr>
        <tr><td headers='Pos'>10th</td><td headers='Points'>1</td></tr>
        </table>
        </article>";
        $this->printInitBdButton();
    }

    public function crearBd(){
        $connection = new mysqli($this->server,$this->user,$this->pass);
        $sentences = file_get_contents("fantasy.sql");
        if ($connection->multi_query($sentences) === TRUE) {
            echo "<h3>Base de datos creada correctamente</h3>";
        } else {
            echo "<h3>Error al crear la base de datos: vuelve a intentarlo</h3>";
        }
        $connection->close();
    }
    public function printHelpButton(){
        echo "<form action='#' method='post'>
            <input type='submit' name='action' value='Mostrar ayuda'/>
        </form>";
    }
    public function printInitBdButton(){
        echo"<form action='#' method='post'>
            <input type='submit' name='action' value='Iniciar simulación'/>
        </form>";
    }
    public function printBeginButton(){
        echo "<form action='fantasyEquipo.php' method='post'>
            <input type='submit' value='Continuar'/>
        </form>";
    }
}
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Javier Carrasco"/>
    <meta name="description" content="documento con un juego de simulación de temporadas de f1, donde se puede importar o exportar equipos generados aleatoriamente y participar con ellos."/>
    <meta name="keywords" content ="F1,coches,fórmula uno, f1 fantasy, fantasy" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>F1 Desktop-Juegos-Fantasy</title>
    <link rel="icon" type="image/x-icon" href="multimedia/imagenes/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css"/>
    <link rel="stylesheet" type="text/css" href="fantasy.css"/>
</head>
<body>
    <header>
        <h1><a href="../index.html">F1 Desktop</a></h1>
        <nav>
            <a href="../index.html">Inicio</a>
            <a href="../piloto.html">Piloto</a>
            <a href="../noticias.html">Noticias</a>
            <a href="../calendario.html">Calendario</a>
            <a href="../meteorología.html">Meteorología</a>
            <a href="../circuito.html">Circuito</a>
            <a href="../viajes.php">Viajes</a>
            <a class="active" href="../juegos.html">Juegos</a>
        </nav>
    </header>
    <p><a href="../index.html">Inicio</a> > <a href="../juegos.html" >Juegos</a> > Fantasy</p>
    <section>
        <h2>Juegos disponibles:</h2>
        <a href="../memoria.html">Juego de memoria</a>
        <a href="../semaforo.php">Juego de reacción</a>
        <a href="../api.html">Juego de escritura</a>
        <a href="fantasyInicio.php">Juego de f1 fantasy</a>
    </section>
    <main>
        <section>
        <h2>F1 fantasy</h2>
        <?php
            $fantasy = new Fantasy();
        ?>
        </section>
    </main>
    <footer>
        <p>Javier Carrasco Arango, Universidad de Oviedo</p>
    </footer>
</body>