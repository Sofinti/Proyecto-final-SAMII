<?php
// variables de conexion
$servidor = "localhost";
$usr      = "root";
$pwd      = "";

// almacenamos nombre de la base de datos
$bd       = "Cooperativa";

// creamos la variable de conexion
$con = new mysqli($servidor, $usr, $pwd, $bd);

// Comprobamos la conexión 
if ($con->connect_error) {
  die("La conexión no se pudo establecer: " . $con->connect_error);
}
echo "Conexión con la B. de Datos establecida <br/>";

?>