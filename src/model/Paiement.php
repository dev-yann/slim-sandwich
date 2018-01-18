<?php


namespace lbs\model;


use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{

    protected $table      = 'paiement';
    protected $primaryKey = 'id';
    public $keyType ='string';
    public $incrementing = false;
    public $timestamps = false;

}