<?php
/**
 * -------------------------
 * curl common http request
 * -------------------------
 *
 * Desc: curl common get http request、post request
 *
 * User: liangqi
 * Date: 16/9/4
 * Time: 下午7:06
 */

namespace WebUtil;


class CURLUtil
{
    /**
     * GET HTTP Request By Curl
     *
     * @param $url
     * @return mixed
     */
    public static function get($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $r = curl_exec($curl);
        curl_close($curl);
        return $r;
    }

    /**
     * post request by default format (x-www-form-urlencoded)
     *
     * @param $url
     * @param $data
     * @return mixed
     */
    public static function post($url, $data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        $r = curl_exec($curl);
        curl_close($curl);
        return $r;
    }

}