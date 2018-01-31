<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 09/01/18
 * Time: 16:30
 */

namespace lbs\model;


use Illuminate\Database\Eloquent\Model;

class Card extends Model
{

    protected $table      = 'carte';
    protected $primaryKey = 'id';
    public $keyType ='string';
    public $incrementing = false;


    public function commandes(){
        return $this->hasMany(Commande::class,'cardID');
    }
}