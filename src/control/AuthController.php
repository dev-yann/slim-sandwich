<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 11/01/18
 * Time: 10:31
 */

namespace lbs\control;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
// peut etre utiliser cette methode dans la classe carte , a voir



class AuthController
{
    // Récupération du conteneur de dépendance
    private $container;

    public function __construct(\Slim\Container $container){
        $this->container = $container;
    }

    public function auth(Request $req, Response $resp,$args){

        // SI HTTP AUTHORIZATION EST MANQUANT
        if($req->hasHeader("Authorization")){

            // JE RENVOIE LE TYPE D'AUTH NECESSAIRE
            $resp = $resp->withHeader('Content-Type','application/json')->withStatus(401);
            $resp->withHeader('WWW-Authenticate', 'Basic realm="MonApplication"');
            $resp->getBody()->write(json_encode('Authorisation manquante'));
            return $resp;

        }

        // SINON L'AUTH EST BIEN ENVOYE
        $auth = base64_decode(explode( " ", $req->getHeader('Authorization')[0])[1]);
        list($user, $pass) = explode(':', $auth);
        

    }
}