<?php
namespace lbs\control;


use lbs\model\Item;
use lbs\model\Sandwich;
use lbs\model\Commande;
use Ramsey\Uuid\Uuid;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\control\Pagination;


class ItemController {

    /**
     * @param Request $req
     * @param Response $resp
     * @return Response|static
     */
    private $container;

    /**
     * CommandeController constructor.
     * @param \Slim\Container $container
     */
    public function __construct(\Slim\Container $container){
    	$this->container = $container;
    }
    public function addItemToCommande (Request $req, Response $resp) {

    	$tab = $req->getParsedBody();
    	$item = new Item();
    	$item->sand_id = filter_var($tab["sand_id"],FILTER_SANITIZE_STRING);
    	$item->commande_id = filter_var($tab["commande_id"],FILTER_SANITIZE_STRING);
    	$item->taille_id = filter_var($tab["taille_id"],FILTER_SANITIZE_STRING);
    	$item->quantite = filter_var($tab["quantite"],FILTER_SANITIZE_STRING);


    	try {
    		if ( Sandwich::where('id','=',$item->sand_id)->firstOrFail() && Commande::where('id','=',$item->commande_id)->firstOrFail() ) {
    			$sand=Sandwich::where('id','=',$item->sand_id)->first()->whereHas("sizes",function ($q) use ($item){
    				$q->where("id","=",$item->taille_id);
    			})->get();
    			$item->save();
    			$resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(201);
    			$resp->getBody()->write(json_encode($sand));
    			return $resp;

    		}
    		else {
    			throw new ModelNotFoundException ($req,$resp);
    		}


    	} catch (ModelNotFoundException $exception) {
    		$notFoundHandler = $this->container->get('notFoundHandler');
    		return $notFoundHandler($req,$resp);
    	}

    }


    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     */
    public function editItem (Request $req, Response $resp, $args) {
    	$item = Item::where("id","=",$args['id'])->first();
    	$tab = $req->getParsedBody();
    	$item->sand_id = filter_var($tab["sand_id"],FILTER_SANITIZE_STRING);
    	$item->commande_id = filter_var($tab["commande_id"],FILTER_SANITIZE_STRING);
    	$item->taille_id = filter_var($tab["taille_id"],FILTER_SANITIZE_STRING);
    	$item->quantite = filter_var($tab["quantite"],FILTER_SANITIZE_STRING);

    	try {

    		$item->save();
    		$resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(200);
    		$resp->getBody()->write(json_encode($item->toArray()));
    		return $resp;

    	} catch (Exception $e) {
    		$resp = $resp->withHeader('Content-Type','application/json');
    		$resp->getBody()->write(json_encode(['type' => 'error', 'error' => 500, 'message' => $e->getMessage()]));
    	}

    }

    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     */
    public function deleteItem (Request $req, Response $resp, $args) {
    	$item = Item::where("id","=",$args['id'])->first();
    	try {
    		$item->delete();
    		return Writer::json_output($resp,200,"Suppression réussie");

    	} catch (Exception $e) {
    		$resp = $resp->withHeader('Content-Type','application/json');
    		$resp->getBody()->write(json_encode(['type' => 'error', 'error' => 500, 'message' => $e->getMessage()]));
    	}
    }
}
