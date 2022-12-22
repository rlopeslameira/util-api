<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/log', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getParsedBody();
   
  $sql = " insert into ec_log (id_empresa, texto, acao, datahora) 
          values 
        (". ($dados['id_empresa'] ? $dados['id_empresa'] : "null") . ", '". $dados['texto'] . "', '". $dados['acao'] . "', '".date("Y-m-d H:i:s")."')";

  // echo $sql;
  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  
  $conn = null;

  try 
  {
    echo json_encode($dados);
  }catch (PDOException $ex) {
    echo json_encode(null);
  }    
});

