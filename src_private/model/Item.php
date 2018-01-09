<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 04/01/18
 * Time: 17:18
 */

namespace lbs\model;


use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function commande(){
        return $this->belongsTo(Commande::class, "commande_id");
    }

    public function sandwich(){
        return $this->belongsTo(Sandwich::class,"sand_id");
    }

    // un item n'a qu'une taille
    public function size(){
        return $this->belongsTo(Size::class,"taille_id");
    }


}