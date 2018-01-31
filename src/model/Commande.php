<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 26/12/17
 * Time: 15:55
 */

namespace lbs\model;
use lbs\model\Card;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    /**
     * @var string
     */
    protected $table = 'commande';
    /**
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * @var string
     */
    public $keyType ='string';
    /**
     * @var bool
     */
    public $timestamps = false;
    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(){
        return $this->hasMany(Item::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sandwichs(){
        return $this->belongsToMany(Sandwich::class, 'sand2com', 'com_id', 'sand_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function card(){
        return $this->belongsTo(Card::class,'cardID');
    }



}