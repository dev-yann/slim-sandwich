<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 26/12/17
 * Time: 15:42
 */

namespace lbs\control;


use lbs\model\Commande;
use Ramsey\Uuid\Uuid;

class CommandeController
{
    // Récupération du conteneur de dépendance
    private $container;

    public function __construct(\Slim\Container $container){
        $this->container = $container;
    }

    /*
     * Création d'une commande via une requête POST
     * Renvoie $resp : tableau représentant la commande
     *
     * */
    public function createCommande(Request $req, Response $resp) {

        // Récupération des données envoyées
        $tab = $req->getParsedBody();

        $commande = new Commande();

        // id globalement unique - unicité très probable
        $commande->id = Uuid::uuid1();

        $commande->nom_client = filter_var($tab["nom_client"],FILTER_SANITIZE_STRING);
        $commande->prenom_client = filter_var($tab["prenom_client"],FILTER_SANITIZE_STRING);
        $commande->mail_client = filter_var($tab["mail_client"],FILTER_SANITIZE_EMAIL);
        // A voir avec le prof, c'est un peu flou
        $commande->livraison = \DateTime::createFromFormat('d-m-Y',$tab['livraison']['date'].' '.$tab['livraison']['heure']);

        // Création du token
        $commande->token = bin2hex(openssl_random_pseudo_bytes(32));
        $commande->etat = "non traité";
        try{

            $commande->save();

            $resp = $resp->withHeader('location',$this->container['router']->pathFor('commande',['token' => $commande->token]));
            $resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(201);
            $resp->getBody()->write(json_encode($commande->toArray()));
            return $resp;

        } catch (\Exception $e){
            // revoyer erreur format jsno
            $resp = $resp->withHeader('Content-Type','application/json');
            $resp->getBody()->write(json_encode(['type' => 'error', 'error' => 500, 'message' => $e->getMessage()]));
        }
    }


}