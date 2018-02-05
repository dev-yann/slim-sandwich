<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 10/12/17
 * Time: 15:58
 */

namespace lbs\control_backend;
use lbs\model\Commande;
use lbs\control\Writer;
use lbs\model\Size;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\control\Pagination;
use lbs\model\Item;
use lbs\model\Tarif;


/*
 * Class BackendSandwichController
 *
 * Permet d'afficher le CA et le nombre de commande passé dans la journée
 * */

class BackendCommandeController extends Pagination
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
   * SandwichController constructor.
   * @param \Slim\Container $container
   */
  public function __construct(\Slim\Container $container){

  	$this->container = $container;
  	$this->result = array();

  }
  public function getBilan(Request $req, Response $resp) {
  	$now = date('Y-m-d');
  	$count = Commande::where("date_paiement","like",$now."%")->count();
  	$commandes = Commande::where("date_paiement","like",$now."%")->get();
  	$total = 0;
  	foreach ($commandes as $commande) {

  		$prixcommande = 0;

  		$items = Item::where("commande_id","=",$commande->id)->with('sandwich','size')->get();

  		foreach ($items as $item){

  			$prix = Tarif::select("prix")->where("taille_id","=",$item->taille_id)->where("sand_id","=",$item->sand_id)->first();

  			$prixcommande += $item->quantite * $prix['prix'];
  		}
  		$total+=$prixcommande;
  	} 



  	return $this->container->view->render($resp, 'getbilan.html', [ 'count' => $count, 'now'=>$now,'total'=>$total]);
  }
}