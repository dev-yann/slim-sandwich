<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 26/12/17
 * Time: 15:55
 */

namespace lbs\model;


use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $table = 'commande';
    protected $primaryKey = 'id';
    public $keyType ='string';
    public $timestamps = false;
    public $incrementing = false;

    public function items(){
        return $this->hasMany(Item::class);
    }

    public function sandwichs(){
        return $this->belongsToMany(Sandwich::class, 'sand2com', 'com_id', 'sand_id');
    }

}