<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 14/12/17
 * Time: 11:56
 */

namespace lbs\control;
use \Psr\Http\Message\ResponseInterface as Response;

class Writer extends Pagination
{

    static public function ressource($tab,$link, $extension){
        $head = ["type" => "ressource","meta" => ["local" => "fr-FR"]];
        $result = array($head,$link,$extension => [$tab]);
        return $result;
    }

    static public function collection($tab){
        $head = ["type" => "collection","meta" => ["count" => self::$total,"size" => self::$size,"date" => self::$date]];
        $result = [$head,$tab];
        return $result;
    }

    static public function linkRessource($container,$setname,$id,$n){



    }

    static public function json_output(Response $resp,$int,$data){

        $resp = $resp->withHeader('Content-Type','application/json')->withStatus($int);
        $resp->getBody()->write(json_encode($data));
        return $resp;
    }
}