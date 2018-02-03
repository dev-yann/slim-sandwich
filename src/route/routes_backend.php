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
$app->get('/categories[/]', \lbs\control_backend\BackendCategoriescontroller::class . ':getCategories')->setName('categories');
$app->get('/categories/{id}[/]', \lbs\control_backend\BackendCategoriescontroller::class . ':getCategorie')->setName('categorie'); // setName fais le lien avec le pathFor du controller, il permet de seulement modifier les routes et de ne pas se préocuper du controller pour le pathfor
$app->get('/categories/{id}/sandwichs[/]',\lbs\control_backend\BackendSandwichController::class . ':getSandwichOfCategorie')->setName('sandwichOfCategorie');


// ROUTES SANDWICHS
$app->get('/sandwichs[/]',\lbs\control_backend\BackendSandwichController::class . ':getSandwich')->setName('sandwichs');
$app->get('/sandwichs/{id}[/]',\lbs\control_backend\BackendSandwichController::class . ':getOneSandwich')->setName('sandwich');
$app->get('/sandwichs/{id}/categories[/]', \lbs\control_backend\BackendSandwichController::class . ':getCategoriesOfSandwich')->setName('categorieOfSandwich');
