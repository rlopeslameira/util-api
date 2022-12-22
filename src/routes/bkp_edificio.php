<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/edificio', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getQueryParams();

  $sql = " Select * from ec_edificiocomercial WHERE url = '" . $dados["url"] . "' ";

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetch(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});

$app->get('/edificio/url', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getQueryParams();

  $sql = " Select * from ec_edificiocomercial WHERE url = '" . $dados["url"] . "' ";

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetch(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});



