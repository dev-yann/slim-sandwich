<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 26/12/17
 * Time: 15:42
 */

namespace lbs\control;


use lbs\control\middleware\TokenControl;
use lbs\model\Commande;
use lbs\model\Paiement;
use lbs\model\Sandwich;
use lbs\control\Pagination;
use lbs\model\Item;
use Ramsey\Uuid\Uuid;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;



class CommandeController
{
    // Récupération du conteneur de dépendance
  private $container;

  public function __construct(\Slim\Container $container){
    $this->container = $container;
    $this->result = array();
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
      $livraison = new \DateTime($tab['date']);
      $livraison ->format('Y-m-d H:i:s');
      $commande->livraison = $livraison;

        // Création du token
      $commande->token = bin2hex(openssl_random_pseudo_bytes(32));
      $commande->etat = "non traité";

      if(isset($tab['card'])){
          $commande->card = filter_var($tab["card"],FILTER_SANITIZE_STRING);

      }

        // indiquer le numero de carte -> prouver que c'est le propriétaire de la carte
        // table carte :
        // id , nom, mdp -> /carte/id/auth ( nom + mdp) -> reponse : token jwt( token avec de l'info : ex: num de la carte)  avec ce token je peu :
        // lire la carte -> get /carte/id + token
        // à partir de la je peux créer une commande avec le token jwt de la carte , la commande est donc associé à la carte ( associate je pense )

        // VERIFICATION DES DONNÉES RECU
       /*
       Mécanisme de validation des données reçues pour la création d'une commande.

       Il faut valider:

       présence obligatoire du nom, mail et date/heure de livraison,
       présence facultative du prénom de l'utilisateur,
       format des noms-prénoms : chaîne de caractères alphabétiques, format de l'@mail
       format de la date, et validité de la date (date future uniquement)

       */


       // if(isset($tab['nom_client']) &&(isset($tab['prenom_client'])) && (isset($tab['mail_client'])) && (isset($tab['date'])))
       //  {
       try{

        $commande->save();
        $resp = $resp->withHeader('location',$this->container['router']->pathFor('commande',['id' => $commande->id]));
        $resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(201);
        $resp->getBody()->write(json_encode($commande->toArray()));
        return $resp;

      } catch (\Exception $e){
            // revoyer erreur format jsno
        $resp = $resp->withHeader('Content-Type','application/json')->withStatus(500);
        $resp->getBody()->write(json_encode(['type' => 'error', 'error' => 500, 'message' => $e->getMessage()]));

      }
   //   else {

   //   Writer::json_output($resp,400, "Données manquantes");
   // }
    }


    public function getCommandes (Request $req, Response $resp, $args) {
      $query = Commande::all();

      return Writer::json_output($resp,200,$query);
    }
    public function getState (Request $req, Response $resp,$args) {
     try{

      if($commande = Commande::select("etat")->where('id',"=",$args['id'])->firstOrFail())
        {


          return Writer::json_output($resp,200,$commande);

        } else {
          throw new ModelNotFoundException($req, $resp);
        }

      } catch (ModelNotFoundException $exception){

        $notFoundHandler = $this->container->get('notFoundHandler');
        return $notFoundHandler($req,$resp);
      }
    }
    public function getCommande (Request $req, Response $resp,$args) {

      $token = $req->getQueryParam("token",1);
      $otherToken = $req->getHeader('x-lbs-token');

        // SOIT DANS L'URL SOIT DANS L'ENTETE HTTP
      if($token != 1){

        try{

          $requeteBase = Commande::where("id","=", $args['id'])->where("token","=",$token)->firstOrFail();
          $resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(200);
          $resp->getBody()->write(json_encode($requeteBase));
          return $resp;

        } catch (ModelNotFoundException $exception){

          $notFoundHandler = $this->container->get('notFoundHandler');
          return $notFoundHandler($req,$resp);

        }

      } elseif (isset($otherToken) && !empty($otherToken)){

        try{

          $request = Commande::where("id","=", $args['id'])->where("token","=",$otherToken)->firstOrFail();
          return Writer::json_output($resp,200,$request);

        } catch (ModelNotFoundException $exception){

          $notFoundHandler = $this->container->get('notFoundHandler');
          return $notFoundHandler($req,$resp);
        }

      } else {

        return Writer::json_output($resp,401,"Token manquant");
      }
    }

    public function editCommande(Request $req,Response $resp,$args) {
      $commande = Commande::where("id","=",$args["id"])->first();
      $tab = $req->getParsedBody();
      $commande->nom_client = filter_var($tab["nom_client"],FILTER_SANITIZE_STRING);
      $commande->prenom_client= filter_var($tab["prenom_client"],FILTER_SANITIZE_STRING);
      $commande->mail_client = filter_var($tab["mail_client"],FILTER_SANITIZE_EMAIL);
      $commande->livraison = $tab["livraison"];

      try{

        $commande->save();

      }catch (\Exception $e){
            // revoyer erreur format jsno
        $resp = $resp->withHeader('Content-Type','application/json');
        $resp->getBody()->write(json_encode(['type' => 'error', 'error' => 500, 'message' => $e->getMessage()]));

      }
      $resp = $resp->withHeader('location',$this->container['router']->pathFor('commande',['token' => $commande->token]));
      $resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(200);
      $resp->getBody()->write(json_encode($commande->toArray()));
      return $resp;
    }
    public function getFacture (Request $req,Response $resp,$args) {
     try{

      if($commande = Commande::Select("nom_client","prenom_client","etat")->where('etat','=','paid')->where('id',"=",$args['id'])->firstOrFail())
        {

                // si j'obtient la commande
          array_push($this->result,$commande);

                // je veux la liste des items de la commande
          $items = Item::where("commande_id","=",$args['id'])->with('sandwich','size')->get();

          foreach ($items as $item){
            $test = Sandwich::where("id","=",$item->sandwich->id)->first()->sizes()->get();
            $tabItem = array('item' => $item->sandwich->nom,'taille id' => $item->taille_id,'quantite' => $item ->quantite, 'prix unitaire' => $item);
    
            array_push($this->result,$tabItem);
          }

          return Writer::json_output($resp,200,$this->result);

        } else {

          throw new ModelNotFoundException($req, $resp);
        }

      } catch (ModelNotFoundException $exception){

        $notFoundHandler = $this->container->get('notFoundHandler');
        return $notFoundHandler($req,$resp);

      }

    }

    public function payCommande (Request $req,Response $resp,$args) {
     try{

      if($commande = Commande::where('id',"=",$args['id'])->firstOrFail())
        {
            $tab = $req->getParsedBody();
          $paiement = new Paiement();
          $paiement->id = Uuid::uuid1();

          $paiement->commande_id = $commande->id;
          $paiement->carte_bancaire = filter_var($tab["carte_bancaire"],FILTER_SANITIZE_NUMBER_FLOAT);
          $date_expiration = \DateTime::createFromFormat ('m-y',$tab['date_expiration']);

          $paiement->date_expiration = $date_expiration->format('m-y');
          $paiement->save();
          $commande->etat = "paid";
          $commande->save();
          return Writer::json_output($resp,201,$paiement);
      } else {

        throw new ModelNotFoundException($req, $resp);
      }

    } catch (ModelNotFoundException $exception){

      $notFoundHandler = $this->container->get('notFoundHandler');
      return $notFoundHandler($req,$resp);

    }

  }
}