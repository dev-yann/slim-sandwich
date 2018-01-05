<?php
namespace lbs\control;


use lbs\model\Item;
use Ramsey\Uuid\Uuid;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\control\Pagination;


class ItemController {

	public function addItemToCommande (Request $req, Response $resp) {

		$tab = $req->getParsedBody();
		$item = new Item();
		$item->sand_id = filter_var($tab["sand_id"],FILTER_SANITIZE_STRING);
		$item->commande_id = filter_var($tab["commande_id"],FILTER_SANITIZE_STRING);
		$item->taille_id = filter_var($tab["taille_id"],FILTER_SANITIZE_STRING);
		$item->quantite = filter_var($tab["quantite"],FILTER_SANITIZE_STRING);

		try {
		// VERIFIER LE FORMULAIRE OU LE FAIRE DURANT LE MIDDLEWARE ???.
			$item->save();
			$resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(201);
			$resp->getBody()->write(json_encode($item->toArray()));
			return $resp;

		} catch (Exception $e) {
			$resp = $resp->withHeader('Content-Type','application/json');
			$resp->getBody()->write(json_encode(['type' => 'error', 'error' => 500, 'message' => $e->getMessage()]));
		}

	}
	

	public function editItem (Request $req, Response $resp,$args) {
		$item = Item::where("id","=",$args['id'])->first();
		$tab = $req->getParsedBody();
		$item->sand_id = filter_var($tab["sand_id"],FILTER_SANITIZE_STRING);
		$item->commande_id = filter_var($tab["commande_id"],FILTER_SANITIZE_STRING);
		$item->taille_id = filter_var($tab["taille_id"],FILTER_SANITIZE_STRING);
		$item->quantite = filter_var($tab["quantite"],FILTER_SANITIZE_STRING);

		try {
		// VERIFIER LE FORMULAIRE OU LE FAIRE DURANT LE MIDDLEWARE ???.
			$item->save();
			$resp = $resp->withHeader('Content-Type', 'application/json')->withStatus(200);
			$resp->getBody()->write(json_encode($item->toArray()));
			return $resp;

		} catch (Exception $e) {
			$resp = $resp->withHeader('Content-Type','application/json');
			$resp->getBody()->write(json_encode(['type' => 'error', 'error' => 500, 'message' => $e->getMessage()]));
		}

	}
}
