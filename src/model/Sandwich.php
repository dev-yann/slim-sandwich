<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 05/12/17
 * Time: 15:25
 */

namespace lbs\model;


class Sandwich extends \Illuminate\Database\Eloquent\Model
{

    protected $table      = 'sandwich';
    protected $primaryKey = 'id';
    public    $timestamps = false;
    protected $hidden = ['pivot'];

    public function categories(){

        return $this->belongsToMany(Categorie::class,'sand2cat', "sand_id","cat_id");
    }

    public function sizes(){
        return $this->belongsToMany(Size::class, 'tarif', 'sand_id', 'taille_id')->withPivot(['prix']);
    }

    public function items(){

        return $this->hasMany(Item::class,"sand_id");
    }

    public function commandes(){
        return $this->belongsToMany(Commande::class, 'sand2com', 'sand_id', 'com_id');
    }
}