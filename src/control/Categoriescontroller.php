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

    /**
     * @var \Slim\Container
     */
    private $container;
    /**
     * @var array
     */
    private $result;

    /**
     * Categoriescontroller constructor.
     * @param \Slim\Container $container
     */
    public function __construct(\Slim\Container $container){

        $this->container = $container;
        $this->result = array();

    }

    // GETTERS

    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     */
    public function getCategories(Request $req, Response $resp, $args){

        $query = Categorie::select('id','nom','description');
        $categories = Pagination::queryNsize($req,$query);

        $unecate = [];

        foreach ($categories as $category){

            $link = array('links' => ['self' => ['href' => $this->container['router']->pathFor('categorie', ['id'=>$category->id])]]);
            array_push($link,$category);

            array_push($this->result,$link);


            $cate = ['nom' => $category->nom,
                     'description' => $category->description,
                     'liencate' => $this->container['router']->pathFor('categorie', ['id'=>$category->id]),
                     'liensand' => $this->container['router']->pathFor('sandwichOfCategorie', ['id'=>$category->id])];

            array_push($unecate, $cate);
        }

         return $this->container->view->render($resp, 'getcategories.html',['categories'=>$unecate]);

          }

    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function getCategorie(Request $req, Response $resp, $args){

        try{

            if($categorie = Categorie::where('id',"=",$args['id'])->firstOrFail())
            {
                $link = array('links' => ['sandwichs' => ['href' => $this->container['router']->pathFor('sandwichOfCategorie', ['id'=>$categorie->id])]]);

                $data = Writer::ressource($categorie,$link,'categorie');

                $id = $categorie->id;
                $idpred = $this->container['router']->pathFor('categorie', ['id'=> $id - 1]);
                $idsuivant = $this->container['router']->pathFor('categorie', ['id'=> $id + 1]);


                return $this->container->view->render($resp, 'getone.html',[
                  'nom' => $categorie->nom,
                  'description' => $categorie->description,
                  'precedent'=>$idpred,
                  'suivant'=>$idsuivant,
                  'numero' => $categorie->id]);

            } else {
                throw new ModelNotFoundException($req, $resp);
            }

        } catch (ModelNotFoundException $exception){

          return $this->container->view->render($resp, 'erreur404.html',['message'=>'La catégorie n\'existe pas']);

        }
    }

    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function getCategoriesOfSandwich(Request $req, Response $resp, $args){

        try{

            $reqsandwichs = Sandwich::where('id','=', $args['id'])->firstOrFail();
            $query =$reqsandwichs ->categories();
            $categories = self::queryNsize($req,$query);

            $cate=[];

            foreach ($categories as $category){

                $link = array('links' => ['self' => ['href' => $this->container['router']->pathFor('categorie', ['id'=>$category->id])]]);
                array_push($link,$category);

                array_push($this->result,$link);

                $unecate = ['nom' => $category->nom,
                            'description' => $category->description,
                            'lien' => $link['links']['self']['href'] ];

                array_push($cate, $unecate);
            }

            $titre = ' du sandwich ' . $reqsandwichs->nom;
            $idsand = $reqsandwichs->id;
            $idsandpred = $this->container['router']->pathFor('categorieOfSandwich', ['id'=> $idsand - 1]);
            $idsandsuiv = $this->container['router']->pathFor('categorieOfSandwich', ['id'=> $idsand + 1]);


            return $this->container->view->render($resp, 'getcategories.html',[
              'categories'=>$cate,
              'titre'=>$titre,
              'numero'=> $idsand,
              'precedent'=> $idsandpred,
              'suivant'=> $idsandsuiv,
            ]);


/*            $data = Writer::collection($this->result);
            return Writer::json_output($resp,200,$data);*/

        } catch (ModelNotFoundException $exception){

          return $this->container->view->render($resp, 'erreur404.html',['message'=>'Ce sandwichs n\'existe pas']);

        }
    }

    // CRÉATION D'UNE CATEGORIE

    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     */
    public function addCategorie(Request $req, Response $resp, $args){

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

    /**
     * @param Request $req
     * @param Response $resp
     * @param $args
     * @return Response|static
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function changeCategorie(Request $req, Response $resp, $args){

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
