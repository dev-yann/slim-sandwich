<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 09/01/18
 * Time: 16:33
 */

namespace lbs\control;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\model\Card;
use lbs\model\Client;
use Ramsey\Uuid\Uuid;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class cardController
{
    // Récupération du conteneur de dépendance
    private $container;

    public function __construct(\Slim\Container $container){
        $this->container = $container;
    }

    public function createCard(Request $req, Response $resp,$args){

        $tab = $req->getParsedBody();
        // verifier si tout les post existe
        if(isset($tab["nom_client"]) && isset($tab["pass_client"]) && isset($tab["mail_client"])){

            $card = new Card();

            $card->id = Uuid::uuid1();
            $card->nom = filter_var($tab["nom_client"],FILTER_SANITIZE_STRING);
            $card->password = filter_var($tab["pass_client"],FILTER_SANITIZE_STRING);
            $card->mail = filter_var($tab["mail_client"],FILTER_SANITIZE_EMAIL);
            $card->cumul = 0;

            // regarder si le client existe
            try{
                $client = Client::select('id')->where('mail','=',$card->mail)->where('password','=',$card->password)->firstOrFail();

                if(!empty($client) && isset($client)){

                    $card->save();
                    /*$resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(201);
                    $resp->getBody()->write(json_encode($card->toArray()));
                    return $resp;*/

                }

            } catch(ModelNotFoundException $e){

                $notFoundHandler = $this->container->get('notFoundHandler');
                return $notFoundHandler($req,$resp);
            }
        }
    }
}