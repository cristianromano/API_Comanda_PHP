<?php
require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './models/Usuario.php';
require_once './models/fpdf.php';
require_once './interfaces/IApiUsable.php';

class PedidoController extends Pedido implements IApiUsable
{
  public function CargarUno($request, $response, $args)
  {
    $parametros = $request->getParsedBody();
    parse_str(file_get_contents('php://input'), $parametros);

    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    $data = AutentificadorJWT::ObtenerData($token);



    $parametros = $request->getParsedBody();
    $nombre_producto = $parametros['producto'];
    $cantidad = $parametros['cantidad'];
    $mail_usuario = $parametros['usuario'];
    $tiempo = $parametros['tiempo'];

    $mesas = Mesa::obtenerTodos();
    $productos = Producto::obtenerTodos();
    // $usuarios = Usuario::obtenerTodos();

    // $usuarioAux = new Usuario();

    $arrProd = explode('/', $nombre_producto);
    $arrCant = explode('/', $cantidad);
    $arrTiempo = explode('/', $tiempo);

    // $final = Producto::calcularMonto($arrCant, $arrProd);
    $mesaAux = new Mesa();
    $mesaAux->estado = 'ocupada';

    foreach ($mesas as $mesa) {
      if ($mesa->estado == 'abierta') {
        $mesaAux->id_mesa = $mesa->id_mesa;
        $mesaAux->estado = 'abierta';


        $mesa->estado = 'ocupada';
        $mesa->modificarMesa();

        break;
      }
    }

    $id_usuario =  Usuario::obtenerUsuarioMail($mail_usuario);

    $pedido = new Pedido();
    $pedido->numero_pedido = Pedido::AlfanumericoRandom(5);

    if ($mesaAux->estado == 'abierta' && $mesaAux->id_mesa >= 1) {

      for ($i = 0; $i < count($arrProd); $i++) {
        # code...
        foreach ($productos as $producto) {
          if ($producto->nombre == $arrProd[$i]) {

            $producto = Producto::obtenerProductoID($arrProd[$i]);
            if (($producto->stock -= $arrCant[$i]) > 0) {

              $timezone = date_default_timezone_set('America/Argentina/San_Luis');
              // $dateArg = date('Y-m-d H:i:s');
              $producto->modificarProductoStock();

              $pedido->id_producto = $producto->id_producto;
              $pedido->id_mesa = $mesaAux->id_mesa;
              $pedido->nombre_producto = $arrProd[$i];
              $pedido->cantidad = $arrCant[$i];
              $pedido->tiempo = $arrTiempo[$i];
              $pedido->mozo = $data->email;
              $pedido->fechamodificacion = date('Y-m-d H:' . '0' . $arrTiempo[$i] . ':s');
              var_dump($pedido->fechamodificacion);
              $pedido->id_usuario = $id_usuario;
              $pedido->estado = 'abierto';
              $pedido->precio_final = $producto->precio * $arrCant[$i];

              
              // $pedido->crearPedido();

              // if ($data->codigo != 'Socio') {
              //   $mozo = Usuario::obtenerUsuarioEmail($data->email);
              //   $mozo->estado = 'ocupado';
              //   $mozo->modificarUsuarioEstado();
              // }

              // $log = new Log();
              // $log->email = $data->email;
              // $log->estado = "El mozo: " . $data->email . ' ,creo el pedido: ' . $pedido->numero_pedido;
              // $log->crearLog();


              $payload = json_encode(array("mensaje" => "Pedido creado con exito , tu numero de pedido es " . $pedido->numero_pedido));
            } else {
              $payload = json_encode(array("mensaje" => "No stock hay de ese producto"));
            }
          }
        }
      }
    } else {
      $payload = json_encode(array("mensaje" => "No hay mesas disponibles"));
    }


    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }



