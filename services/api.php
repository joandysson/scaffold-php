<?php
namespace Services;

class Api {

    private $headers;
    private $body;


    public function Headers($array = [])
    {
        if(is_array($array)){
            $this->headers = $array;
        }
    }

    public function body($array)
    {
        if(is_array($array)){
            $this->body = $array;
        }
    }

    public function enviar($host, $method)
    {
        $curl = curl_init($host);
        curl_setopt($curl, CURLOPT_URL, $host);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->body);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if($err) {
            return 'Error '.$err;
        }

        curl_close($curl);

        return $response;
    }

}