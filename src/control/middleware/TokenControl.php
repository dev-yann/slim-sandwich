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

        } catch (SignatureInvalidException $e){

        } catch (BeforeValidException $e){

        } catch (\UnexpectedValueException $e){

        }


        $resp = $next($req,$resp);
        return $resp;
    }
}