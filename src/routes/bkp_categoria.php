<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/categorias', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getQueryParams();

  $sql = " Select * from ec_categoria WHERE status = 0";

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetchAll(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});


