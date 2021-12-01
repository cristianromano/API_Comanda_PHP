<?php

require_once __DIR__.'/AutentificadorJWT.php';

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
// use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;


class MWParaAutenticar 
{
    public function Autenticacion(Request $request, RequestHandlerInterface $handler): Response
    {
        // $token = '';
        // $authHeaderString = $request->getHeader('Authorization');
        // foreach ($authHeaderString as $header) {
        //     if(str_contains($header, 'Bearer')){
        //         $token = explode(' ', $header)[1];
        //         break;
        //     }
        // }

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        

        if($token != ''){
            try {
                AutentificadorJWT::VerificarToken($token);
                $request = $request->withAttribute('token', $token);
                return $handler->handle($request);

            } catch (\Throwable $th) {
                $responseFactory = new ResponseFactory();
                $response = $responseFactory->createResponse(400, 'AE: Acceso Denegado');
                $response->getBody()->write(json_encode(['mensaje' => $th->getMessage()]));
                // return $response;
            }
        }else{
            $responseFactory = new ResponseFactory();
            $response = $responseFactory->createResponse(400, 'Acceso Denegado');
            $response->getBody()->write(json_encode(['mensaje' => 'Token VACIO']));
        }
        return $response;
    }
}


















