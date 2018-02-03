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

    /**
     * @var string
     */
    protected $table      = 'sandwich';
    /**
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * @var bool
     */
    public    $timestamps = false;
    /**
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories(){

        return $this->belongsToMany(Categorie::class,'sand2cat', "sand_id","cat_id");
    }

    /**
     * @return $this
     */
    public function sizes(){
        return $this->belongsToMany(Size::class, 'tarif', 'sand_id', 'taille_id')->withPivot(['prix']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(){

        return $this->hasMany(Item::class,"sand_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function commandes(){
        return $this->belongsToMany(Commande::class, 'sand2com', 'sand_id', 'com_id');
    }
}