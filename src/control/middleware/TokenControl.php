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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\model\Card;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\control\Writer;

class TokenControl
{
    /**
     * @var \Slim\Container
     */
    private $container;

    /**
     * TokenControl constructor.
     * @param \Slim\Container $container
     */
    public function __construct(\Slim\Container $container){
        $this->container = $container;
        $this->result = array();
    }

    /**
     * @param Request $req
     * @param Response $resp
     * @param $next
     * @return Response|static
     */
    public function tokenCommandeControl(Request $req, Response $resp, $next) {

      $token = $req->getQueryParam("token",1);
      $otherToken = $req->getHeader('x-lbs-token');

        // SOIT DANS L'URL SOIT DANS L'ENTETE HTTP
      if($token != 1){

        try{
             $resp = $next($req->withAttribute('token', $token),$resp);
            return $resp;
;

        } catch (ModelNotFoundException $exception){

          $notFoundHandler = $this->container->get('notFoundHandler');
          return $notFoundHandler($req,$resp);

        }

      } elseif (isset($otherToken) && !empty($otherToken)){

        try{

                $resp = $next($req->withAttribute('token', $otherToken),$resp);
            return $resp;
;


        } catch (ModelNotFoundException $exception){

          $notFoundHandler = $this->container->get('notFoundHandler');
          return $notFoundHandler($req,$resp);
        }

      } else {

        return Writer::json_output($resp,401,"Token manquant");
      }
    }
    


    /**
     * @param Request $req
     * @param Response $resp
     * @param $next
     * @return Response|static
     */

    public function tokenControl(Request $req, Response $resp, $next){

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

    /**
     * @param Request $req
     * @param Response $resp
     * @param $next
     * @return Response|static
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function checkCardCommand(Request $req, Response $resp, $next){
        /*

        Body Parsing Middleware
        By default, this middleware will detect the following content types:
        application/x-www-form-urlencoded (standard web-based forms, without file uploads)
        application/json, application/*+json (JSON payloads)

        */
        $tab = $req->getParsedBody();

        // ON RECUPERE L'ID DE LA CARTE
        if(isset($tab['card'])){

            // ON VERIFIE QUE LE TOKEN D'AUTHORISATION EST BIEN ENVOYE
            if(!$req->hasHeader('Authorization')){

                $resp = $resp->withHeader('WWW-Authenticate', 'Bearer realm="api.lbs.local"');
                return Writer::json_output($resp, 401, ['type' => 'error', 'error' => 401, 'message' => 'no authorization header present']);
            }

            // VERIFICATION DU TOKEN & VERIFICATION DE LA CARD ID
            try{

                $header = $req->getHeader('Authorization')[0];
                $mysecret = 'je suis un secret $µ°';
                $tokenString = sscanf($header,"Bearer %s")[0];
                // $token est un objet qui a pour propriété les claims du token
                $token = JWT::decode($tokenString,$mysecret,['HS512']);

                if($token->uid == $tab['card']){
                    try{
                        $req = $req->withAttribute('card',$token->uid);

                    } catch (ModelNotFoundException $e){
                        $notFoundHandler = $this->container->get('notFoundHandler');
                        return $notFoundHandler($req,$resp);
                    }
                } else {
                    return Writer::json_output($resp,401,['error' => "wrong credentials"]);
                }

            } catch(ExpiredException $e){
                // todo: voir si on doit rajouter du code ds les exceptions
                return Writer::json_output($resp,401,['error' => "wrong token"]);
            } catch (SignatureInvalidException $e){
                return Writer::json_output($resp,401,['error' => "wrong token"]);
            } catch (BeforeValidException $e){
                return Writer::json_output($resp,401,['error' => "wrong token"]);
            } catch (\UnexpectedValueException $e){
                return Writer::json_output($resp,401,['error' => "wrong token"]);
            }

            // ON RENVOIT AVEC L'ATTRIBUT
            $resp = $next($req,$resp);
            return $resp;

        } else {
            // ON RENVOIT SANS L'ATTRIBUT
            $resp = $next($req,$resp);
            return $resp;
        }
    }
}