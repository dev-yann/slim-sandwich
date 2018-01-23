<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 16/01/18
 * Time: 15:41
 */
namespace lbs\control\middleware;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\control\Writer;

class TokenControl
{

    public function tokenControl(Request $req, Response $resp, $next){

        // une personne peu faire des commandes avec sa carte de fidélité,
        // donc on doit vérifier si l'id de la carte est bien envoyé et si
        // elle est bien associé au token


        // TODO: le token est transporté dans le header Authorization
        if(!$req->hasHeader('Authorization')){

            $resp = $resp->withHeader('WWW-Authenticate', 'Bearer realm="api.lbs.local"');
            return Writer::json_output($resp, 401, ['type' => 'error', 'error' => 401, 'message' => 'no authorization header present']);

        }

        try{

            $header = $req->getHeader('Authorization')[0];

            $mysecret = 'je suis un secret $µ°';
            $tokenString = sscanf($header,"Bearer %s")[0];
            $token = JWT::decode($tokenString,$mysecret,['HS512']);

        } catch(ExpiredException $e){
            // todo: voir si on doit rajouter du code ds les exceptions

        } catch (SignatureInvalidException $e){

        } catch (BeforeValidException $e){

        } catch (\UnexpectedValueException $e){

        }


        $resp = $next($req,$resp);
        return $resp;
    }

    public function checkCardCommand(Request $req, Response $resp, $next){

        $tab = $req->getParsedBody();
        if(isset($tab['card'])){
            // L'id de la carte est envoyée donc

            // verification du token

            // le token correspond à la carte ?

        }
    }
}