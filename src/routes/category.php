<?php


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

error_reporting(E_ALL);
ini_set('display_errors', 1);
$app = new Slim\App;

$app->get('/category', function (Request $request, Response $response) {

    $db = new Db();

    try {
        $db = $db->connect();

        $category = $db->query("SELECT * FROM category")->fetchAll(PDO::FETCH_OBJ);


        return $response
            ->withStatus(200)
            ->withHeader("Content-Type", 'application/json')
            ->withJson($category);

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

$app->post('/category/add', function (Request $request, Response $response) {

    $body = json_decode($request->getBody());

    if (!isset($body->category_name))
        return $response->withJson(
            array(
                "error" => "Gerekli parametreler gönderilmedi."
            )
        );

    $db = new Db();

    try {
        $db = $db->connect();

        $category = $db->prepare('INSERT INTO category (category_name) VALUES (:category_name)');

        $category->bindParam(':category_name', $body->category_name);

        $category = $category->execute();

        if($category){
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "text"=>"Kategori başarılı bir şekilde eklenmiştir."
                ));
        }else{
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error"=>array(
                        "text"=>"Ekleme işlemi sırasında bir hata oluştu."
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
// ---------------------------------- ikinci endpoint hazır -----------------------------------//

$app->get('/category/sil/{category_id}', function (Request $request, Response $response){

    $category_id = $request->getAttribute("category_id");

    $db = new Db();

    try{
        $db = $db->connect();
        $category = $db->prepare("DELETE FROM category WHERE category_id= :category_id");
        $category->bindParam(':category_id', $category_id);
        $category = $category->execute();

        if($category){
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "text"=>"Kategori başarılı bir şekilde silinmiştir."
                ));
        }else{
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error"=>array(
                        "text"=>"Silme işlemi sırasında bir hata oluştu"
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