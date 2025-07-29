<?php
include("conexion.php");

$sql = "SELECT id, matricula, marca, modelo,kilometraje,precio FROM auto";
$result = $con->query($sql);

if ($result->num_rows > 0) {
  // output data of each fila
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["id"]. 
        " matricula: " . $row["matricula"].
        " marca" .$row["marca"]. 
        " modelo" .$row["modelo"].
        " kilometraje" .$row["kilometraje"].
        " precio" .$row["precio"].
        "<br>";
  }
} else {
  echo "0 results";
}
$con->close();
?>