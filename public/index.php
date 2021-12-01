<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/EncuestaController.php';
require_once './controllers/DescargaController.php';
require_once './middlewares/AutentificadorJWT.php';
require_once './middlewares/MWParaAutenticar.php';
require_once './middlewares/MWParaAutorizar.php';
require_once './middlewares/JSONMiddleware.php';
require_once './db/AccesoDatos.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
// $app->setBasePath('/public');
$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("hola alumnos de los lunes!");
    return $response;
});

// peticiones
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
  })->add(\MWParaAutenticar::class . ':Autenticacion')->add(\MWAutorizar::class . ':Autorizacion');

    //->add(JsonMiddleware::class . ':process');
    
  $app->group('/productos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \ProductoController::class . ':TraerTodos');
    $group->get('/{producto}', \ProductoController::class . ':TraerUno');
    $group->post('[/]', \ProductoController::class . ':CargarUno');
  })->add(\MWParaAutenticar::class . ':Autenticacion')->add(\MWAutorizar::class . ':Autorizacion');

  $app->group('/pedidos', function (RouteCollectorProxy $group) {
    $group->get('[/]', \PedidoController::class . ':listarPedidos');
    $group->get('/{pedido}', \PedidoController::class . ':TraerUno');
    $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\MWParaAutenticar::class . ':Autenticacion')->add(\MWAutorizar::class . ':Autorizacion');
    $group->put('/modificar', \PedidoController::class . ':modificarEstadoPedido');
  });

  $app->group('/cliente', function (RouteCollectorProxy $group) {
    $group->get('/{codigo}', \PedidoController::class . ':verTiempo')->add(\MWParaAutenticar::class . ':Autenticacion')->add(\MWAutorizar::class . ':Autorizacion');
  });

  $app->group('/cuenta', function (RouteCollectorProxy $group) {
    $group->get('/{codigo}', \PedidoController::class . ':obtenerCuenta');
  });

  $app->group('/pedidosinformes', function (RouteCollectorProxy $group) {
    $group->get('/{id_pedido}', \PedidoController::class . ':pedidosTiempoCorrecto')->add(\MWParaAutenticar::class . ':Autenticacion')->add(\MWAutorizar::class . ':Autorizacion');
  });

  $app->group('/encuestas', function (RouteCollectorProxy $group) {
    $group->post('/', \EncuestaController::class . ':CargarUno')->add(\MWParaAutenticar::class . ':Autenticacion')->add(\MWAutorizar::class . ':Autorizacion');
  });

  $app->group('/mesas', function (RouteCollectorProxy $group) {
    $group->put('/', \MesaController::class . ':ModificarUno')->add(\MWParaAutenticar::class . ':Autenticacion')->add(\MWAutorizar::class . ':Autorizacion');
  });

  $app->group('/descargar', function (RouteCollectorProxy $group) {
    $group->get('/pdf', \PedidoController::class . ':descargaPDF');
    $group->get('/csv/usuarios', \UsuarioController::class . ':descargaCSV');
    $group->get('/csv/productos', \ProductoController::class . ':descargaCSV');
    $group->get('/csv/pedidos', \PedidoController::class . ':descargaCSV');
    $group->get('/csv/mesas', \MesaController::class . ':descargaCSV');
  })->add(\MWParaAutenticar::class . ':Autenticacion')->add(\MWAutorizar::class . ':Autorizacion');

  $app->group('/logueo', function (RouteCollectorProxy $group) {
    $group->post('[/]', \UsuarioController::class . ':Loguear');
  });

// Run app
$app->run();

