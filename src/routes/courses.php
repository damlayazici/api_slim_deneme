<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


//tüm kurs listesini getir
$app->get('/courses', function ($request, $response) {


    $db = new Db();


    try {
        $db = $db->connect();
        /*     for($i=0; i<10; $i++){
               echo $i;
                 }*/
        echo "her şey yolunda..";

    } catch (PDOException $e) {
        return $response->withJson(
            array(
                "error" => array(
                    "text" => $e->getMessage(),
                    "code" => $e->getCode()
                )
            )
        );
    }


    $db = null;


});