  public function TraerUno($request, $response, $args)
  {
    // Buscamos usuario por nombre
    $usr = $args['pedido'];
    $usuario = Pedido::obtenerPedido($usr);
    $payload = json_encode($usuario);

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Producto::obtenerTodos();
    $payload = json_encode(array("listaXXProductos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function verTiempo($request, $response, $args)
  {
    $codigo = $args['codigo'];
    $lista = Pedido::obtenerTiempo($codigo);
    $payload = json_encode(array("Tiempo" => $lista));

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

  public function obtenerCuenta($request, $response, $args)
  {

    $numero_pedido = $args['codigo'];
    $precioFinal = 0;
    $acum = 0;
    $pedido = Pedido::obtenerPedidoCuenta($numero_pedido);

    foreach ($pedido as $producto) {
      $acum = ($producto->cantidad * $producto->precio);
      $precioFinal += $acum;
    }

    $payload = json_encode(array("CUENTA:" => $precioFinal));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public function modificarEstadoPedido($request, $response, $args)
  {

    $parametros = $request->getParsedBody();
    parse_str(file_get_contents('php://input'), $parametros);

    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    $data = AutentificadorJWT::ObtenerData($token);


    $numero_pedido = $parametros['id_pedido'];
    $estado_producto = $parametros['estado'];
    $tiempo = $parametros['tiempo'];

    $v2pedido = new Pedido();
    $v2pedido->estado = $estado_producto;
    $v2pedido->numero_pedido = $numero_pedido;
    $usr = Usuario::obtenerUsuarioEmail($data->email);

    switch ($data->codigo) {
      case 'Socio':
        $log = new Log();
        $log->estado = "Modifica Pedido:" . $numero_pedido . " a: " . "'" . $estado_producto . "'";
        $log->email = $data->email;
        $log->crearLog();

        if ($estado_producto != 'terminado') {
          $v2pedido->tiempo = $tiempo;
        } else {
          $v2pedido->tiempo = '--';
        }

        $v2pedido->modificarPedido();
        $payload = json_encode(array("mensaje" => "modificado con exito pedido Socio"));
        break;
      case 'Bartender':
        $log = new Log();
        $log->estado = "Modifica Pedido:" . $numero_pedido . " a: " . "'" . $estado_producto . "'";
        $log->email = $data->email;
        $log->crearLog();

        if ($estado_producto != 'terminado') {
          $usr->estado = 'ocupado';
          $v2pedido->tiempo = $tiempo;
          $usr->modificarUsuarioEstado();
        } else {
          $usr->estado = 'libre';
          $v2pedido->tiempo = '--';
          $usr->modificarUsuarioEstado();
        }

        $v2pedido->modificarPedido();
        $payload = json_encode(array("mensaje" => "modificado con exito pedido Bartender"));
        break;
      case 'Cocinero':
        $log = new Log();
        $log->estado = "Modifica Pedido:" . $numero_pedido . " a: " . "'" . $estado_producto . "'";
        $log->email = $data->email;
        $log->crearLog();

        if ($estado_producto != 'terminado') {
          $usr->estado = 'ocupado';
          $v2pedido->tiempo = $tiempo;
          $usr->modificarUsuarioEstado();
        } else {
          $usr->estado = 'libre';
          $v2pedido->tiempo = '--';
          $usr->modificarUsuarioEstado();
        }

        $v2pedido->modificarPedido();
        $payload = json_encode(array("mensaje" => "modificado con exito pedio Cocinero"));
        break;
      case 'Cervecero':
        $log = new Log();
        $log->estado = "Modifica Pedido:" . $numero_pedido . " a: " . "'" . $estado_producto . "'";
        $log->email = $data->email;
        $log->crearLog();

        if ($estado_producto != 'terminado') {
          $usr->estado = 'ocupado';
          $v2pedido->tiempo = $tiempo;
          $usr->modificarUsuarioEstado();
        } else {
          $usr->estado = 'libre';
          $v2pedido->tiempo = '--';
          $usr->modificarUsuarioEstado();
        }

        $v2pedido->modificarPedido();
        $payload = json_encode(array("mensaje" => "modificado con exito pedido Cervezero"));
        break;

      default:
        $payload = json_encode(array("mensaje" => "No tenes autorizado modificar estado"));
        break;
    }


    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }


  public function listarPedidos($request, $response, $args)
  {

    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    $data = AutentificadorJWT::ObtenerData($token);


    switch ($data->codigo) {
      case 'Socio':
        $b = Pedido::obtenerTodos();

        json_encode($b);
        break;

      case 'Mozo':
        $b = Pedido::obtenerTodos();

        json_encode($b);
        break;

      case 'Bartender':
        $b = Pedido::obtenerPedidoBartender();
        json_encode($b);
        break;

      case 'Cocinero':
        $b = Pedido::obtenerPedidoCocinero();
        json_encode($b);
        # code...
        break;

      case 'Cervecero':
        $b = Pedido::obtenerPedidoCervecero();
        json_encode($b);
        # code...
        break;

      default:
        'err';
        break;
    }

    // var_dump($k);
    $payload = json_encode(array("LISTA:" => $b));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function pedidosTiempoCorrecto($request, $response, $args)
  {

    $numero_pedido = $args['id_pedido'];
    $fechas = Pedido::obtenerFechasPedido($numero_pedido);

    $arrFechas = explode(':', $fechas->fechamodificacion);

    $minutos = intval($arrFechas[1]);
    $minutos2 = intval($fechas->tiempo);
    $total = $minutos + $minutos2;
    $arrFechas[1] = strval($total);

    $fechaModificada = implode(':', $arrFechas);

    $timezone = date_default_timezone_set('America/Argentina/San_Luis');
    $dateArg = date('Y-m-d H:i:s');
    // var_dump($dateArg);
    if (strcmp($fechaModificada, $dateArg) < 0) {
      # code...
      $payload = json_encode(array('se paso del tiempo pedido'));
    } else {
      $payload = json_encode(array('no se paso'));
    }


    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function descargaPDF($request, $response, $args)
  {

    $listaPedidos = Pedido::obtenerTodos();
    $header = array('MESA', 'PEDIDO', 'USUARIO', 'PRODUCTO', 'CANTIDAD', 'FECHA', 'TICKET');
    // $cadena = "<ul>";
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('arial', 'B', 24);
    $pdf->Cell(177, 10, 'TODOS LOS PEDIDOS [ ADMIN ] ', 1, 2, 'C');
    $pdf->SetFont('courier', 'B', 11);
    $pdf->FancyTable($header, $listaPedidos);
    $pdf->Output('F', './pdf/pedidos_historico.pdf', false);

    $payload = json_encode(array("PDF generado"));

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
      fputcsv($fp, array($row[0], $row[1], $row[2], $row[3], $row[4], $row[5]));
    }

    fclose($fp);

    return $response->withHeader('Content-Disposition', ' attachment; filename="pedido.csv"')->withAddedHeader('Content-Type', 'application/csv');
  }
}
