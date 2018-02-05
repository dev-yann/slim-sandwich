<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 26/12/17
 * Time: 15:42
 */

namespace lbs\control_private;


use lbs\model\Commande;
use lbs\control\Pagination;
use lbs\control\Writer;
use lbs\model\Item;
use Ramsey\Uuid\Uuid;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class privateCommandeController
 * @package lbs\control_private
 */
class privateCommandeController extends Pagination
{
    /**
     * @var \Slim\Container
     */
    private $container;
    /**
     * @var array
     */
    private $result;

    /**
     * privateCommandeController constructor.
     * @param \Slim\Container $container
     */
    public function __construct(\Slim\Container $container){
        $this->container = $container;
        $this->result = array();
    }

    /**
     * @param Request $req
     * @param Response $resp
     */
    public function test (Request $req, Response $resp) {
        echo "test";
    }

    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     */
    public function getCommandes(Request $req, Response $resp, $args){

        $query = Commande::select("id", "nom_client", "prenom_client", "mail_client", "livraison","token" ,"etat");
        $state = $req->getQueryParam('state',null);
        if(!is_null($state)){
           $query = $query->where("etat",'=',$state);
        }

        $query = $query->orderBy('livraison');

        $commandes = Pagination::queryNsize($req,$query);

        foreach ($commandes as $commande){
            array_push($this->result,$commande);
        }

        $data = Writer::collection($this->result);
        return Writer::json_output($resp,200,$data);
    }

    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function getCommande(Request $req, Response $resp, $args){

        try{

            if($commande = Commande::where('id',"=",$args['id'])->firstOrFail())
            {

                array_push($this->result,$commande);

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

    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function changeStateCommande(Request $req, Response $resp, $args){

        $tab = $req->getParsedBody();
        
        $state = filter_var($tab['state'],FILTER_SANITIZE_STRING);
        $id = filter_var($args['id'],FILTER_SANITIZE_STRING);

        try{

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