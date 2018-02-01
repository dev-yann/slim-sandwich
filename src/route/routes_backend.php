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
$app->get('/categories[/]',\lbs\control_backend\BackendCategoriescontroller::class .':getCategories')->setName("categories");
