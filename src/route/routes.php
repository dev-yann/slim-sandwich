<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 22/11/17
 * Time: 11:52
 */
$app->get('/',\lbs\control\Home::class . ':home');
$app->get('/bonjour/{name}',function($req,$rep,$args){
    // le middleware est executer ici

    $rep->getbody()->write('bonjour '.$args['name']);


    // le middleware est executer ici

});


// ROUTES CATEGORIES
$app->get('/categories[/]', \lbs\control\Categoriescontroller::class . ':getCategories')->setName('categories');
$app->get('/categories/{id}[/]', \lbs\control\Categoriescontroller::class . ':getCategorie')->setName('categorie'); // setName fais le lien avec le pathFor du controller, il permet de seulement modifier les routes et de ne pas se préocuper du controller pour le pathfor
// Ajout d'une categorie
$app->post('/categories[/]', \lbs\control\Categoriescontroller::class . ':addCategorie')->setName('categories');
// Modification d'une categorie
$app->put('/categories[/]', \lbs\control\Categoriescontroller::class . ':changeCategorie')->setName('categorie');


// ROUTES SANDWICHS
$app->get('/sandwichs[/]',\lbs\control\SandwichController::class . ':getSandwich')->setName('sandwichs');
$app->get('/sandwichs/{id}',\lbs\control\SandwichController::class . ':getOneSandwich')->setName('sandwich');
$app->get('/categories/{id}/sandwichs',\lbs\control\SandwichController::class . ':getSandwichOfCategorie')->setName('sandwichOfCategorie');
$app->get('/sandwichs/{id}/categories', \lbs\control\Categoriescontroller::class . ':getCategoriesOfSandwich')->setName('categorieOfSandwich');


//ROUTES TAILLES
$app->get('/sandwichs/{id}/sizes', \lbs\control\sizeController::class . ':sizeOfSandwich')->setName('sizeOfSandwich');
$app->get('/sizes/{id}', \lbs\control\sizeController::class . ':sizeOfSandwich')->setName('size');

//ROUTES COMMANDES
$app->get('/commande/{id}',\lbs\control\CommandeController::class .':getCommande')->setName("commande");
$app->get('/commande/{id}/state',\lbs\control\CommandeController::class .':getState')->setName("stateCommande");
$app->get('/commandes[/]',\lbs\control\CommandeController::class .':getCommandes')->setName("commandes");
$app->post('/commande[/]',\lbs\control\CommandeController::class .':createCommande')->setName("createCommande");
$app->put('/commande/{id}',\lbs\control\CommandeController::class .':modifyCommande')->setName("modifyCommande");

//ROUTES ITEMS 
$app->post('/item[/]',\lbs\control\ItemController::class.':addItemToCommande')->setName("addItem");
$app->put('/item/{id}',\lbs\control\ItemController::class.':editItem')->setName("editItem");
