<?php
class EquiposFantasy{
    protected $server;
    protected $user;
    protected $pass;
    protected $db;

    public function __construct(){
        $this->server = "localhost";
        $this->user = "DBUSER2024";
        $this->pass = "DBPSWD2024";
        $this->db = "Fantasy";
        if(count($_POST)>0 and $_POST["action"]=="Exportar"){
            $this->exportToCsv($_POST["teamName"]);
        }
    }
    public function printMenus(){
        if(count($_POST)>0){
            switch($_POST["action"]){
                case "Importar equipo":
                    $mimes = array("application/vnd.ms-excel","text/csv");
                    if(isset($_FILES["fileName"]) and !empty($_FILES["fileName"]["tmp_name"]) and in_array($this->getMimeType($_FILES["fileName"]["tmp_name"]),$mimes)){
                        if($this->importTeam($_FILES["fileName"]["tmp_name"])){
                            echo "<h3>Se ha importado los equipos correctamente.</h3>";
                            $this->showContinue();
                        }else{
                            echo "<h3>Hubo errores importando el archivo</h3>";
                            $this->showBack("Crear equipo");
                        }
                    }else{
                        echo "<h3>Fichero inválido, debe ser de tipo csv.<h3>";
                        $this->showBack("Crear equipo");
                    }
                    break;
                case "Crear nuevo equipo":
                    $this->createNewTeamMenu();
                    $this->showCreateNewTeam();
                    $this->showImport();
                    break;
                case "Crear equipo":
                    if($this->createTeam($_POST["teamName"],$_POST["driver1"],$_POST["driver2"])){
                        $this->showExportOption($_POST["teamName"]);
                        $this->showContinue();
                    }else{
                        $this->showBack("Crear nuevo equipo");
                    }
                    break;
                case "Exportar":
                    $this->showContinue();
                    break;
                default:
                    break;
            }
        }else{
            $this->showCreateNewTeam();
            $this->showImport();
        }
    }
    public function showBack(String $back){
        echo "<form action='#' method='post'>
            <input type='submit' value='$back'/>
        </form>";
    }
    public function showContinue(){
        echo "<form action='fantasyCarrera.php' method='post'>
            <input type='submit' value='Continuar'/>
        </form>";
    }
    public function showCreateNewTeam(){
        echo "<section><h3>Crear un equipo</h3>
        <form action='#' method='post'>
            <label for='crearEquipo'>Pulsa aquí para crear un nuevo equipo:</label>
            <input type='submit' id='crearEquipo' name='action' value='Crear nuevo equipo'/>
        </form>
        </section>";
    }
    public function showImport(){
        echo "<section><h3>Si deseas, puedes jugar con equipos ya hechos:</h3>
        <form action='#' method='post' enctype='multipart/form-data'>
            <label for='importarEquipos'>Selecciona los equipos a importar:</label>
            <input type='file' accept='text/csv' id='importarEquipos' name='fileName'/>
            <input type='submit' name='action' value='Importar equipo'/>
        </form>
        </section>";
    }
    private function getMimeType(string $filename)
    {
        $info = finfo_open(FILEINFO_MIME_TYPE);
        if (!$info) {
            return false;
        }
        $mimeType = finfo_file($info, $filename);
        finfo_close($info);
    
        return $mimeType;
    }
    public function createNewTeamMenu(){
        $connection = new mysqli($this->server,$this->user,$this->pass,$this->db);
        $piloto1 = $connection->query("SELECT nombrePiloto FROM Piloto ORDER BY RAND() LIMIT 1")->fetch_assoc()["nombrePiloto"];
        $piloto2 = $connection->query("SELECT nombrePiloto FROM Piloto WHERE nombrePiloto not LIKE '".$piloto1."' ORDER BY RAND() LIMIT 1")->fetch_assoc()["nombrePiloto"];
        $connection->close();
        print "<section><h3>Nuevo equipo:</h3>
        <form action='#' method='post'>
        <label for='nombreEquipo'>Nombre de equipo:</label>
        <input type='text' id='nombreEquipo' name='teamName'/>
        <label for='piloto1'>Piloto 1:</label>
        <input readonly type='text' id='piloto1' name='driver1' value='$piloto1'/>
        <label for='piloto2'>Piloto 2:</label>
        <input readonly type='text' id='piloto2' name='driver2' value='$piloto2'/>
        <input type='submit' name='action' value='Crear equipo'/>
        </form>
        </section>";
    }
    public function createTeam(String $teamName, String $driver1, String $driver2){
        if(empty($teamName)){
            echo "<h3>Nombre de equipo inválido</h3>";
            return false;
        }
        $done = true;
        $connection = new mysqli($this->server,$this->user,$this->pass,$this->db);
        $connection->autocommit(FALSE);
        $pst = $connection->prepare("SELECT COUNT(*) AS count FROM Equipo WHERE nombreEquipo LIKE ?");
        $pst->bind_param("s",$teamName);
        if($exists = $pst->execute()){
            $exists=$pst->get_result()->fetch_assoc()["count"];
            if($exists==0){
                $pst = $connection->prepare("INSERT INTO Equipo (nombreEquipo,nivelCoche) VALUES (?,?)");
                $nivCoche=rand(5,9);
                $pst->bind_param("si",$teamName,$nivCoche);
                $pst->execute();
                $pst = $connection->prepare("INSERT INTO Corre_para (equipo,piloto) VALUES (?,?),(?,?)");
                $pst->bind_param("ssss",$teamName,$driver1,$teamName,$driver2);
                $pst->execute();
                $this->populateOtherTeams($connection,$teamName);
            }else{
                echo "<h3>Porfavor elige otro nombre de equipo.</h3>";
                $done=false;
            }
        }else{
            echo "<h3>Ocurrió un error en la base de datos.</h3>";
            $done=false;
        }
        if($done){
            $connection->commit();
        }else{
            $connection->rollback();
        }
        $connection->close();
        return $done;
    }
    public function populateOtherTeams(Mysqli $connection,String $teamName){
        $pst = $connection->prepare("SELECT nombreEquipo FROM Equipo WHERE nombreEquipo NOT LIKE ?");
        $pst->bind_param("s",$teamName);
        $pst->execute();
        $results = $pst->get_result();
        $pst = $connection->prepare("INSERT INTO Corre_para (equipo,piloto) VALUES (?,?),(?,?)");
        $driver1= null;//placeholders, will be populated in the loop.
        $driver2=null;
        $team=null;
        $pst->bind_param("ssss",$team,$driver1,$team,$driver2);
        while($res = $results->fetch_assoc()){
            $team=$res["nombreEquipo"];
            $drivers = $connection ->query("SELECT nombrePiloto FROM Piloto WHERE nombrePiloto NOT IN (SELECT piloto FROM Corre_para) ORDER BY RAND() LIMIT 2");
            $driver1 = $drivers->fetch_array()["nombrePiloto"];
            $driver2 = $drivers->fetch_array()["nombrePiloto"];
            $pst->execute();
        }

    }
    public function showExportOption(String $teamName){
        print "<section><h3>Si deseas, puedes guardar la configuración de los equipos para usarlos cuando quieras:</h3>
        <form action='#' method='post' enctype='multipart/form-data'>
            <label for='nombreEquipo'>Nombre de equipo:</label>
            <input readonly type='text' id='nombreEquipo' value='$teamName' name='teamName'/>
            <input type='submit' name='action' value='Exportar'/>
        </form>
        </section>";
    }
    private function exportToCsv($teamName,$filename = "teamSettingsF1Fantasy.csv",$delimiter=",") {
        $connection = new mysqli($this->server,$this->user,$this->pass,$this->db);
        $connection->autocommit(FALSE);
        $pst = $connection->prepare("SELECT nombreEquipo,nivelCoche FROM Equipo WHERE nombreEquipo LIKE ?");
        $pst->bind_param("s",$teamName);
        if($pst->execute()){
            $result = $pst->get_result()->fetch_all(MYSQLI_ASSOC);
            $tmpfile = tmpfile();
            fwrite($tmpfile,$result[0]["nombreEquipo"].$delimiter.$result[0]["nivelCoche"]."\n");
            $result=$connection->query("SELECT equipo,piloto FROM Corre_para");
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                fwrite($tmpfile,$row["equipo"].$delimiter.$row["piloto"]."\n");
            }
            fseek($tmpfile, 0);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="'.$filename.'";');
            fpassthru($tmpfile);
            fclose($tmpfile);
        }else{
            echo "<h3>Error al exportar la configuración.<h3>";
        }
        $connection->close();
        exit();
    }
    public function importTeam(String $fileName){
        $done = true;
        $connection = new mysqli($this->server,$this->user,$this->pass,$this->db);
        $connection->autocommit(FALSE);
        $file = fopen($fileName, "r") or die("Error al leer el fichero.");
        $lines=array();
        $lineCount=0;
        while(!feof($file)){
            $line = trim(fgets($file));
            if(!empty($line)){
                $lines[$lineCount++] = explode(",",$line);
            }
        }
        fclose($file);
        if($lineCount==11){
            $pst = $connection->prepare("INSERT INTO Equipo (nombreEquipo,nivelCoche) VALUES (?,?)");
            $pst->bind_param("si",$lines[0][0],$lines[0][1]);
            if($done =$pst->execute()){
                $pst = $connection->prepare("INSERT INTO Corre_para (equipo,piloto) VALUES (?,?)");
                $equipo=null;
                $piloto=null;
                $pst->bind_param("ss",$equipo,$piloto);
                for($i=1;$i<$lineCount;$i++){
                    $equipo=$lines[$i][0];
                    $piloto=$lines[$i][1];
                    $done =$pst->execute();
                    if(!$done){
                        break;
                    }
                }
            }
        }else{
            $done=false;
        }
        if($done){
            $connection->commit();
        }else{
            $connection->rollback();
        }
        $connection->close();
        return $done;
    }
}
$equipos = new EquiposFantasy();
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
    <p><a href="../index.html">Inicio</a> > <a href="../juegos.html" >Juegos</a> > <a href="fantasyInicio.php">Fantasy</a> > Equipo</p>
    <section>
        <h2>Juegos disponibles:</h2>
        <a href="../memoria.html">Juego de memoria</a>
        <a href="../semaforo.php">Juego de reacción</a>
        <a href="../api.html">Juego de escritura</a>
        <a href="fantasyInicio.php">Juego de f1 fantasy</a>
    </section>
    <main>
        <section><h2>Creación de equipos</h2>
        <?php
            $equipos->printMenus();
        ?>
        </section>
    </main>
    <footer>
        <p>Javier Carrasco Arango, Universidad de Oviedo</p>
    </footer>
</body>