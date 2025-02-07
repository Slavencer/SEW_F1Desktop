<?php
class Carrusel{
    protected $capital;
    protected $país;
    protected $photos;
    public function __construct(String $capital, String $país){
        $this->capital=$capital;
        $this->país=$país;
    }
    public function getTenPhotos(){
        $params = array(
            urlencode("method").'='.urlencode("flickr.photos.search"),
            urlencode("api_key").'='.urlencode("exampleKeyId"),
            urlencode("text").'='.urlencode($this->país.",".$this->capital),
            urlencode("tags").'='.urlencode($this->país.",".$this->capital),
            urlencode("per_page").'='.urlencode("10"),
            urlencode("page").'='.urlencode("1"),
            urlencode("format").'='. urlencode("json"),
            urlencode("nojsoncallback").'='.urlencode("1"),
        );
        $url = "https://www.flickr.com/services/rest/?".implode('&', $params);
        $response = file_get_contents($url);
        $json = json_decode($response);
        echo "<section><h3>Imagenes de ".$this->capital.", ".$this->país.": </h3>";
        for($i=0;$i<10;$i++) {
            $titulo = $json->photos->photo[$i]->title;
            $URLfoto = "https://live.staticflickr.com/".($json->photos->photo[$i]->server)."/".($json->photos->photo[$i]->id)."_".($json->photos->photo[$i]->secret)."_m.jpg";
            print "<img alt='".$titulo."' src='".$URLfoto."' />";
        }
        echo "<button>&gt;</button><button>&lt;</button></section>";
    }
}
class Moneda{
    protected $monedaLocal;
    protected $monedaDestino;

    public function __construct(String $monedaLocal, String $monedaDestino){
        $this->monedaLocal=$monedaLocal;
        $this->monedaDestino=$monedaDestino;
    }
    public function getExchangeInfo(){
        $url = "https://openexchangerates.org/api/latest.json?app_id=exampleKeyId&symbols=".$this->monedaLocal.",".$this->monedaDestino;
        $headers = [
            "Accept: application/json",
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);
        $this->showExchangeRate($response);
    }
    private function showExchangeRate(String $response){
        $json = json_decode($response,true);
        $rates = $json["rates"];
        $to=$rates[$this->monedaDestino];
        $from=$rates[$this->monedaLocal];
        echo "<section><h3>Cambio de moneda:</h3><p>1 ".$this->monedaLocal." = ".round($to/$from,6)." ".$this->monedaDestino."</p></section>";
    }
}
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Javier Carrasco"/>
    <meta name="description" content="documento con el modelado de los viajes de f1"/>
    <meta name="keywords" content ="F1,coches,fórmula uno" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>F1 Desktop-Viajes</title>
    <link rel="icon" type="image/x-icon" href="multimedia/imagenes/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="estilo/layout.css"/>
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
            <a class="active" href="viajes.php">Viajes</a>
            <a href="juegos.html">Juegos</a>
        </nav>
    </header>
    <p><a href="index.html">Inicio</a> > Viajes</p>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <main>
        <h2>Viajes</h2>
        <?php
            $currency = new Moneda("EUR","CNY");
            $currency -> getExchangeInfo();
        ?>
        <button>Cargar mapas</button>
        <div id='map'></div>
        <?php
            $carrusel = new Carrusel("Beijing","China");
            $carrusel -> getTenPhotos();
        ?>
        <script src="js/viajes.js"></script>        
    </main>
    <footer>
        <p>Javier Carrasco Arango, Universidad de Oviedo</p>
    </footer>
</body>
</html>
