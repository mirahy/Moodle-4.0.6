<?php

function conectaWebService ($contexto) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, getenv('LINK_SUPORTE')."/$contexto");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
}

?>

