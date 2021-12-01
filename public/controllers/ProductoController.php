<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $nombre = $parametros['nombre'];
    $precio = $parametros['precio'];
    $codigo = $parametros['codigo'];
    $stock = $parametros['stock'];
    $tipo = $parametros['tipo'];


    $usr = new Producto();
    $usr->nombre = $nombre;
    $usr->precio = $precio;
    $usr->stock = $stock;
    $usr->tipo = $tipo;
    $usr->codigo_producto = $codigo;
    $usr->crearProducto();
    

    $payload = json_encode(array("mensaje" => "Producto creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];
    $usuario = Producto::obtenerProductoID($usr);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Producto::obtenerTodos();
    $payload = json_encode(array("listaProductos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function ModificarUno($request, $response, $args)
  {
     // $method = $request->getMethod();
     $parametros = $request->getParsedBody();
     parse_str(file_get_contents('php://input'), $parametros);
     $usr = new Usuario();
 
     $usr->id = $parametros['id'];
     $usr->usuario = $parametros['usuario'];
     $usr->clave = $parametros['clave'];
    
     $usr->modificarUsuario();
     $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
 
     $response->getBody()->write($payload);
     return $response
       ->withHeader('Content-Type', 'application/json');
  }

  public function BorrarUno($request, $response, $args)
  {
    $usuarioId = $args['id'];

    var_dump($usuarioId);
    Usuario::borrarUsuario($usuarioId);

    $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function descargaCSV($request, $response, $args)
  {
      $conexion = new mysqli('localhost', 'root', '', 'comanda');
      $query = 'SELECT * FROM pedidos';

      // $headersQuery = array('id','usuario','clave','codigo','email','estado');
      $a =  $conexion->query($query);
      // $arrUsuarios = [];
      $i = 0;

      $fp = fopen('php://output', 'w+');

      // if ($i == 0) {
      //     fputcsv($fp , $headersQuery , '');
      //     $i = 1;
      // }

      while ($row = mysqli_fetch_array($a)) {
          fputcsv($fp , array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]));
      }

      fclose($fp);

     return $response->withHeader('Content-Disposition',' attachment; filename="producto.csv"')->withAddedHeader('Content-Type', 'application/csv');

  }



}