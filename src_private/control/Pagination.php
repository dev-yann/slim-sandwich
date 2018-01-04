<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 14/12/17
 * Time: 10:38
 */

namespace lbs\control;
use \Psr\Http\Message\ServerRequestInterface as Request;


class Pagination
{
    protected static $t,$total,$size,$page,$date;

    protected static function myPagination(Request $req,$query){

        // DEFINITION DE LA DATE
        self::$date = date("d-m-y");

        // DEFINITION DU NOMBRE DE PAGE
        $nbPage = $req->getQueryParam('page',1);// si il est absent alors 1

        if($nbPage < 0){
            $nbPage = 1;
        }

        self::$page = $nbPage;

        // RECUPERATION DU TYPE DE PAIN , NULL PAR DEFAUT
        $paramT = $req->getQueryParam('t',null);
        if(!is_null($paramT)){
            $query = $query->where('type_pain','like','%'.$paramT.'%');
        }
        self::$t = $paramT;

        // VERIFICATION DE LA PRESENCE OU NON D'IMAGE
        $boolImg = (is_null($req->getQueryParam('img',null))) ? 0 : 1;
        if($boolImg === 1){
            $query = $query->whereNotNull('img');
        }

        // RECUPERATION DE NOMBRE DE RÉSULTAT GLOBAL
        $total = $query->count();
        self::$total = $total;

        // RECUPERATION DU NOMBRE DE RESULTAT PAR PAGE
        $size = $req->getQueryParam('size',10);

        // Pour ne pas dépasser la page maximum
        $nbpageMax = round(($total/$size)+1);

        if($nbPage > $nbpageMax){

            self::$page = $nbpageMax;
        }
        self::$size = intval($size);

        return $query;

    }

    protected static function queryNsize($req,$query){

        return self::myPagination($req,$query)->skip((self::$page - 1) * self::$size)->take(self::$size)->get();
    }
}