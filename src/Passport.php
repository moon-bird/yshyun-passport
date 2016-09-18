<?php

/**
 * 融云登录
 * User: xiaopeng.wang
 * Date: 2016/6/22
 * Time: 16:36
 */

namespace YshyunPassport;

class Passport
{
    const HTTP_CODE_OK = 200;

    public $isLogin = false;
    public $userId = '';
    public $nickName = '';
    public $avatar = '';

    private $_guzzleClient = null;

    public function __construct(){

        $client = $this->_getGuzzleClient();
        $cookies = $_COOKIE;
        $setCookieArr = [];
        foreach($cookies as $key=>$value){

            $setCookie = new \GuzzleHttp\Cookie\SetCookie([
                'Name'=>$key,
                'Value'=>$value,
                'Domain'=>'.yunshangyun.com'
            ]);
            $setCookieArr[] = $setCookie;
        }
        try{

            $jar = new \GuzzleHttp\Cookie\CookieJar(true, $setCookieArr);
            $response = $client->request('GET', 'http://passport.yunshangyun.com/api/initLogin.json', [
                'cookies' => $jar
            ]);
            $responseJson = $response->getBody()->getContents();
            $responseJsonObj = json_decode($responseJson);


            if ($responseJsonObj->head->code == self::HTTP_CODE_OK && $responseJsonObj->body->isLogin == true){

                $this->isLogin = true;
                $this->userId = $responseJsonObj->body->userId;
                $this->nickName = $responseJsonObj->body->nickName;
                $this->avatar = $responseJsonObj->body->avatar;
            }
        } catch (Exception $e){}
    }

    protected function _getGuzzleClient(){

        if (null == $this->_guzzleClient){

            $options = [
                'timeout' => 3,
                'verify' => false
            ];

            $this->_guzzleClient = new \GuzzleHttp\Client($options);

        }
        return $this->_guzzleClient;
    }
}