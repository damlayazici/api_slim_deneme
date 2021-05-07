<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

error_reporting(E_ALL);
ini_set('display_errors', 1);
$app = new Slim\App;


//tüm kurs listesini getir
$app->get('/user', function (Request $request, Response $response) {

    $db = new Db();


    try {

        $db = $db->connect();

        $courses = $db->query("SELECT * FROM user")->fetchAll(PDO::FETCH_OBJ);

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
    $db = null;

});
// ---------------------------------- ilk endpoint hazır -----------------------------------//

$app->get('/user/{user_id}', function (Request $request, Response $response) {

    $user_id = $request->getAttribute("user_id");
    $db = new Db();

    try {

        $db = $db->connect();

        $user = $db->query("SELECT * FROM user WHERE user_id = ${user_id}")->fetch(PDO::FETCH_OBJ);
        //fetchall değil fetch çünkü sadece bir id getirilsin istiyoruz


        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", 'application/json')
            ->withJson($user);

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
// ---------------------------------- ikinci endpoint hazır -----------------------------------//

$app->post('/user/add', function (Request $request, Response $response) {

    $body = json_decode($request->getBody());

    if (!isset($body->user_name) || !isset($body->user_lastName) || !isset($body->user_mail) || !isset($body->user_address)
        || !isset($body->user_tel) || !isset($body->user_password)) {
        return $response->withJson(
            array(
                "error" => "Gerekli parametlereler girilmemiştir!"
            )
        );
    }
    $db = new Db();

    try {
        $db = $db->connect();
        $user = $db->prepare('INSERT INTO user (user_name, user_lastName, user_mail, 
                  user_address, user_tel, user_password) VALUES (:user_name, :user_lastName, :user_mail, :user_address, :user_tel, :user_password)');

        $user->bindParam(':user_name', $body->user_name);
        $user->bindParam(':user_lastName', $body->user_lastName);
        $user->bindParam(':user_mail', $body->user_mail);
        $user->bindParam(':user_address', $body->user_address);
        $user->bindParam(':user_tel', $body->user_tel);
        $user->bindParam(':user_password', $body->user_password);

        $user = $user->execute();

        if ($user) {
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "text" => "User başarılı bir şekilde eklenmiştir.."
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

$app->post('/user/update/{user_id}', function (Request $request, Response $response) {

    $body = json_decode($request->getBody());


    if (!isset($body->user_name) || !isset($body->user_lastName) || !isset($body->user_mail) ||
        !isset($body->user_address) || !isset($body->user_tel) || !isset($body->user_password)) {
        return $response->withJson(
            array(
                "error" => "Gerekli parametreler alınamamıştır."
            )
        );
    }
    $user_id = $request->getAttribute("user_id");

    if ($user_id) {
        $user_name = $request->getParam("user_name");
        $user_lastName = $request->getParam("user_lastName");
        $user_mail = $request->getParam("user_mail");
        $user_address = $request->getParam("user_address");
        $user_tel = $request->getParam("user_tel");
        $user_password = $request->getParam("user_password");

        $db = new db();

        try {
            $db = $db->connect();

            $updated = $db->prepare("UPDATE user SET user_name= :user_name, user_lastName= :user_lastName, user_mail= :user_mail, 
                user_tel= :user_tel, user_address= :user_address, user_password= :user_password WHERE user_id= $user_id");

            $updated->bindParam("user_name", $body->user_name);
            $updated->bindParam("user_lastName", $body->user_lastName);
            $updated->bindParam("user_mail", $body->user_mail);
            $updated->bindParam("user_tel", $body->user_tel);
            $updated->bindParam("user_address", $body->user_address);
            $updated->bindParam("user_password", $body->user_password);

            $updated = $updated->execute();

            if ($updated) {
                return $response
                    ->withStatus(200)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "text" => "User başarılı bir şekilde güncellendi"
                    ));
            } else {
                return $response
                    ->withStatus(500)
                    ->withHeader("Content-Type", 'application/json')
                    ->withJson(array(
                        "error" => array(
                            "text" => "User güncelleme işlemi sırasında bir hata oluştu"
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
    }
});
// ---------------------------------- dördüncü endpoint hazır -----------------------------------//


$app->get('/user/delete/{user_id}', function (Request $request, Response $response) {

    $user_id = $request->getAttribute("user_id");

    $db = new Db();

    try {
        $db = $db->connect();

        $deleted = $db->prepare("DELETE FROM user WHERE user_id= :user_id");

        $deleted->bindParam(':user_id', $user_id);

        $deleted = $deleted->execute();


        if ($deleted) {
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "text" => "User başarılı bir şekilde silinmiştir.."
                ));

        } else {
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error" => array(
                        "text" => "User silme işlemi sırasında bir hata oluştu.."
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