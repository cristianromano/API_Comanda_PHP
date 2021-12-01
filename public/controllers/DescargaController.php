<?php

require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './models/Usuario.php';

class Descarga
{

    public function descargaCSV($request, $response, $args)
    {
        $conexion = new mysqli('localhost', 'root', '', 'comanda');
        $query = 'SELECT * FROM usuarios';

        $headersQuery = array('id','usuario','clave','codigo','email','estado');
        $a =  $conexion->query($query);
        $arrUsuarios = [];
        $i = 0;

        $fp = fopen('php://output', 'w+');

        if ($i == 0) {
            fputcsv($fp , $headersQuery , '');
            $i = 1;
        }

        while ($row = mysqli_fetch_array($a)) {
            fputcsv($fp , array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]) , '');
        }

        fclose($fp);

       return $response->withHeader('Content-Disposition',' attachment; filename="downloaded.csv"')->withAddedHeader('Content-Type', 'application/csv');

    }
}
