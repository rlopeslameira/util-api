<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *"); 

require 'PHPMailerAutoload.php';
require 'class.phpmailer.php';

header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);

$mailer = new PHPMailer();

$mailer->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

$nome     = strip_tags($dados['nome']);
$email    = strip_tags($dados['email']);
$to    = strip_tags($dados['to']);
$assunto  = strip_tags($dados['assunto']);
$mensagem  = strip_tags($dados['mensagem']);

$message = "Formul&aacute;rio de Contato: <br/><br/>";
$message .= "Nome: $nome <br/>";
$message .= "E-mail: $email <br/>";
$message .= "Assunto: $assunto <br/>";
$message .= "Mensagem: $mensagem <br/>";    

$mailer->CharSet = 'UTF-8';
$mailer->IsSMTP();
$mailer->Host = 'smtp.edificiocomercial.com'; // localhost
$mailer->SMTPAuth = true; // Enable SMTP authentication
$mailer->isHTML(true); // Set email format to HTML
$mailer->Port = 587;

$mailer->Username = 'mail@edificiocomercial.com'; // SMTP username
$mailer->Password = 'Pu582xy?'; // SMTP password
// email do destinatario

$mailer->AddAddress($to, $to);
$mailer->From = 'mail@edificiocomercial.com';
$mailer->Sender = 'mail@edificiocomercial.com';
$mailer->FromName = "Ed. Comercial"; 
$mailer->AddBCC('contato@edificiocomercial.com');
// assunto da mensagem
$mailer->Subject = $assunto;
// corpo da mensagem
$mailer->MsgHTML($message);

if(!$mailer->Send()){
    echo json_encode(Array(
        "status" => false,
        "message" => $mailer->ErrorInfo        
    ));
} else {
    echo json_encode(Array(
        "status" => true,
        "message" => "Mensagem enviada com sucesso!"
    ));
}

?>

