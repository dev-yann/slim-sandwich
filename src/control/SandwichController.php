<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 10/12/17
 * Time: 15:58
 */

namespace lbs\control;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\model\Categorie;
use lbs\control\Writer;
use lbs\model\Size;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\model\Sandwich;
use lbs\control\Pagination;

class SandwichController extends Pagination
{
    // Récupération du conteneur de dépendance

    private $container;
    private $result;

    public function __construct(\Slim\Container $container){

        $this->container = $container;
        $this->result = array();

    }


    // RÉCUPÉRER UN SANDWICH
    public function getOneSandwich(Request $req, Response $resp,$args){

        try{

            if($sandwich = Sandwich::where('id',"=",$args['id'])->firstOrFail())
            {
                $categories = $sandwich->categories()->select('id','nom')->get();
                $tailles = $sandwich->sizes()->select('id','nom','prix')->get();

                $id = $sandwich->id;
                $idpred = $id -1;
                $idsuivant = $id +1;

                $lestailles=[];

                foreach ($tailles as $taille){

                    $unetaille = ['nom' => $taille->nom,
                                'prix' => $taille->prix];

                    array_push($lestailles, $unetaille);
                }


                return $this->container->view->render($resp, 'getone.html',[
                  'nom' => $sandwich->nom,
                  'description' => $sandwich->description,
                  'precedent'=>$idpred,
                  'suivant'=>$idsuivant,
                  'numero' => $sandwich->id,
                  'tailles'=>$tailles]);

            } else {

                throw new ModelNotFoundException($req, $resp);
            }

        } catch (ModelNotFoundException $exception){

            return $this->container->view->render($resp, 'erreur404.html',['message'=>'Le sandwich n\'existe pas']);

        }
    }

    // RÉCUPÉRER PLUSIEURS SANDWICHS
    public function getSandwich(Request $req, Response $resp,$args){

        $query = Sandwich::select('id','nom','description','img');
          $sandwichs = Pagination::queryNsize($req,$query);
          $sand=[];

        foreach ($sandwichs as $sandwich){

            $link = array('links' => ['self' => ['href' => $this->container['router']->pathFor('sandwich', ['id'=>$sandwich->id])]]);
            array_push($link,$sandwich);

            array_push($this->result,$link);


            $unsand = ['nom' => $sandwich->nom,
                        'description' => $sandwich->description,
                      'lien' => $link['links']['self']['href'] ];

            array_push($sand, $unsand);
        }

          return $this->container->view->render($resp, 'getsandwichs.html',['sandwichs'=>$sand]);
    }

    public function getSandwichOfCategorie(Request $req, Response $resp,$args){

        try{
            $categorie = Categorie::where('id','=',$args['id'])->firstOrFail();
            $query = $categorie->sandwichs();
            $sandwichs = Pagination::queryNsize($req,$query);

            foreach ($sandwichs as $sandwich){

                $link = array('links' => ['self' => ['href' => $this->container['router']->pathFor('sandwich', ['id'=>$sandwich->id])]]);
                array_push($link,$sandwich);

                array_push($this->result,$link);
            }

            $data = Writer::collection($this->result);
            return Writer::json_output($resp,200,$data);

        } catch (ModelNotFoundException $exception){

            $notFoundHandler = $this->container->get('notFoundHandler');
            return $notFoundHandler($req,$resp);

        }
    }
}
