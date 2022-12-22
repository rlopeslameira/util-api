<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

function getUrl(){
    if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
    return $protocol . "://" . $_SERVER['HTTP_HOST'] . "/";
}

$app->get('/qrcode', function (Request $request, Response $response, array $args) { 
    $params = $request->getQueryParams();

    $output = uniqid();    
    $size = 100;
    $margin = 5;

    if (!isset($params["text"])){
        
        $result["error"] = "Informe o texto para ser gerado o QRCode";
        echo json_encode($result);

    }else{
        $text = $params["text"];
        if (isset($params["size"]))
        {
            $size = $params["size"] > 10 ? $params["size"] : $size;
        }

        if (isset($params["margin"]))
        {
            $margin = $params["margin"] > 1 ? $params["margin"] : $margin;
        }

        if (isset($params["output"]))
        {
            $output = $params["output"] != "" ? $params["output"] : $output;
        }
        $output = 'files/qrcode/'.$output.'.png';

        QRcode::png($text, $output, $size, $margin);

        $result["url"] = getUrl().$output;

        echo json_encode($result);
    }
});
?>