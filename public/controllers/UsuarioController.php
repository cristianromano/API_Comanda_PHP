<?php
require_once './models/Usuario.php';
require_once './models/Log.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutentificadorJWT.php';

class UsuarioController extends Usuario implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $clave = $parametros['clave'];
    $codigo = $parametros['codigo'];
    $email = $parametros['email'];

    // Creamos el usuario
    $usr = new Usuario();
    $usr->usuario = $usuario;
    if ($codigo == 'Cliente') {
      $usr->clave = 'sin clave';
    } else {
      $usr->clave = $clave;
    }
    $usr->codigo = $codigo;
    $usr->email = $email;
    $usr->crearUsuario();

    $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['usuario'];
    $usuario = Usuario::obtenerUsuario($usr);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Usuario::obtenerTodos();
    $payload = json_encode(array("listaUsuario" => $lista));

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

  public function Loguear($request, $response, $args)
  {

    $parametros = $request->getParsedBody();

    $email = $parametros['email'];
    $clave = $parametros['clave'];
    $usr = new Usuario();
    $usr->email = $email;
    $usr->clave = $clave;

    $usr->codigo = Usuario::obtenerUsuarioCodigo($email);

    if (Usuario::Login($usr)) {
      $payload = json_encode(array("mensaje" => "Usuario Logueado con exito"));
      $datos = ["codigo" => $usr->codigo, "clave" => $usr->clave, "email" => $usr->email];

      $log = new Log();
      $log->email = $datos["email"];
      $log->estado = "Login";
      $log->crearLog();

      echo AutentificadorJWT::CrearToken($datos);
    } 
    else {
      $payload = json_encode(array("mensaje" => "Usuario no existe"));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  
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
          fputcsv($fp , array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]));
      }

      fclose($fp);

     return $response->withHeader('Content-Disposition',' attachment; filename="usuarios.csv"')->withAddedHeader('Content-Type', 'application/csv');

  }





}
