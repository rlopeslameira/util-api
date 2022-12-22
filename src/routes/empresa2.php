<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/empresas', function (Request $request, Response $response, array $args) {  
  // $conn = $request->getAttribute('conn');
  
  // $queryBuilder = $conn->createQueryBuilder();

  // $queryBuilder->from('ec_empresa');

  $dados = $request->getQueryParams();
  

  $where = "";

  if (isset($dados["nome"]))
  {  
    if ($dados["nome"] != ""){
      $where .= " and nome like '%" . $dados["nome"] . "%'";
    }
  }

  $sql = " Select * from ec_empresa WHERE id_empresa > 0 " . $where;

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetchAll(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});

$app->get('/empresas/consulta', function (Request $request, Response $response, array $args) {  

  $dados = $request->getQueryParams();
    
  $separada = explode($separador, $cadena);
  $where = "";

  foreach ($separada as $item) {
    if ($where != "")
      $where .= " OR ";

    $where .= " (unaccent(ec_edificiocomercial.url) = unaccent('". trim(pg_escape_string($item)) ."')      
      or unaccent(ec_empresa.nome) ilike unaccent('%". trim(pg_escape_string($item)) ."%')
      or unaccent(ec_empresa.descricao) ilike unaccent('%". trim(pg_escape_string($item)) ."%')
      or unaccent(ec_empresa.tags) ilike unaccent('%". trim(pg_escape_string($item)) ."%')) ";
  }

  if ($where != "")
      $where = " and  " . $where;

  $sql = " Select ec_empresa.*, ec_imagem.arquivo, string_agg(CONCAT(cat.nome, ','), '') as categorias from ec_empresa 
    left join ec_imagem 
      on ec_imagem.id_empresa = ec_empresa.id_empresa
      and ec_imagem.area = '2'
    inner join ec_edificiocomercial
      on ec_edificiocomercial.id_edificiocomercial = ec_empresa.id_edificiocomercial
    left join ec_empresa_categoria empcat
      on empcat.id_empresa = ec_empresa.id_empresa
    inner join ec_categoria cat
      on cat.id_categoria = empcat.id_categoria
    where 
      ec_empresa.status = 0 ". $where ."  group by ec_empresa.id_empresa, ec_imagem.arquivo ";

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetchAll(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});

$app->get('/empresas/destaques', function (Request $request, Response $response, array $args) {  

  $dados = $request->getQueryParams();
  
  $sql = " Select ec_empresa.*, ec_imagem.arquivo, string_agg(CONCAT(cat.nome, ','), '') as categorias from ec_empresa 
          inner join ec_edificiocomercial
            on ec_edificiocomercial.id_edificiocomercial = ec_empresa.id_edificiocomercial
          left join ec_imagem 
            on ec_imagem.id_empresa = ec_empresa.id_empresa
            and ec_imagem.area = '2'
          left join ec_empresa_categoria empcat
            on empcat.id_empresa = ec_empresa.id_empresa
          inner join ec_categoria cat
            on cat.id_categoria = empcat.id_categoria
          where ec_edificiocomercial.url = '". $dados["url"] ."' and ec_empresa.destaque = 0
          group by ec_empresa.id_empresa, ec_imagem.arquivo
        limit 3 ";

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetchAll(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});

$app->get('/empresas/procurados', function (Request $request, Response $response, array $args) {  

  $dados = $request->getQueryParams();
  
  $sql = " Select ec_empresa.*, ec_imagem.arquivo, string_agg(CONCAT(cat.descricao, ','), '') as categorias from ec_empresa 
          inner join ec_edificiocomercial
            on ec_edificiocomercial.id_edificiocomercial = ec_empresa.id_edificiocomercial
          left join ec_imagem 
            on ec_imagem.id_empresa = ec_empresa.id_empresa
            and ec_imagem.area = '0'
          left join ec_empresa_categoria empcat
            on empcat.id_empresa = ec_empresa.id_empresa
          inner join ec_categoria cat
            on cat.id_categoria = empcat.id_categoria
          where ec_edificiocomercial.url = '". $dados["url"] ."'
          group by ec_empresa.id_empresa, ec_imagem.arquivo
          order by ec_empresa.destaque DESC
        limit 3 ";

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetchAll(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});

$app->get('/empresa/destaque', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getQueryParams();

  $sql = " Select * from ec_destaque
  WHERE id_empresa = " . $dados["id_empresa"] ;

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetchAll(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});

$app->get('/empresa/categoria', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getQueryParams();

  $sql = " Select c.*, ec.id_empresa_categoria from ec_empresa_categoria ec
  inner join ec_categoria c
    On c.id_categoria = ec.id_categoria
  WHERE id_empresa = " . $dados["id_empresa"] ;

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetchAll(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});

$app->get('/empresa/servico', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getQueryParams();

  $sql = " Select * from ec_servico
  WHERE id_empresa = " . $dados["id_empresa"] ;

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetchAll(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});

$app->post('/empresa', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getParsedBody();

  try {

    $bd = new db();
    $conn = $bd->connect();
        
    $sql = " insert into ec_empresa
    (nome, descricao, url, site, email, telefone, endereco, instagram, facebook, 
    twitter, tags, titulo_banner, descricao_banner, status, id_usuario, id_edificiocomercial) VALUES 
    ('".$dados["nome"]."', 
    '".addslashes($dados["descricao"])."' , 
    '".addslashes($dados["url"])."', 
    '".addslashes($dados["site"])."', 
    '".$dados["email"]."', 
    '". addslashes($dados["telefone"]) ."', 
    '". addslashes($dados["endereco"]) ."', 
    '". addslashes($dados["instagram"])."', 
    '". addslashes($dados["facebook"])."',
    '". addslashes($dados["twitter"])."',
    '". addslashes($dados["tags"])."',
    '". addslashes($dados["titulo_banner"])."',
    '". addslashes($dados["descricao_banner"])."',
     ". $dados["status"].",
     ". $dados["id_usuario"].",
     ".$dados["id_edificiocomercial"].")";

    $resultado = $conn->query($sql);
    $id = $conn->lastInsertId();
    $dados["id_empresa"] = $id;   

    echo json_encode($dados);	

  } catch (Exception $ex) {
    echo json_encode($ex);
  }
  
});

$app->put('/empresa/status', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getParsedBody();
  try {
    $sql = " update ec_empresa set 
        status = '".$dados["status"]."' 
        where id_empresa = '".$dados["id_empresa"]."';";	

    $bd = new db();
    $conn = $bd->connect();
    
    $resultado = $conn->query($sql);

    $conn = null;

    echo json_encode($dados);
    
  } catch (Exception $ex) {
    echo json_encode($ex);
  }
  
});

