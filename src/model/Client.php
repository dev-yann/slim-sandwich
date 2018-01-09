<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 09/01/18
 * Time: 17:20
 */

namespace lbs\model;


use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table      = 'client';
    protected $primaryKey = 'id';
    public    $timestamps = false;
    protected $hidden = ['pivot'];
}