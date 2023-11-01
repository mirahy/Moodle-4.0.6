<?php

require_once('config.php');
// require_once('conexao-web-service.php');
require_once('sigecad-consulta.php');
require_once($CFG->libdir . '/adminlib.php');
global $CFG, $COURSE;



//require_login();

// function consultaDadosCartao($documento) {
//     $numCartao = conectaWebService("usuario-cartao", $documento);
//     return $numCartao ? $numCartao : $documento;
// }

function consultaDadosCartao($documento) {
    $numCartao = cosultaCartao($documento); 
    return $numCartao ? $numCartao : $documento;
}

$syscontext = context_system::instance();

$ISBNLivro   = required_param('isbn', PARAM_TEXT);
$firstName   = optional_param('firstname',NULL, PARAM_TEXT);
$lastName   = optional_param('lastname',NULL, PARAM_TEXT);
$userName   = optional_param('idnumber',NULL, PARAM_TEXT);

$bodytag = str_replace(".", "", $userName);
$bodytag = str_replace("-", "", $bodytag);

// antes da atualização do minha biblioteca
//$email = 'MB'.$bodytag;

// depois da atualização
$email = consultaDadosCartao($bodytag);

if($email == '04327208124'){
    $email = '2021001360';
}
echo '<br>Número do Cartão: '. $email.'<br>';
//echo $ISBNLivro.'<br>';
//echo '<pre>';

//die();

$service_url = 'https://digitallibrary.zbra.com.br/DigitalLibraryIntegrationService/AuthenticatedUrl';
$curl = curl_init($service_url);

if($ISBNLivro=='0'){
	$curl_post_data = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<CreateAuthenticatedUrlRequest
xmlns=\"http://dli.zbra.com.br\"
xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"
xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
<FirstName>$firstName</FirstName>
<LastName>$lastName</LastName>
<Email>$email</Email>
<CourseId xsi:nil=\"true\"/>
<Tag xsi:nil=\"true\"/>
</CreateAuthenticatedUrlRequest>
";

}else{
$curl_post_data = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
<CreateAuthenticatedUrlRequest
xmlns=\"http://dli.zbra.com.br\"
xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"
xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
<FirstName>$firstName</FirstName>
<LastName>$lastName</LastName>
<Email>$email</Email>
<CourseId xsi:nil=\"true\"/>
<Tag xsi:nil=\"true\"/>
<Isbn>$ISBNLivro</Isbn>
</CreateAuthenticatedUrlRequest>
";
}


$content_size = strlen($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
 "Content-Type: application/xml; charset=utf-8",
 "Host: digitallibrary.zbra.com.br",
 "Content-Length: $content_size",
 "Expect: 100-continue",
 "Accept-Encoding: gzip, deflate",
 "Connection: Keep-Alive",
 "X-DigitalLibraryIntegration-API-Key:da9918f1-9b25-4e7a-8272-7b81adc1785b")
);

curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);

if ($curl_response === false) {
 	echo curl_error($curl);
 	curl_close($curl);
 	die();
}

curl_close($curl);
$xml = new SimpleXMLElement($curl_response);
/*
if ($xml->Success != 'true') {
//    if ($xml->Success != 'false') {
 	echo htmlspecialchars($result);
  	echo '<br><br><br><center><font size="5" face="verdana" color="red">Seu login <b>n&atilde;o</b> tem acesso a Minha Biblioteca!</font></center>';
  	echo '<br><center><font size="4" face="verdana" color="green">Entre em contato com o setor de TI da EaD e envie seu login para facilitar o atendimento: <b>'.$USER->username.'</b></font></center>';
    echo '<br><center><font size="5" face="verdana" color="green">E-mail:<b> ti.ead@ufgd.edu.br</b></font></center>';
 	die();
}
*/

if ($xml->Success != 'true') {
  //  if ($xml->Success != 'false') {
//    echo '<br><br><center><font size="4" face="verdana" color="black">'.htmlspecialchars($result).'</font></center>';
    //echo '<br><br><br><center><font size="5" face="verdana" color="red">Seu login <b>n&atilde;o</b> tem acesso a Minha Biblioteca!</font></center>';
  //  echo '<br><center><font size="4" face="verdana" color="black">Entre em contato com Biblioteca: Envie uma foto 3x4 (formato .jpeg), nome completo e CPF para biblioteca.atendimento@ufgd.edu.br <b>solicitando ativação do cartão.</b></font></center>';
  //  echo '<br><center><font size="4" face="verdana" color="red">É necessário imprimir ou salvar o cartão em pdf depois de ativado.</font></center>';
   // echo '<br><center><font size="4" face="verdana" color="red">login: Número do cartão da biblioteca</font></center>';
   // echo '<br><center><font size="4" face="verdana" color="red">Senha: CPF(somente números)</font></center>';
   // echo '<br><center><font size="4" face="verdana" color="black">Link da plataforma: <a href="https://sophia.ufgd.edu.br/Terminal/">https://sophia.ufgd.edu.br/Terminal/</a> </font></center>';
    //echo '<br><center><font size="4" face="verdana" color="black">Consulte o artigo de como acessar os livros na plataforma: <a href="https://files.ufgd.edu.br/arquivos/arquivos/78/BIBLIOTECA/Tutoriais/COMO%20ACESSAR%20E%20LOGAR%20NO%20SISTEMA%20SOPHIA.pdf">Ajuda sophia UFGD</a> </font></center>';
    echo '<br><center><font size="4" face="verdana" color="black">Verifique se seu cartão UFGD tem foto e se está liberado para impressão no sistema de cartão: <a href="https://drive.google.com/file/d/1CLgjVxUz7vuAlN5G1jf17OocYy051PY3/view?usp=sharing">TUTORIAL DE COMO SOLICITAR E IMPRIMIR O CARTÃO DA UFGD</a></font></center>';

    echo '<br><center><font size="4" face="verdana" color="black">Consulte o artigo de como acessar os livros na plataforma: <a href="https://files.ufgd.edu.br/arquivos/arquivos/78/BIBLIOTECA/Tutoriais/COMO%20ACESSAR%20E%20LOGAR%20NO%20SISTEMA%20SOPHIA.pdf">Ajuda sophia UFGD</a> </font></center>';

    echo '<br><center><font size="4" face="verdana" color="black">Consulte a página de Tutoriais da Biblioteca: <a href="https://www.ufgd.edu.br/setor/biblioteca/tutoriais">https://www.ufgd.edu.br/setor/biblioteca/tutoriais</a> </font></center>';
    die();
}

//print_r($xml->AuthenticatedUrl);
redirect($xml->AuthenticatedUrl);

//echo $xml->AuthenticatedUrl;
//echo '<script type="text/javascript">window.open(\''.$xml->AuthenticatedUrl.'\');</script>';
//die();
 

?>
