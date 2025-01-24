<?php
class Carrera{
    protected $server;
    protected $user;
    protected $pass;
    protected $db;
    protected $maxRaces;
    protected $puntos;
    protected $nextRace;

    public function __construct(){
        $this->server = "localhost";
        $this->user = "DBUSER2024";
        $this->pass = "DBPSWD2024";
        $this->db = "Fantasy";
        $this->puntos = [0=>25,1=>18,2=>15,3=>12,4=>10,5=>8,6=>6,7=>4,8=>2,9=>1];
        $this->maxRaces=5;
    }
    public function showMenus(){
        if(count($_POST)>0){
            switch($_POST["action"]){
                case "Crear carreras":
                    $this->createRaces();
                    $this->showNextRaceMenu(0);
                    break;
                case "Siguiente carrera":
                    $this->nextRace=$_POST["nextRace"]+1;
                    $this->runRace();
                    $this->showRace();
                    if($this->nextRace==$this->maxRaces-1){
                        $this->showResultsOfChampionshipMenu();
                    }else{
                        $this->showNextRaceMenu($this->nextRace);
                    }
                    break;
                case "Ver resultados":
                    $this->showChampionshipSummary();
                    break;
                default:
                    $this->showCreateRaces();
                    break;
            }
        }else{
            $this->showCreateRaces();
        }
    }
    public function createRaces(){
        $lugares = ["Nueva York, EEUU","Teruel, España","Birmingham, Inglaterra","Moscú, Russia","Roma, Italia","Mónaco","Berlín, Alemania"];
        $connection = new mysqli($this->server,$this->user,$this->pass,$this->db);
        $connection->query("DELETE FROM Carrera");
        $pst = $connection->prepare("INSERT INTO Carrera (código,lugar,meteorología) VALUES (?,?,?)");
        $código=null;
        $lugar=null;
        $meteorlogía=null;
        $pst->bind_param("isi",$código,$lugar,$meteorlogía);
        for($i=0;$i<$this->maxRaces;$i++){
            $código=$i;
            $lugar=$lugares[rand(0,count($lugares)-1)];
            $meteorlogía=rand(0,9);
            $pst->execute();
        }
        $connection->close();
    }
    public function showRace(){
        $connection = new mysqli($this->server,$this->user,$this->pass,$this->db);
        $carreraActual = $connection->query("SELECT lugar,meteorología FROM Carrera WHERE código='".$this->nextRace."'")->fetch_all(MYSQLI_BOTH)[0];
        $lugar=$carreraActual["lugar"];
        if($carreraActual["meteorología"]<3){
            $tiempo="Despejado";
        }elseif($carreraActual["meteorología"]<5){
            $tiempo="Lluvia leve";
        }elseif($carreraActual["meteorología"]<7){
            $tiempo="Lluvia";
        }elseif($carreraActual["meteorología"]<=9){
            $tiempo="Tormenta";
        }
        $drivers = array_keys($this->puntajes);
        print "<article><h3>Carrera $this->nextRace</h3><p>Lugar: $lugar</p><p>Tiempo: $tiempo</p><section><h4>Resultados de la carrera $this->nextRace</h4><ol>";
        for($i=0;$i<count($this->puntajes);$i++){
            $driver = $drivers[$i];
            $points = $this->puntos[$i];
            print "<li>$driver - $points puntos</li>";
        }
        echo "</ol></section></article>";
        $connection->close();
    }
    public function runRace(){
        $connection = new mysqli($this->server,$this->user,$this->pass,$this->db);
        $pilotos = $connection->query("SELECT nombrePiloto,lluvia,habilidad,velocidad FROM Piloto p INNER JOIN Corre_para c ON p.nombrePiloto=c.piloto")->fetch_all(MYSQLI_BOTH);
        $wheather = $connection->query("SELECT meteorología FROM Carrera WHERE código='".$this->nextRace."'")->fetch_all(MYSQLI_BOTH)[0]["meteorología"];
        $pst = $connection->prepare("SELECT puntos FROM Puntúa_en WHERE piloto=? and carrera=".$this->nextRace-1);
        $driver=null;
        $pst->bind_param("s",$driver);
        $carreraActual = array();
        foreach($pilotos as $piloto){
            $driver=$piloto["nombrePiloto"];
            $pointsPiloto=$wheather*$piloto["lluvia"]+rand(5,9)*$piloto["habilidad"]+rand(5,9)*$piloto["velocidad"];
            if($this->nextRace>1){//TODO change for query of previous run
                $pst->execute();
                $pointsPiloto+=10*$pst->get_result()->fetch_all(MYSQLI_BOTH)[0]["puntos"];
            }
            $carreraActual[$piloto["nombrePiloto"]]=$pointsPiloto;
        }
        arsort($carreraActual);
        $this->puntajes=$carreraActual;
        $connection->close();
        if(!$this->insertResultsToDatabase()){
            echo "<h3>Ocurrió un error crítico en la base de datos</h3>";
        }
    }
    public function showNextRaceMenu(int $nextRace){
        print "<section><h3>Siguiente carrera</h3>
        <form action='#' method='post'>
        <label for='raceNum'>Siguiente carrera:</label>
        <input readonly type='text' id='raceNum' name='nextRace' value='$nextRace'/>
        <label for='runRace'>Pulsa aquí para iniciar la siguiente carrera:</label>
        <input type='submit' id='runRace' name='action' value='Siguiente carrera'/>
        </form></section>";
    }
    public function showResultsOfChampionshipMenu(){
        print "<section><h3>Ver resultados</h3>
        <form action='#' method='post'>
            <label for='runRace'>Pulsa aquí para ver los resultados del campeonato:</label>
            <input type='submit' id='runRace' name='action' value='Ver resultados'/>
        </form></section>";
    }
    public function showCreateRaces(){
        echo "<section><h3>Crear las carreras</h3>
        <form action='#' method='post'>
            <label for='crearCarreras'>Pulsa aquí para crear las carreras:</label>
            <input type='submit' id='crearCarreras' name='action' value='Crear carreras'/>
        </form>
        </section>";
    }
    public function insertResultsToDatabase(){
        $connection = new mysqli($this->server,$this->user,$this->pass,$this->db);
        $connection->autocommit(FALSE);
        $pst = $connection->prepare("INSERT INTO Puntúa_en (carrera,piloto,posición,puntos) VALUES (?,?,?,?)");
        $driver=null;
        $j=null;
        $points=null;
        $pst->bind_param("isis",$this->nextRace,$driver,$j,$points);
        $done=true;
        $drivers = array_keys($this->puntajes);
        for($j=0;$j<count($this->puntajes);$j++){
            $driver = $drivers[$j];
            $points = $this->puntos[$j];
            $done = $pst->execute();
            if(!$done){
                break;
            }
        }
        if($done){
            $connection->commit();
        }else{
            $connection->rollback();
        }
        $connection->close();
        return $done;
    }    
    public function showChampionshipSummary(){
        $connection = new mysqli($this->server,$this->user,$this->pass,$this->db);
        $results = $connection->query("SELECT c.equipo team,SUM(p.puntos) totalPoints FROM corre_para c INNER JOIN puntúa_en p ON c.piloto=p.piloto GROUP BY c.equipo ORDER BY totalPoints DESC");
        echo "<article><h3>Ranking final</h3><ol>";
        foreach($results->fetch_all(MYSQLI_ASSOC) as $tuple){
            $team = $tuple["team"];
            $points = $tuple["totalPoints"];
            print "<li>$team - $points</li>";
        }
        echo "</ol></article>";
        $connection->close();
    }
}
$carrera = new Carrera();
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
    <p><a href="../index.html">Inicio</a> > <a href="../juegos.html" >Juegos</a> > <a href="fantasyInicio.php">Fantasy</a> > Carrera </p>
    <section>
        <h2>Juegos disponibles:</h2>
        <a href="../memoria.html">Juego de memoria</a>
        <a href="../semaforo.php">Juego de reacción</a>
        <a href="../api.html">Juego de escritura</a>
        <a href="fantasyInicio.php">Juego de f1 fantasy</a>
    </section>
    <main>
        <section><h2>Carreras f1</h2>
        <?php
        $carrera->showMenus();
        ?>
        </section>
    </main>
    <footer>
        <p>Javier Carrasco Arango, Universidad de Oviedo</p>
    </footer>
</body>