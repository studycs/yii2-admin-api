<?php
namespace app\handlers;
use GuzzleHttp\Client;
/**
 *
 */
class GoogleSms
{
    public $header = [];
    public $params = [];

    public function __construct($params){
        $this->params = $params;
        $this->setHeader();
    }

    public function getSid(){
        $cookie = explode(';',$this->params['cookie']);
        foreach($cookie as $value){
            $val = explode('=',$value);
            if(count($val)==2){
                [$key,$value] = $val;
                if(trim($key)=='SAPISID') return $value;
            }
        }
        return '';
    }

    public function setHeader(){
        $dateUtc = time();
        $sapiSid = $this->getSid();
        $origin = "https://voice.google.com";
        $sidHash = sha1("{$dateUtc} {$sapiSid} {$origin}");
        $this->header['accept'] = '*/*';
        $this->header['accept-language'] = 'en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7';
        $this->header['cache-control'] = 'no-cache';
        $this->header['content-type'] = 'application/json+protobuf';
        $this->header['origin'] = 'https://clients6.google.com';
        $this->header['pragma'] = 'no-cache';
        $this->header['sec-ch-ua'] = '" Not A;Brand";v="99", "Chromium";v="102", "Google Chrome";v="102"';
        $this->header['sec-ch-ua-mobile'] = '?0';
        $this->header['sec-fetch-dest'] = 'empty';
        $this->header['sec-fetch-mode'] = 'cors';
        $this->header['sec-fetch-site'] = 'same-origin';
        $this->header['user-agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36';
        $this->header['x-goog-authuser'] = '0';
        $this->header['x-goog-encode-response-if-executable'] = 'base64';
        $this->header['x-javascript-user-agent'] = 'google-api-javascript-client/1.1.0';
        $this->header['x-origin'] = 'https://voice.google.com';
        $this->header['x-referer'] = 'https://voice.google.com';
        $this->header['x-requested-with'] = 'XMLHttpRequest';
        $this->header['authorization'] = "SAPISIDHASH {$dateUtc}_{$sidHash}";
        $this->header['cookie'] = $this->params['cookie'];
    }

    public function setImage($url=null){
        if(file_exists($url)) $this->params['image'] = $url;
    }

    /**
     * @return string
     */
    public function getImagePutUrl(){
        $dateUtc = intval(microtime(true) * 1000);
        $imageInfo = pathinfo($this->params['image']);
        $imageData = file_get_contents($this->params['image']);
        $url = 'https://docs.google.com/upload/photos/resumable?authuser=0';
        $dataTpl = '{
    "protocolVersion": "0.8",
    "createSessionRequest": {
        "fields": [
            {
                "external": {
                    "name": "file",
                    "filename": "%s",
                    "put": {},
                    "size": %d
                }
            },
            {
                "inlined": {
                    "name": "album_mode",
                    "content": "temporary",
                    "contentType": "text/plain"
                }
            },
            {
                "inlined": {
                    "name": "silo_id",
                    "content": "26",
                    "contentType": "text/plain"
                }
            },
            {
                "inlined": {
                    "name": "title",
                    "content": "%s",
                    "contentType": "text/plain"
                }
            },
            {
                "inlined": {
                    "name": "addtime",
                    "content": "%s",
                    "contentType": "text/plain"
                }
            },
            {
                "inlined": {
                    "name": "onepick_host_id",
                    "content": "google-voice",
                    "contentType": "text/plain"
                }
            },
            {
                "inlined": {
                    "name": "onepick_version",
                    "content": "v1",
                    "contentType": "text/plain"
                }
            },
            {
                "inlined": {
                    "name": "batchid",
                    "content": "%s",
                    "contentType": "text/plain"
                }
            },
            {
                "inlined": {
                    "name": "album_abs_position",
                    "content": "0",
                    "contentType": "text/plain"
                }
            },
            {
                "inlined": {
                    "name": "client",
                    "content": "google-voice",
                    "contentType": "text/plain"
                }
            }
        ]
    }
}';
        $data = sprintf($dataTpl, (string)$imageInfo['basename'], strlen($imageData), (string)$imageInfo['basename'], (string)$dateUtc, (string)$dateUtc);
        try{
            $client = new Client(['verify'=>false,'timeout'=>20]);
            $response = $client->request('GET',$url,['body'=>$data,'proxy'=>$this->params['proxy'],'headers'=>$this->header]);
            $result = json_decode($response->getBody(),true);
            return $result['sessionStatus']['externalFieldTransfers'][0]['putInfo']['url'] ?? null;
        }catch(\Throwable $e){
            return null;
        }
    }

    public function uploadImage(){
        $url = $this->getImagePutUrl();
        if(!empty($url)){
            try{
                $images = file_get_contents($this->params['image']);
                $client = new Client(['verify'=>false,'timeout'=>20]);
                $response = $client->request('POST',$url,['body'=>$images,'proxy'=>$this->params['proxy'],'headers'=>$this->header]);
                $result = json_decode($response->getBody(),true);
                return $result['sessionStatus']['additionalInfo']['uploader_service.GoogleRupioAdditionalInfo']['completionInfo']['customerSpecificInfo']['url'] ?? null;
            }catch(\Throwable $e){
                return null;
            }
        }
        return null;
    }

    public function sendImage(){
        $body = [null,null,null,null,'','',[],null,[]];
        $imageUrl = $this->uploadImage();
        if(!empty($imageUrl)){
            $body[9] = [2, null, null, $imageUrl];
            $number = $this->params['number'];
            $body[6][] = $number;
            $data = json_encode($body,JSON_UNESCAPED_UNICODE);
            $url = 'https://clients6.google.com/voice/v1/voiceclient/api2thread/sendsms?alt=protojson&key=' . $this->params['token'];
            try{
                $client = new Client(['verify'=>false,'timeout'=>20]);
                $response = $client->request("POST",$url,['body'=>$data,'proxy'=>$this->params['proxy'],'headers'=>$this->header]);
                $result = json_decode($response->getBody(),true);
                if($response->getStatusCode()==200 && !empty($result)){
                    return ['message'=>'图片消息发送成功！','result'=>(string)$response->getBody()];
                }else{
                    return ['message'=>'图片消息发送失败！','result'=>(string)$response->getBody()];
                }
            }catch(\Throwable $e){
                return ['message'=>$e->getMessage(),'result'=>null];
            }
        }else{
            return ['message'=>'图片上传失败！','result'=>null];
        }
    }

    public function sendText(){
        $body = [null, null, null, null, '', '', [], null, []];
        $body[4] = $this->params['text'];
        $number = $this->params['number'];
        $body[6][] = $number;
        $data = json_encode($body, JSON_UNESCAPED_UNICODE);
        $url = 'https://clients6.google.com/voice/v1/voiceclient/api2thread/sendsms?alt=protojson&key=' . $this->params['token'];
        try{
            $client = new Client(['verify'=>false,'timeout'=>20]);
            $response = $client->request("POST",$url,['body'=>$data,'proxy'=>$this->params['proxy'],'headers'=>$this->header]);
            $result = json_decode($response->getBody(),true);
            if($response->getStatusCode()==200 && !empty($result)){
                return ['message'=>'文字消息发送成功！','result'=>(string)$response->getBody()];
            }else{
                return ['message'=>'文字消息发送失败！','result'=>(string)$response->getBody()];
            }
        }catch(\Throwable $e){
            return ['message'=>$e->getMessage(),'result'=>null];
        }
    }
}