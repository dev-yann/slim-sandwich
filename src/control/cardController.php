<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 09/01/18
 * Time: 16:33
 */

namespace lbs\control;


use lbs\model\Card;
use Ramsey\Uuid\Uuid;

class cardController
{
    // Récupération du conteneur de dépendance
    private $container;

    public function __construct(\Slim\Container $container){
        $this->container = $container;
    }

    public function createCard(Request $req, Response $resp,$args){

        $tab = $req->getParsedBody();
        $card = new Card();

        $card->id = Uuid::uuid1();
        $card->nom = filter_var($tab["nom_client"],FILTER_SANITIZE_STRING);
        $card->password = filter_var($tab["pass_client"],FILTER_SANITIZE_STRING);

        $card->mail = filter_var($tab["mail_client"],FILTER_SANITIZE_EMAIL);
        // revoir la date
        $card->date_creation = \DateTime::createFromFormat('d-m-Y',$tab['livraison']);
        $card->cumul = 0;


        // verifier si tout les post existe
        if(isset($tab["nom_client"]) && isset($tab["pass_client"]) && isset($tab["mail_client"]) && isset($tab['livraison'])){

            // regarder si le client existe

            $client = 
        }

    }
}