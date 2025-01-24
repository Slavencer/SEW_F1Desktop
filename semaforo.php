<?php 
class Record{
    protected $server;
    protected $user;
    protected $pass;
    protected $dbname;

    public function __construct(){
        $this->server = "localhost";
        $this->user = "DBUSER2024";
        $this->pass = "DBPSWD2024";
        $this->dbname = "records";
    }

    public function sendRecord(){
        if(count($_POST)>0){
            $database = new mysqli($this->server,$this->user,$this->pass,$this->dbname);
            $insert = sprintf("INSERT INTO registro (nombre,apellidos,nivel,tiempo) VALUES ('%s','%s','%f','%f')",$_POST["nombre"],$_POST["apellidos"],$_POST["nivel"],$_POST["tiempo"]);
            if($database->query($insert)===FALSE){
                echo "An error occurred when sending the record to the database.";
            }
            $database->close();
        }
    }
    public function showTopTen(){
        if(count($_POST)>0){
            $database = new mysqli($this->server,$this->user,$this->pass,$this->dbname);
            $select = sprintf("SELECT * FROM registro WHERE nivel=%f ORDER BY tiempo ASC LIMIT 10",$_POST["nivel"]);
            $result = $database->query($select);
            if ($result->num_rows > 0) {
                echo "<ol>Mejores resultados para la dificultad ".$_POST["nivel"].":";
                $rows = $result->fetch_all(MYSQLI_BOTH);
                foreach ($rows as $row){
                    echo sprintf("<li>%s %s - %g</li>",$row["nombre"],$row["apellidos"],$row["tiempo"]);
                }
                echo "</ol>";
            }
            $database->close();
        }
    }
}
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Javier Carrasco"/>
    <meta name="description" content="documento con un juego de tiempo de reacción"/>
    <meta name="keywords" content ="F1,coches,fórmula uno,juego f1, juego reacción" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>F1 Desktop-Juegos-Semáforo</title>
    <link rel="icon" type="image/x-icon" href="multimedia/imagenes/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="estilo/layout.css"/>
    <link rel="stylesheet" type="text/css" href="estilo/semaforo_grid.css"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="js/semaforo.js"></script>
</head>
<body>
    <header>
        <h1><a href="index.html">F1 Desktop</a></h1>
        <nav>
            <a href="index.html">Inicio</a>
            <a href="piloto.html">Piloto</a>
            <a href="noticias.html">Noticias</a>
            <a href="calendario.html">Calendario</a>
            <a href="meteorologia.html">Meteorología</a>
            <a href="circuito.html">Circuito</a>
            <a href="viajes.php">Viajes</a>
            <a class="active" href="juegos.html">Juegos</a>
        </nav>
    </header>
    <p><a href="index.html">Inicio</a> > <a href="juegos.html" >Juegos</a> > Semáforo</p>
    <section>
        <h2>Juegos disponibles:</h2>
        <a href="memoria.html">Juego de memoria</a>
        <a href="semaforo.php">Juego de reacción</a>
        <a href="api.html">Juego de escritura</a>
        <a href="php/fantasyInicio.php">Juego de f1 fantasy</a>
    </section>
    <main>
        <script>
            let semáforo=new Semáforo();
        </script>
        <?php
        $record = new Record();
        $record -> sendRecord();
        $record -> showTopTen();
        ?>
    </main>
    <footer>
        <p>Javier Carrasco Arango, Universidad de Oviedo</p>
    </footer>
</body>