<?php

add_action('wp_ajax_nopriv_tema_contacto', 'ajax_tema_contacto');
add_action('wp_ajax_tema_contacto', 'ajax_tema_contacto');

function ajax_tema_contacto()
{

    header('Content-Type: application/json');
    
    $data = array(
            'secret' => "",
            'response' => $_REQUEST['g-recaptcha-response']
    );

    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($verify));

    if($response->success == '1'){


    $destinatario = '';

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth   = true; 
    $mail->SMTPSecure = "tls"; 
    $mail->Port       = 587;
    $mail->Host       = "email-smtp.us-east-1.amazonaws.com";
    $mail->Username   = "";
    $mail->Password   = "";

    $mail->SetFrom('', utf8_decode('Nuevo contacto')); //from (verified email address)
    $mail->Subject = "Nuevo contacto"; //subject

    $nombre         = $_REQUEST['nombre'];
    $edad         = $_REQUEST['edad'];
    $email         = $_REQUEST['email'];
    $mensaje        = $_REQUEST['mensaje'];

    //message
    $body  = "<h1>Nuevo contacto</h1>";
    $body .= "<hr>";
    $body .= "<p><strong>Nombre:</strong> " . $nombre . "</p>";
    $body .= "<p><strong>Email:</strong> " . $email . "</p>";
    $body .= "<p><strong>Edad:</strong> " . $edad . "</p>";
    $body .= "<p><strong>Mensaje:</strong><br/>" . $mensaje . "</p>";

    $mail->MsgHTML($body);
    $mail->AddAddress($destinatario, "Interno");
    $mail->addCC('');

    if ($mail->Send()) {  }
    
    echo json_encode(array("exito" => "exito", "message" => "Mensaje enviado correctamente, pronto nos pondremos en contacto con usted."));
}else{
    echo json_encode(array("exito" => "error", "message" => "SPAM")); 
}

exit;
}