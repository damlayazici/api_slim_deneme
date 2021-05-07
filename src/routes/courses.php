<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

error_reporting(E_ALL);
ini_set('display_errors', 1);
$app = new Slim\App;


//tüm kurs listesini getir
$app->get('/courses', function (Request $request, Response $response) {

    $db = new Db();


    try {

        $db = $db->connect();

        $courses = $db->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_OBJ);

        /*if(!empty($courses)){*/
        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", 'application/json')
            ->withJson($courses);

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
    // ---------------------------------- ilk endpoint hazır -----------------------------------//


    $db = null;


});


// sadece kurs detayını getir
$app->get('/course/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute("id");
    $db = new Db();

    try {

        $db = $db->connect();

        $course = $db->query("SELECT * FROM courses WHERE id = ${id}")->fetch(PDO::FETCH_OBJ);
        //fetchall değil fetch çünkü sadece bir id getirilsin istiyoruz


        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", 'application/json')
            ->withJson($course);

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
    // ---------------------------------- ikinci endpoint hazır -----------------------------------//


    $db = null;


});

// kurs ekle
$app->post('/course/add', function (Request $request, Response $response) {


    $body = json_decode($request->getBody());
//postman da gönderilen verilen bağlantısını yapabilmek için gereklidir.


    if (!isset($body->title) || !isset($body->couponCode) || !isset($body->price))
        return $response->withJson(
            array(
                "error" => "Gerekli parametreler gönderilmedi."
            )
        );


    $db = new Db();
    try {

        $db = $db->connect();

        $stst = $db->prepare('INSERT INTO courses (`title`, couponCode, price) VALUES (:title, :couponCode, :price)');

        $stst->bindParam(':title', $body->title);
        $stst->bindParam(':couponCode', $body->couponCode);
        $stst->bindParam(':price', $body->price);

        $course = $stst->execute();

        if ($course) {
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "text" => "Kurs başarılı bir şekilde eklenmiştir.."
                ));

        } else {
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error" => array(
                        "text" => "Ekleme işlemi sırasında bir hata oluştu.."
                    )
                ));
        }
    } catch (PDOException $e) {
        print_r($e);
        return $response->withJson(
            array(
                "error" => array(
                    "text" => $e->getMessage(),
                    "code" => $e->getCode()
                )
            )
        );

    }
});

// ---------------------------------- üçüncü endpoint hazır -----------------------------------//


// kurs güncelle
$app->put('/course/update/{id}', function (Request $request, Response $response) {

    $body = json_decode($request->getBody());
    //postman da gönderilen verilen bağlantısını yapabilmek için gereklidir.

    if (!isset($body->title) || !isset($body->couponCode) || !isset($body->price))
        return $response->withJson(
            array(
                "error" => "Gerekli parametreler gönderilmedi."
            )
        );

    $id = $request->getAttribute("id");

    if ($id) {
        $title = $request->getParam("title"); //getParam request ile gelen veriyi alır
        $couponCode = $request->getParam("couponCode");
        $price = $request->getParam("price");

        $db = new Db();

        try {
            $db = $db->connect();
            $stst = $db->prepare("UPDATE courses SET title= :title, couponCode= :couponCode, price= :price WHERE id= $id");

            $stst->bindParam("title", $body->title);
            $stst->bindParam("couponCode", $body->couponCode);
            $stst->bindParam("price", $body->price);

            $course = $stst->execute();

            if ($course) {
                return $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "text" => "Kurs başarılı bir şekilde güncellenmiştir."
                    ));
            } else {
                return $response
                    ->withStatus(500)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "error" => array(
                            "text" => "Düzenleme işlemi sırasında bir problem oluştu."
                        )
                    ));
            }

        } catch (PDOException $e) {
            print_r($e);
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
    } else {
        return $response->withStatus(500)->withJson(
            array(
                "error" => array(
                    "text" => "ID bilgisi eksik.."
                )
            )
        );
    }
});

// ---------------------------------- dördüncü endpoint hazır -----------------------------------//


// kurs sil
/*$app->delete('/course/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute("id");*/

$app->get('/course/sil/{id}', function (Request $request, Response $response) {

$id = $request->getAttribute("id");


    $db = new Db();
    try {

        $db = $db->connect();

        $stst = $db->prepare("DELETE FROM courses WHERE id= :id");

        $stst->bindParam(':id', $id);

        $course = $stst->execute();

        if ($course) {
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "text" => "Kurs başarılı bir şekilde silinmiştir.."
                ));

        } else {
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error" => array(
                        "text" => "Silme işlemi sırasında bir hata oluştu.."
                    )
                ));
        }
    } catch (PDOException $e) {
        print_r($e);
        return $response->withJson(
            array(
                "error" => array(
                    "text" => $e->getMessage(),
                    "code" => $e->getCode()
                )
            )
        );

    }
});

// ---------------------------------- beşinci endpoint hazır -----------------------------------//