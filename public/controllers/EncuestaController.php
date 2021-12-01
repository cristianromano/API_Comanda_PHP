<?php

require_once './models/Pedido.php';
require_once './models/Producto.php';
require_once './models/Mesa.php';
require_once './models/Usuario.php';
require_once './models/Encuesta.php';
require_once './models/fpdf.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends Encuesta
{


    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        //   $header = $request->getHeaderLine('Authorization');
        //   $token = trim(explode("Bearer", $header)[1]);

        //   $data = AutentificadorJWT::ObtenerData($token); 

        $puntacion_mesa = $parametros['puntacion_mesa'];
        $puntacion_mozo = $parametros['puntacion_mozo'];
        $puntacion_trabajador = $parametros['puntacion_trabajador'];
        $puntuacion_lugar = $parametros['puntuacion_lugar'];
        $numero_pedido = $parametros['numero_pedido'];
        $descripcion = $parametros['descripcion'];

        $encuesta = new Encuesta();
        $encuesta->puntuacion_mesa = $puntacion_mesa;
        $encuesta->puntuacion_mozo = $puntacion_mozo;
        $encuesta->puntuacion_trabajador = $puntacion_trabajador;
        $encuesta->puntuacion_lugar = $puntuacion_lugar;
        $encuesta->numero_pedido = $numero_pedido;
        $encuesta->descripcion = $descripcion;

        $encuesta->crearEncuesta();

        $payload = json_encode($encuesta);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