$app->put('/empresa', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getParsedBody();
   
   
  $sql = " update ec_empresa set nome = '".($dados["nome"])."', 
                             descricao = '".pg_escape_string($dados["descricao"])."',
                             url = '".pg_escape_string($dados["url"])."',
                             site = '".pg_escape_string($dados["site"])."',
                             email = '".pg_escape_string($dados["email"])."',
                             telefone = '".pg_escape_string($dados["telefone"])."',                                                       
                             endereco = '".pg_escape_string($dados["endereco"])."',
                             instagram = '".pg_escape_string($dados["instagram"])."',                            
                             facebook = '".pg_escape_string($dados["facebook"])."',                            
                             twitter = '".pg_escape_string($dados["twitter"])."',                            
                             status = '".$dados["status"]."',                            
                             tags = '".pg_escape_string($dados["tags"])."',                       
                             titulo_banner = '".pg_escape_string($dados["titulo_banner"])."',                   
                             descricao_banner = '".pg_escape_string($dados["descricao_banner"])."'                       
            where id_empresa = ". $dados['id_empresa'];


  // echo $sql;
  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  
  $conn = null;

  try 
  {
    echo json_encode($dados);
  }catch (PDOException $ex) {
    echo json_encode($ex);
  }    
});

$app->get('/empresa', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getQueryParams();

  $sql = " Select * from ec_empresa WHERE id_empresa = " . $dados["id_empresa"];	

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetch(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});

$app->get('/empresa_url', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getQueryParams();

  $sql = " Select * from ec_empresa WHERE url = '" . $dados["url"] . "'";	

  $bd = new db();
  $conn = $bd->connect();
  $resultado = $conn->query($sql);
  $data = $resultado->fetch(PDO::FETCH_ASSOC);	
  $conn = null;

  echo json_encode($data);

});

$app->post('/empresa/categoria', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getParsedBody();

  try {

    $bd = new db();
    $conn = $bd->connect();
        
    foreach ($dados["categorias"] as $categoia) {
      if ($categoia["delete"] == "S" && isset($categoia["id_empresa_categoria"]))
      {
        $sql = " delete from ec_empresa_categoria where id_empresa_categoria = ".$categoia["id_empresa_categoria"];
  
        $resultado = $conn->query($sql);
      }else if (!isset($categoia["id_empresa_categoria"])){
        $sql = " insert into ec_empresa_categoria (id_empresa, id_categoria) VALUES 
        ( ". $dados["id_empresa"].", ".$categoia["id_categoria"].")";
  
        $resultado = $conn->query($sql);
      }
      
    }
    
    echo json_encode($dados["categorias"]);	

  } catch (Exception $ex) {
    echo json_encode($ex);
  }
  
});

$app->post('/empresa/servico', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getParsedBody();

  try {

    $bd = new db();
    $conn = $bd->connect();
        
    foreach ($dados["servicos"] as $servicos) {
      if ($servicos["delete"] == "S" && isset($servicos["id_servico"]))
      {
        $sql = " delete from ec_servico where id_servico = ".$servicos["id_servico"];
  
        $resultado = $conn->query($sql);
      }else if (!isset($servicos["id_servico"])){
        $sql = " insert into ec_servico (id_empresa, nome, descricao) VALUES 
        ( ". $dados["id_empresa"].", '". pg_escape_string($servicos["nome"]) ."', '". pg_escape_string($servicos["descricao"]) ."')";
  
        $resultado = $conn->query($sql);
      }
    }
    
    echo json_encode($dados["servicos"]);	

  } catch (Exception $ex) {
    echo json_encode($ex);
  }
  
});

$app->post('/empresa/destaque', function (Request $request, Response $response, array $args) {  
  
  $dados = $request->getParsedBody();

  try {

    $bd = new db();
    $conn = $bd->connect();
        
    foreach ($dados["destaques"] as $destaques) {
      if ($destaques["delete"] == "S" && isset($destaques["id_destaque"]))
      {
        $sql = " delete from ec_destaque where id_destaque = ".$destaques["id_destaque"];
  
        $resultado = $conn->query($sql);
      }else if (!isset($destaques["id_destaque"])){
        $sql = " insert into ec_destaque (id_empresa, titulo, descricao) VALUES 
        ( ". $dados["id_empresa"].", '". pg_escape_string($destaques["titulo"]) ."', '". pg_escape_string($destaques["descricao"]) ."')";
  
        $resultado = $conn->query($sql);
      }

     
    }
    
    echo json_encode($dados["destaques"]);	

  } catch (Exception $ex) {
    echo json_encode($ex);
  }
  
});

