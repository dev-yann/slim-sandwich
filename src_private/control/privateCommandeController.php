<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 26/12/17
 * Time: 15:42
 */

namespace lbs\control;


use lbs\model\Commande;
use lbs\control\Pagination;
use lbs\model\Item;
use Ramsey\Uuid\Uuid;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class privateCommandeController extends Pagination
{
    // Récupération du conteneur de dépendance
    private $container;
    private $result;
    public function __construct(\Slim\Container $container){
        $this->container = $container;
        $this->result = array();
    }


    // LISTE LES COMMANDES ##
    /*liste des commandes, filtrées sur l'état , triée par date de livraison et ordre de création –
     permet au point de vente de planifier la préparation des commandes*/

    public function getCommandes(Request $req, Response $resp,$args){

        $query = Commande::select("id", "nom_client", "prenom_client", "mail_client", "livraison","token" ,"etat");

        // Filtre sur l'état: on selectionne les commandes avec l'état renseigné
        // null par défault
        $state = $req->getQueryParam('state',null);
        if(!is_null($state)){
           $query = $query->where("etat",'=',$state);
        }

        // TRIER PAR DATE DE LIVRAISON
        $query = $query->orderBy('livraison');


        $commandes = Pagination::queryNsize($req,$query);

        foreach ($commandes as $commande){
            array_push($this->result,$commande);
        }


        $data = Writer::collection($this->result);
        return Writer::json_output($resp,200,$data);
    }


    /*
     *
     * accès au détail complet d'une commande, avec la liste des items,
     les noms des sandwichs et leur taille sous la forme de ressources imbriqués


    // a travailler, la base doit etre nikel pour que ce soit possible
    */
    public function getCommande(Request $req, Response $resp,$args){

        try{

            if($commande = Commande::where('id',"=",$args['id'])->firstOrFail())
            {

                // si j'obtient la commande
                array_push($this->result,$commande);

                // je veux la liste des items de la commande
                $items = Item::where("commande_id","=",$args['id'])->with('sandwich','size')->get();

                foreach ($items as $item){

                    $tabItem = array('items' => $item);
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


    // CHANGE "NON TRAITE" EN "TRAITÉ"
    public function changeStateCommande(Request $req, Response $resp,$args){

        // Récuperation de données envoyées
        $tab = $req->getParsedBody();

        // CLEAN DATA
        $state = filter_var($tab['state'],FILTER_SANITIZE_STRING);
        $id = filter_var($args['id'],FILTER_SANITIZE_STRING);

        try{

            // Récuperation de l'id dans la base
            $commande = Commande::where('id','=',$id)->firstOrFail();

            if($state === "traite" || $state === "non traite"){
                $commande->etat = $state;
                $commande->save();
            } else {

                return Writer::json_output($resp, 400,"mauvaise syntaxe d'état");
            }



            $resp = $resp->withHeader('location',$this->container['router']->pathFor('commande',['id' => $commande->id]));
            return Writer::json_output($resp,200,$commande->toArray());

        } catch (ModelNotFoundException $e){

            $notFoundHandler = $this->container->get('notFoundHandler');
            return $notFoundHandler($req,$resp);
        }
    }

}