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
        // SI TOUS LES POSTS SONT ENVOYÉS
        if(isset($tab["nom_client"]) && isset($tab["pass_client"]) && isset($tab["mail_client"])){

            $card = new Card();
            $card->id = Uuid::uuid1();
            $card->nom = filter_var($tab["nom_client"],FILTER_SANITIZE_STRING);
            $card->password = password_hash(filter_var($tab["pass_client"],FILTER_SANITIZE_STRING),PASSWORD_DEFAULT);
            $card->mail = filter_var($tab["mail_client"],FILTER_SANITIZE_EMAIL);
            $card->cumul = 0;

            // SI LA CARTE EXIST

            $client = Card::where("mail",'=',$card->mail)->first();

            if(empty($client) && !isset($client)){

                // ET SI IL N'A PAS DE CARTE
                $card->save();
                Writer::json_output($resp,201,['id' => $card->id]);

            } else {
                return Writer::json_output($resp,200,"Cette carte existe déjà");
            }
        }
    }
}
