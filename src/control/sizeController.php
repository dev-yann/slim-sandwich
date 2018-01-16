<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 19/12/17
 * Time: 12:30
 */

namespace lbs\control;


use lbs\model\Sandwich;
use lbs\model\Size;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class sizeController extends Pagination
{

    private $container;
    private $result;

    public function __construct(\Slim\Container $container){

        $this->container = $container;
        $this->result = array();

    }


    public function sizeOfSandwich(Request $req, Response $resp,$args){

       try{
            $sandwich = Sandwich::where('id','=',$args['id'])->firstOrFail();


            $query = $sandwich->sizes();
            $sizes = self::queryNsize($req,$query);


            foreach ($sizes as $size){

                $link = array('links' => ['self' => ['href' => $this->container['router']->pathFor('size', ['id'=>$size->id])]]);
                array_push($link,$size);

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