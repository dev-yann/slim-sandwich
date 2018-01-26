<?php

namespace lbs\control;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\model\Sandwich;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\model\Categorie;
use Slim\Exception\ContainerException;
use Slim\Exception\NotFoundException;
use Slim\Handlers\NotFound;
use lbs\control\Pagination;


class Categoriescontroller extends Pagination {

    // Récupération du conteneur de dépendance

    private $container;
    private $result;

    public function __construct(\Slim\Container $container){

        $this->container = $container;
        $this->result = array();

    }

    // GETTERS
    public function getCategories(Request $req, Response $resp,$args){

        $query = Categorie::select('id','nom','description');
        $categories = Pagination::queryNsize($req,$query);

        foreach ($categories as $category){

            $link = array('links' => ['self' => ['href' => $this->container['router']->pathFor('categorie', ['id'=>$category->id])]]);
            array_push($link,$category);

            array_push($this->result,$link);
        }

        $data = Writer::collection($this->result);
        return Writer::json_output($resp,200,$data);

        //return $this->container->view->render($resp, 'getcategories.html', []);
          //                          ['elements' => [
          //                               nom => $this->result['nom'],
          //                               descr => $this->result['description'],
          //                               cate => $links[self[href]
          //                          ]]);

          }

    public function getCategorie(Request $req, Response $resp,$args){

        try{

            if($categorie = Categorie::where('id',"=",$args['id'])->firstOrFail())
            {
                $link = array('links' => ['sandwichs' => ['href' => $this->container['router']->pathFor('sandwichOfCategorie', ['id'=>$categorie->id])]]);

                $data = Writer::ressource($categorie,$link,'categorie');
                return Writer::json_output($resp,200,$data);

            } else {
                throw new ModelNotFoundException($req, $resp);
            }

        } catch (ModelNotFoundException $exception){

            $notFoundHandler = $this->container->get('notFoundHandler');
            return $notFoundHandler($req,$resp);
        }
    }

    public function getCategoriesOfSandwich(Request $req, Response $resp,$args){

        try{

            $query = Sandwich::where('id','=', $args['id'])->firstOrFail()->categories();
            $categories = self::queryNsize($req,$query);

            foreach ($categories as $category){

                $link = array('links' => ['self' => ['href' => $this->container['router']->pathFor('categorie', ['id'=>$category->id])]]);
                array_push($link,$category);

                array_push($this->result,$link);
            }

            $data = Writer::collection($this->result);
            return Writer::json_output($resp,200,$data);

        } catch (ModelNotFoundException $exception){

            $notFoundHandler = $this->container->get('notFoundHandler');
            return $notFoundHandler($req,$resp);
        }
    }

    // CRÉATION D'UNE CATEGORIE
    public function addCategorie(Request $req, Response $resp,$args){

        $tab = $req->getParsedBody();


        $c = new Categorie();
        $c->nom = filter_var($tab['nom'],FILTER_SANITIZE_SPECIAL_CHARS);
        $c->description = filter_var($tab['description'],FILTER_SANITIZE_SPECIAL_CHARS);

        try{

            $c->save();

        }catch (\Exception $e){
            // revoyer erreur format json
            $resp = $resp->withHeader('Content-Type','application/json');
            $resp->getBody()->write(json_encode(['type' => 'error', 'error' => 500, 'message' => $e->getMessage()]));

        }

        $resp = $resp->withHeader('location',$this->container['router']->pathFor('categorie',['id' => $c->id]));
        return Writer::json_output($resp,201,$c->toArray());
    }

    public function changeCategorie(Request $req, Response $resp,$args){

        // Récuperation de données envoyées
        $tab = $req->getParsedBody();

        // Néttoyage de la donné recupérées
        $id = filter_var($tab['id'],FILTER_SANITIZE_STRING);
        $nom = filter_var($tab['nom'],FILTER_SANITIZE_STRING);
        $description = filter_var($tab['description'],FILTER_SANITIZE_STRING);


        try{

            // Récuperation de l'id dans la base
            $categ = Categorie::select('id')->where('id','=',$id)->firstOrFail();

            $categ->nom = $nom;
            $categ->description = $description;

            $categ->save();

            $resp = $resp->withHeader('location',$this->container['router']->pathFor('categorie',['id' => $categ->id]));
            return Writer::json_output($resp,200,$categ->toArray());

        } catch (ModelNotFoundException $e){

            $notFoundHandler = $this->container->get('notFoundHandler');
            return $notFoundHandler($req,$resp);
        }
    }
}
