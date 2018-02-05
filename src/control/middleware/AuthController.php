<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 11/01/18
 * Time: 10:31
 */

namespace lbs\control\middleware;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\model\Card;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\control\Writer;

class AuthController
{
    // Récupération du conteneur de dépendance
    /**
     * @var \Slim\Container
     */
    private $container;

    /**
     * AuthController constructor.
     * @param \Slim\Container $container
     */
    public function __construct(\Slim\Container $container){
        $this->container = $container;
    }

    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     */
    public function auth(Request $req, Response $resp, $args){

        // EN UTILISANT RESTED : ENVOYE
        // AUTHO : BASIC
        // BASIC AUTH : ID CARTE - PASS

        // SI HTTP AUTHORIZATION EST MANQUANT
        if(!$req->hasHeader("Authorization")){

            // JE RENVOIE LE TYPE D'AUTH NECESSAIRE
            $resp = $resp->withHeader('WWW-Authenticate', 'Basic realm="api.lbs.local"');
            return Writer::json_output($resp, 401, ['type' => 'error', 'error' => 401, 'message' => 'no authorization header present']);
        }


        // SINON L'EN-TETE EST PRESENT
        $auth = base64_decode(explode( " ", $req->getHeader('Authorization')[0])[1]);
        //SEPARATION DE L'ID DE LA CARTE ET DU MDP
        list($mail, $pass) = explode(':', $auth);

        // ALORS JE TEST AVEC LA BDD
        try {
            $card = Card::where('mail','=',$mail)->firstOrFail();

            // SI MAUVAIS MDP
            if (!password_verify($pass,$card->password)){
                throw new \Exception("Bad credentials");
            }

        } catch (ModelNotFoundException $e){

            $resp = $resp->withHeader('WWW-Authenticate', 'Basic realm="api.lbs.local"');
            return Writer::json_output($resp,401,['error' => "Une authentification est nécessaire pour accéder à la ressource"]);

        } catch (\Exception $e){

            $resp = $resp->withHeader('WWW-Authenticate', 'Basic realm="api.lbs.local"');
            $resp = $resp->withStatus(401);
            return Writer::json_output($resp,401,['error' => $e->getMessage()]);
        }

        // SI ON ARRIVE ICI C'EST QUE TOUT EST BON : ON CREER LE TOKEN JWT
        $mysecret = 'je suis un secret $µ°';
        $token = JWT::encode([
            'iss' => "http://api.lbs.local/auth",
            'aud' => "http://api.lbs.local/",
            'iat' => time(),
            'exp' => time()+3600,
            'uid' => $card->id ],
            $mysecret, 'HS512');

        return Writer::json_output($resp,201,["token" => $token]);
    }
}