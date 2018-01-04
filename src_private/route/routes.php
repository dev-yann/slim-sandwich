<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 22/11/17
 * Time: 11:52
 */

$app->get('/',function($req,$rep,$args){
    // le middleware est executer ici

    $rep->getbody()->write('bonjour yann');


    // le middleware est executer ici

});

//ROUTES COMMANDES
$app->get('/commandes[/]',\lbs\control\privateCommandeController::class .':getCommandes')->setName("commandes");
$app->get('/commandes/{id}',\lbs\control\privateCommandeController::class .':getCommande')->setName("commande");
