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
    protected $hidden = ['pivot'];

    public function sandwichs(){

        return $this->belongsToMany(Sandwich::class,'sand2cat', "cat_id","sand_id")->withPivot(['sand_id','cat_id']);
    }
}
