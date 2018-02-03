<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 22/11/17
 * Time: 11:52
 */

// ROUTES CATEGORIES
$app->get('/categories[/]', \lbs\control\Categoriescontroller::class . ':getCategories')->setName('categories');
$app->get('/categories/{id}[/]', \lbs\control\Categoriescontroller::class . ':getCategorie')->setName('categorie'); // setName fais le lien avec le pathFor du controller, il permet de seulement modifier les routes et de ne pas se préocuper du controller pour le pathfor
// Ajout d'une categorie
$app->post('/categories[/]', \lbs\control\Categoriescontroller::class . ':addCategorie')->setName('categories')->add(\lbs\control\middleware\CheckFormulaire::class.':checkFormulaire')->setArgument('fields',array("nom","description"));
// Modification d'une categorie
$app->put('/categories[/]', \lbs\control\Categoriescontroller::class . ':changeCategorie')->setName('categorie')->add(\lbs\control\middleware\CheckFormulaire::class.':checkFormulaire')->setArgument('fields',array("nom","description","id"));


// ROUTES SANDWICHS
$app->get('/sandwichs[/]',\lbs\control\SandwichController::class . ':getSandwich')->setName('sandwichs');
$app->get('/sandwichs/{id}[/]',\lbs\control\SandwichController::class . ':getOneSandwich')->setName('sandwich');
$app->get('/categories/{id}/sandwichs[/]',\lbs\control\SandwichController::class . ':getSandwichOfCategorie')->setName('sandwichOfCategorie');
$app->get('/sandwichs/{id}/categories[/]', \lbs\control\Categoriescontroller::class . ':getCategoriesOfSandwich')->setName('categorieOfSandwich');


//ROUTES TAILLES
$app->get('/sandwichs/{id}/sizes[/]', \lbs\control\sizeController::class . ':sizeOfSandwich')->setName('sizeOfSandwich');
$app->get('/sizes/{id}[/]', \lbs\control\sizeController::class . ':sizeOfSandwich')->setName('size');

//ROUTES COMMANDES
$app->get('/commande/{id}[/]',\lbs\control\CommandeController::class .':getCommande')->setName("commande");
$app->get('/commande/{id}/state[/]',\lbs\control\CommandeController::class .':getState')->setName("stateCommande");
$app->get('/commandes[/]',\lbs\control\CommandeController::class .':getCommandes')->setName("commandes");

//ROUTES COMMANDES
$app->post('/commande[/]',\lbs\control\CommandeController::class .':createCommande')->setName("createCommande")->add(\lbs\control\middleware\TokenControl::class.':checkCardCommand')->add(\lbs\control\middleware\CheckFormulaire::class.':checkFormulaire')->setArgument('fields',array("nom_client","prenom_client","mail_client","date"));
$app->put('/commande/{id}',\lbs\control\CommandeController::class .':editCommande')->setName("editCommande");
$app->post('/commande/{id}/pay',\lbs\control\CommandeController::class . ':payCommande')->add(\lbs\control\middleware\CheckFormulaire::class.':checkFormulaire')->setArgument('fields',array("carte_bancaire","date_expiration"));
$app->get('/commande/{id}/facture',\lbs\control\CommandeController::class . ':getFacture')->setName("getFacture");
//ROUTES ITEMS
$app->post('/item[/]',\lbs\control\ItemController::class.':addItemToCommande')->setName("addItem")->add(\lbs\control\middleware\CheckFormulaire::class.':checkFormulaire')->setArgument('fields',array("sand_id","commande_id","taille_id","quantite"));
$app->put('/item/{id}',\lbs\control\ItemController::class.':editItem')->setName("editItem")->add(\lbs\control\middleware\CheckFormulaire::class.':checkFormulaire')->setArgument('fields',array("sand_id","commande_id","taille_id","quantite"));
$app->delete('/item/{id}',\lbs\control\ItemController::class.':deleteItem')->setName("deleteItem");


// ROUTES CARD
$app->post('/card[/]',\lbs\control\cardController::class.':createCard')->add(\lbs\control\middleware\CheckFormulaire::class.':checkFormulaire')->setArgument('fields',array("nom_client","pass_client","mail_client"));


// ON AJOUTE LE MIDDLEWARE TOKENCONTROL QUI DÉCODE LE TOKEN ET RENVOI DES EXCEPTIONS SI ERREUR
$app->get('/card/{id}',\lbs\control\cardController::class.':getCard')->add(\lbs\control\middleware\TokenControl::class.':tokenControl');


// ROUTES AUTHORIZATION
$app->get('/card[/]', \lbs\control\middleware\AuthController::class.':auth');
