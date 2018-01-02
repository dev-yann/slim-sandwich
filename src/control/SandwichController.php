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

                $link = array('links' => ['categories' => ['href' => $this->container['router']->pathFor('categorieOfSandwich', ['id'=>$sandwich->id])],'tailles' => ['href' => $this->container['router']->pathFor('sizeOfSandwich', ['id'=>$sandwich->id])]]);


                $data = Writer::ressource($sandwich,$link,'sandwich');
                array_push($data, ["Categories" => [$categories]]);
                array_push($data, ["Tailles" => [$tailles]]);
                return Writer::json_output($resp,200,$data);

            } else {

                throw new ModelNotFoundException($req, $resp);
            }

        } catch (ModelNotFoundException $exception){

            $notFoundHandler = $this->container->get('notFoundHandler');
            return $notFoundHandler($req,$resp);

        }

    }

    // RÉCUPÉRER PLUSIEURS SANDWICHS
    public function getSandwich(Request $req, Response $resp,$args){

        $query = Sandwich::select('id','nom','description','img');
        $sandwichs = Pagination::queryNsize($req,$query);

        foreach ($sandwichs as $sandwich){

            $link = array('links' => ['self' => ['href' => $this->container['router']->pathFor('categorie', ['id'=>$sandwich->id])]]);
            array_push($link,$sandwich);

            array_push($this->result,$link);
        }

        $data = Writer::collection($this->result);
        return Writer::json_output($resp,200,$data);
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