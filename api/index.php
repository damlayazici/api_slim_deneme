<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';




$app = new Slim\App;



//Courses isimli tablo için route
require '../src/routes/courses.php';



require '../src/routes/user.php';


require '../src/routes/category.php';

// Run app
$app->run();

