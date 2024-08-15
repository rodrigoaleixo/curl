<?php

namespace  RodrigoAleixo\Curl;

class Curl
{
    public $url;
    public $cookie;
    public $typeCookie;
    public $file;
    public $typePathFile;

    public function setUrl($url) {
        $this->url = $url;
    }
    public function setFile($file) {
        $this->file = $file;
    }
    public function setTypePathFile($type)
    {
        $this->typePathFile = $type;
    }
    public function setTypeCookie($cookie){
        $this->typeCookie = $cookie;
    }
    public function setCookie($name)
    {
        $this->cookie = $name;
    }
    public function openUrl()
    {

        $request = curl_init();
        curl_setopt ($request, CURLOPT_URL, $this->url); // Request URL.
        curl_setopt ($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($request, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt ($request, CURLOPT_FOLLOWLOCATION, true);

        if($this->typeCookie =="POST"){
            curl_setopt ($request, CURLOPT_COOKIEJAR, $this->cookie); // Armazena os cookies no arquivo
            curl_setopt ($request, CURLOPT_COOKIEFILE, $this->cookie); // Usa os cookies armazenados
        }elseif($this->typeCookie =="GET"){
            curl_setopt ($request, CURLOPT_COOKIEFILE, $this->cookie); // Usa os cookies armazenados
        }
        curl_setopt($request, CURLOPT_USERAGENT, 'Mozilla/5.0');

        $file_contents = curl_exec($request);
        //$html = html_entity_decode(trim($file_contents));
        //$html = mb_convert_encoding($html, "UTF-8", "auto");
        curl_close($request);
        return $file_contents;
    }
    public function loadPdf($response)
    {
        file_put_contents($this->file, $response);


        if($this->typePathFile=="Download"){
            // Define os cabeçalhos para forçar o download
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="arquivo.pdf"');
            header('Content-Length: ' . filesize($this->file));
            // Envia o arquivo para o navegador do usuário
            readfile($this->file);
            // Remove o arquivo temporário
            unlink($this->file);
        }
    }

}
