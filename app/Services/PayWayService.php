<?php

namespace App\Services;



Class PayWayService
{
   public function getApiUrl(){
        return config ('payway.api_url');
   }


    public function getHash($str){
        $key = config ('payway.api_key');
        return base64_decode(hash_hmac('sha512', $str, $key, true));
   }
};

