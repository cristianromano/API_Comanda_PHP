<?php

require_once __DIR__.'/AutentificadorJWT.php';
require_once __DIR__.'/MWParaAutenticar.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
// use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class MWAutorizar
{
    public $rolesUsuarios = ['Socio'];
    public $rolesProductos = ['Socio'];
    public $rolesCliente = ['Socio','Cliente'];
    public $rolesMesas = ['Socio', 'Mozo'];
    public $rolesPedidos = ['Socio', 'Mozo'];
    public $rolesDescargar = ['Socio'];
    public $rolesEncuestas = ['Socio','Cliente'];
    public $rolesPedidosinformes = ['Socio'];
    public $rolesCuenta = ['Socio', 'Mozo'];
    
    public function Autorizacion(Request $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //Obtengo la seccion desde la ruta del uri 
        // var_dump($request);
        $path = $request->getUri()->getPath();
        $seccion = explode('/', $path)[1];

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        
        // $request->getAttribute('token')

        // Recupero el token desde el atributo del request y luego los datos del usuario
        $data = AutentificadorJWT::ObtenerData($token);
        // var_dump($data);

        if(in_array($data->codigo, $this->{'roles' . ucfirst($seccion)})){
            $response = $handler->handle($request);
        }else{
            $responseFactory = new ResponseFactory();
            $response = $responseFactory->createResponse(400, 'Access Denied');
            $response->getBody()->write(json_encode(["mensaje"=>"No tiene los permisos necesarios"]));
            return $response;
        }
        return $response;
    }
}