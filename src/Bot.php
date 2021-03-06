<?php

/*
 * Init a Bot
 */

class Bot {

    private $REQUEST;
    private $STATE = 0;

    function __construct() {
        $this->REQUEST = new Request();
        $this->executeCommand();
        //$this->giphyApi();
    }

    function executeCommand() {
        if ($this->REQUEST->text === "/start") {
            $mex = "Salve, sono SearchGIF! Trova le GIF scrivendomi un messaggio! (es. prova a scrivere \"funny cat\")";
            $this->apiSendMessage($mex);
        } elseif ($this->REQUEST->text === "/stop") {
            $mex = "Arrivederci!";
            $this->apiSendMessage($mex);
        } elseif (strpos($this->REQUEST->text, "/search") === 0) {
            //$this->apiSendMessage("sto cercando...");
            $qs = urlencode(substr($this->REQUEST->text, 8));
            $this->giphyApi($qs);
        } else {
            $qs = urlencode($this->REQUEST->text);
            $this->giphyApi($qs);
        }
    }

    function apiSendMessage($text, $params = array()) {
        $params += array(
            'chat_id' => $this->REQUEST->chatId,
            'text' => $text,
        );
        header("Content-Type: application/json");
        $params["method"] = "sendMessage";
        echo json_encode($params);
    }

    function giphyApi($qs) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.giphy.com/v1/gifs/random?tag=$qs&api_key=dc6zaTOxFJmzC");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resp = trim(curl_exec($ch));
        curl_close($ch);

        if (curl_errno($ch)) {
            $this->apiSendMessage(curl_errno($ch));
        } else {
            $mex = "";
            $result = json_decode($resp);
            //var_dump($result->data);
            if ($result->data->url) {
                $mex .= $result->data->url . " \n\n";
            } else {
                $mex = 'Nessuna GIF trovata con quel criterio di ricerca :( Prova con altre parole chiavi!';
            }
            $this->apiSendMessage($mex);
        }
    }

}
