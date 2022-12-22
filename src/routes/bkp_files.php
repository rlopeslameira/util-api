<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->post('/upload', function (Request $request, Response $response, array $args) {
    
    try{

        $server_url = 'https://maisescolaweb.com.br/edificiocomercial-api/files/';
        $response = array();
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/edificiocomercial-api/files" . "/" ;
    
        $uploadedFiles = $request->getUploadedFiles();       
        $uploadedFile = $uploadedFiles['file'];
        $origin_name = $uploadedFile->getClientFilename();
        $files_info = pathinfo($origin_name);
    
        if (!file_exists($upload_dir))
            mkdir($upload_dir, 0777, true);
    
        if (isset($files_info['extension']))
            $files_ext = $files_info['extension'];
    
        $random_name = rand(1,1999999)."-".rand(1,1999999).'.'.$files_ext;
        $upload_name = $upload_dir . $random_name;
            
        if (is_uploaded_file($uploadedFile->file))
        {
            if(move_uploaded_file($uploadedFile->file , $upload_name)) {
                               
                $response = array(
                    "status" => "success",
                    "error" => false,
                    "name" => $random_name,
                    "ext" => $files_ext,
                    "message" => "File uploaded successfully",
                    "location" => $server_url.$random_name,
                    "uri" => $server_url.$random_name,
                    "root" => $_SERVER['DOCUMENT_ROOT']
                );
            }else
            {
            $response = array(
                "status" => "error",
                "error" => true,
                "message" => "Error with upload.",
                "path" => $upload_name,
                "root" => $_SERVER['DOCUMENT_ROOT']
            );
            }
        }else
        {
            $error = "";
            switch($uploadedFile->getError()){
            case 0: //no error; possible file attack!
                $error = "Ocorreu um problema com o seu upload.";
                break;
            case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
                $error = "O arquivo que você está tentando enviar é muito grande.";
                break;
            case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                $error = "O arquivo que você está tentando enviar é muito grande.";
                break;
            case 3: //uploaded file was only partially uploaded
                $error = "O arquivo que você está tentando carregar foi carregado apenas parcialmente.";
                break;
            case 4: //no file was uploaded
                $error = "Você deve selecionar uma imagem para upload.";
                break;
            default: //a default error, just in case!  :)
            $error = "Ocorreu um problema com o seu upload.";
                break;
            }
        
            $response = array(
            "error" => true,
            "message" => $error,
            );
        }
  
      echo json_encode($response);
    }
    catch (Exception $e) {
        echo 'Exceção capturada: '.  $e->getMessage() ."\n";
    }    
});

$app->post('/upload/save', function (Request $request, Response $response, array $args) {
    $dados = $request->getParsedBody();
    
    $bd = new db();
    $conn = $bd->connect();
    $sql = "";
    if ($dados["area"] == '2') //2 = servicos 
        $sql = "update ec_servico set imagem = '".$dados["uri"]."' where id_servico = ".$dados["id"];
    else if ($dados["area"] == '3') //2 = destaques
        $sql = "update ec_destaque set imagem = '".$dados["uri"]."' where id_destaque = ".$dados["id"];
    else
        $sql = "update ec_destaque set imagem = '".$dados["uri"]."' where id_destaque = ".$dados["id"];

    $resultado = $conn->query($sql);
    
    $conn = null;

    echo json_encode($dados);
});

$app->get('/imagem', function (Request $request, Response $response, array $args) {
    $dados = $request->getQueryParams();
    
    $bd = new db();
    $conn = $bd->connect();
    $sql = "select * from ec_imagem where id_empresa = ".$dados["id_empresa"];
    $resultado = $conn->query($sql);
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);	
    $conn = null;
  
    echo json_encode($data);
});

$app->post('/imagem', function (Request $request, Response $response, array $args) {
    $dados = $request->getParsedBody();
    
    $bd = new db();
    $conn = $bd->connect();
    $sql = "insert into ec_imagem (arquivo, area, id_empresa) values ('".$dados["arquivo"]."', ".$dados["area"]. ", ".$dados["id_empresa"].");";
    $resultado = $conn->query($sql);
    
    $conn = null;

    echo json_encode($dados);
});

$app->post('/imagem/delete', function (Request $request, Response $response, array $args) {
    $dados = $request->getParsedBody();
    
    $bd = new db();
    $conn = $bd->connect();
    $sql = "delete from ec_imagem where id_imagem = ".$dados["id_imagem"];
    $resultado = $conn->query($sql);
    
    $conn = null;

    echo json_encode($dados);
});

?>