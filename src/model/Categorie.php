<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 21/11/17
 * Time: 15:31
 */
namespace lbs\model;
class Categorie extends \Illuminate\Database\Eloquent\Model {

    protected $table      = 'categorie';
    protected $primaryKey = 'id';
    public    $timestamps = false;

}
