<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 10/12/17
 * Time: 15:58
 */

namespace lbs\control_backend;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use lbs\model\Categorie;
use lbs\control\Writer;
use lbs\model\Size;
use phpDocumentor\Reflection\Types\Self_;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\model\Sandwich;
use lbs\control\Pagination;

/*
 * Class SandwichController
 *
 * Permet de générer les ressources concernant les
 * sandwichs
 * */

class BackendSandwichController extends Pagination
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

  /**
   * @param Request $req
   * @param Response $resp
   * @param $args
   * @return Response|static
   * @throws \Interop\Container\Exception\ContainerException
   */
  public function getOneSandwich(Request $req, Response $resp, $args){

      try{

          if($sandwich = Sandwich::where('id',"=",$args['id'])->firstOrFail())
          {
              /* associe les tailles aux sandwichs */
              $categories = $sandwich->categories()->select('id','nom')->get();
              $tailles = $sandwich->sizes()->select('id','nom','prix')->get();
              $lestailles=[];


              foreach ($tailles as $taille){

                  $unetaille = ['nom' => $taille->nom,
                              'prix' => $taille->prix];

                  array_push($lestailles, $unetaille);
              }

              /* navigation entre les sandwichs */
              $id = $sandwich->id;
              $idpred = $this->container['router']->pathFor('sandwich', ['id'=> $id - 1]);
              $idsuivant = $this->container['router']->pathFor('sandwich', ['id'=> $id + 1]);


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
          /* page 404 */
          return $this->container->view->render($resp, 'erreur404.html',['message'=>'Le sandwich n\'existe pas']);


      }
  }

  /**
   * @param Request $req
   * @param Response $resp
   * @param $args
   * @return Response|static
   */
  public function getSandwich(Request $req, Response $resp, $args){

        $query = Sandwich::select('id','nom','description','img');
        $sandwichs = Pagination::queryNsize($req,$query);
        $sand=[];


      foreach ($sandwichs as $sandwich){

          $unsand = [
              'nom' => $sandwich->nom,
              'description' => $sandwich->description,
              'liensand' => $this->container['router']->pathFor('sandwich', ['id'=>$sandwich->id]),
              'liensuppr' => $this->container['router']->pathFor('deleteOneSandwich', ['id'=>$sandwich->id])
              ];

          array_push($sand, $unsand);
      }

        /* Pagination */
        if(!empty( $_GET['page'])){
           $pageCourante = $_GET['page'];
         }else{
           $pageCourante = 1;
         }

         $pagePred = $pageCourante - 1;
         $pageSuiv = $pageCourante + 1;

         $navigation = $this->container['router']->pathFor('sandwichs');


         // Besoin des categories pour l'ajout des sandwichs
      $categories = Categorie::select('nom','id')->get();
      // Besoin des tailles
      $tailles = Size::select('id','nom')->get();

      // CSRF token name and value
      $nameKey = $this->container->csrf->getTokenNameKey();
      $valueKey = $this->container->csrf->getTokenValueKey();

      $name = $req->getAttribute($nameKey);
      $value = $req->getAttribute($valueKey);

       return $this->container->view->render($resp, 'getsandwichs.html',[
            'categories' => $categories,
            'tailles' => $tailles,
            'sandwichs'=>$sand,
            'numero' => $pageCourante,
            'precedent'=> $navigation. "?page=".$pagePred,
            'suivant'=> $navigation. "?page=". $pageSuiv,
            'name' => $name,
            'value' => $value,
            'namekey' => $nameKey,
            'valuekey' => $valueKey
            ]);
  }

  /**
   * @param Request $req
   * @param Response $resp
   * @param $args
   * @return Response|static
   * @throws \Interop\Container\Exception\ContainerException
   */
  public function getSandwichOfCategorie(Request $req, Response $resp, $args){

      try{
          /* associe les sandwichs aux categories */
          $categorie = Categorie::where('id','=',$args['id'])->firstOrFail();
          $query = $categorie->sandwichs();
          $sandwichs = Pagination::queryNsize($req,$query);
          $sand=[];

          foreach ($sandwichs as $sandwich){

              $unsand = ['nom' => $sandwich->nom,
                          'description' => $sandwich->description,
                          'lien' => $this->container['router']->pathFor('sandwich', ['id'=>$sandwich->id]) ];

              array_push($sand, $unsand);
          }

          /* Navigation entre les catégories */
          $titre = 'de la catégorie ' . $categorie->nom;
          $idcate = $categorie->id;
          $idcatepred = $this->container['router']->pathFor('sandwichOfCategorie', ['id'=> $idcate - 1]);
          $idcatesuiv = $this->container['router']->pathFor('sandwichOfCategorie', ['id'=> $idcate + 1]);


          return $this->container->view->render($resp, 'getsandwichs.html',[
            'sandwichs'=>$sand,
            'titre'=>$titre,
            'numero'=> $idcate,
            'precedent'=> $idcatepred,
            'suivant'=> $idcatesuiv,
          ]);

      } catch (ModelNotFoundException $exception){
          /* page 404 */
          return $this->container->view->render($resp, 'erreur404.html',['message'=>'Aucun sandwichs actuellement dans cette catégorie']);

      }
  }

    public function deleteOneSandwich (Request $req, Response $resp, $args) {

      try{
          $sandwich = Sandwich::where('id','=',$args['id'])->firstOrFail();
          $sandwich->categories()->detach();
          $sandwich->sizes()->detach();
          $sandwich->delete();
          return $resp->withRedirect('/sandwichs[/]', 204);

      } catch (ModelNotFoundException $e){
          $notFoundHandler = $this->container->get('notFoundHandler');
          return $notFoundHandler($req,$resp);
      }
    }

    private static function associatePriceSize ($data,$sandwich,$resp) {

      $tailles = Size::select('id')->get();
      $tabAttach = [];

        // VERIFICATION
        if(!empty($data['tailles']) && !empty($data['prix']) && isset($data['tailles']) && isset($data['prix'])){
            $sandwich->save();
        } else {
            return Writer::json_output($resp,403,['Requête' => "Incomplète ou erronée"]);
        }

        foreach ($tailles as $taille){
          if(!empty($data['tailles'][$taille->id]) && isset($data['tailles'][$taille->id]) && isset($data['prix'][$taille->id]) && !empty($data['prix'][$taille->id])){
              $tabAttach[] = ['sand_id' => $sandwich->id, 'taille_id' => $taille->id, 'prix' => $data['prix'][$taille->id]];
          }
      }
      if(!empty($tabAttach)){
          $sandwich->sizes()->sync($tabAttach);

      } else {
            // La tableau est vide il y a un soucis
          return Writer::json_output($resp,403,['Requête' => "Incomplète ou erronée"]);
      }
    }

    public function createSandwich (Request $req, Response $resp, $args) {

      $data = $req->getParsedBody();
      if(empty($data['categorie'])) {

        $sandwich = new Sandwich();
        $sandwich->nom = filter_var($data['nom'],FILTER_SANITIZE_SPECIAL_CHARS);
        $sandwich->description = filter_var($data['description'],FILTER_SANITIZE_SPECIAL_CHARS);
        $sandwich->type_pain = filter_var($data['type_pain'],FILTER_SANITIZE_SPECIAL_CHARS);

        if(empty($data['img'])){
            $sandwich->img = null;
        } else {
            $sandwich->img = filter_var($data['img'],FILTER_SANITIZE_SPECIAL_CHARS);
        }

        // Il y a forcement un prix et une taille
        self::associatePriceSize($data,$sandwich,$resp);
        return $resp->withRedirect('/sandwichs[/]', 204);

    } else {

          $sandwich = new Sandwich();
          $sandwich->nom = filter_var($data['nom'],FILTER_SANITIZE_SPECIAL_CHARS);
          $sandwich->description = filter_var($data['description'],FILTER_SANITIZE_SPECIAL_CHARS);
          $sandwich->type_pain = filter_var($data['type_pain'],FILTER_SANITIZE_SPECIAL_CHARS);

          if(empty($data['img'])){
              $sandwich->img = null;
          } else {
              $sandwich->img = filter_var($data['img'],FILTER_SANITIZE_SPECIAL_CHARS);
          }

          // Il y a forcement un prix et une taille
          self::associatePriceSize($data,$sandwich,$resp);

          $tabAttach = [];
          foreach ($data['categorie'] as $cat_id){
              // risque que la categorie n'existe pas
              $tabAttach[] = ['sand_id' => $sandwich->id, 'cat_id' => $cat_id];
          }
          $sandwich->categories()->attach($tabAttach);
          return $resp->withRedirect('/sandwichs[/]', 204);
      }
  }
}
