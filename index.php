<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *"); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'src/config/db.php';
require 'vendor/phpqrcode/phpqrcode/qrlib.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);

date_default_timezone_set('America/Belem');

$app->add(function ($request, $response, $next) {  
    return $next($request, $response);         
});

$app->get('/', function (Request $request, Response $response, array $args) {    
    $array = array();
    $array['message'] = "Util - API ::.";
    echo json_encode($array);
});

require 'src/routes/qrcode.php';

$app->run();

