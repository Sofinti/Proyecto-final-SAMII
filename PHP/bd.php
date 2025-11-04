<?php
 // Inicializamos las variables de conexión
$servidor    = 'localhost';
$usuario     = 'root';
$contrasenia = '';
$nombre_base = 'Cooperativa';

    // Creamos una conexión
$con = mysqli_connect($servidor,$usuario,$contrasenia,$nombre_base); 

   // Comprobamos la conexión 
if ($con->connect_error) {
    die("La conexión no se pudo establecer: " . $con->connect_error);
  }
  //   Sentencia para probar si la conexion es exitosa
  //   echo "Conexión con la B. de Datos establecida <br/>";

?>