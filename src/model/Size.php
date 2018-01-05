<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 20/12/17
 * Time: 19:31
 */

namespace lbs\model;


use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'taille_sandwich';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $hidden = ['pivot'];

    public function sandwichs(){
        return $this->belongsToMany(Sandwich::class, 'tarif', 'taille_id', 'sand_id')->withPivot(['prix']);
    }

    public function items(){
        return $this->hasMany(Item::class);
    }
}