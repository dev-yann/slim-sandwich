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

    protected $table      = 'card';
    protected $primaryKey = 'id';
    public    $timestamps = true;
    const created_at = 'date_creation';
    protected $hidden = ['pivot'];

}