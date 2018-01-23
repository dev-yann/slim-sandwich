<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 21/11/17
 * Time: 17:49
 */


$tab = array('settings'=>['displayErrorDetails'=>true,
                          'production' => true,
                          'tmpl_dir' => __DIR__.'/../templates'], /* chemin des templates */

             'view' => function($c) {
                return new \Slim\Views\Twig (
                  $c['settings']['tmpl_dir'],
                  ['debug' => true,
                   'cache' => $c['settings']['tmpl_dir']
                  ]
                );
              }
            );
return $tab;
