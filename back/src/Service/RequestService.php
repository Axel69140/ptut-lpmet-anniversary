<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class RequestService
{
    public function extractBodyFormParameters(Request $request)
    {
        $body = $request->getContent();
        
        $parameters = [];
        foreach (explode('&', $body) as $chunk) {
            $param = explode("=", $chunk);
            try{
                $parameters[urldecode($param[0])] = urldecode($param[1]);
            }catch(\Exception $e){
                $parameters[urldecode($param[0])] = null;
            }
        }

        return $parameters;
    }
}