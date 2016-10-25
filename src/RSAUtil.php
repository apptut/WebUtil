<?php
namespace WebUtil;

/**
 * ------------------
 *   rsa加密解密
 * ------------------
 *
 * User: liangqi
 * Date: 16/8/31
 * Time: 下午7:07
 */
class RSAUtil
{
    const KEY_LEN_1024 = 1024;

    /**
     * 公钥加密
     *
     * @param $content
     * @param $public_key
     * @return null|string
     */
    public static function encodeByPublic($content, $public_key)
    {
        $encrypted = '';
        $res = openssl_get_publickey(self::formatPubKey($public_key));

        $base64Content = base64_encode($content);
        $total = strlen($base64Content);
        $maxContentLength = self::KEY_LEN_1024 / 8 - 11;
        if ($total > $maxContentLength) {
            $encodeArr = [];
            $step = ceil($total / $maxContentLength);
            for ($i = 0; $i < $step; $i++) {
                $start = $i * $maxContentLength;
                $end = $total - $start >= $maxContentLength ? $maxContentLength : $total - $start;
                $newStr = substr($base64Content, $start, $end);
                $rel = openssl_public_encrypt($newStr, $encrypted, $res);
                if (!$rel) {
                    return null;
                }
                array_push($encodeArr, base64_encode($encrypted));
            }
            return base64_encode(json_encode($encodeArr));
        } else {
            $rel = openssl_public_encrypt($content, $encrypted, $res);
            if ($rel) {
                return base64_encode($encrypted);
            }
        }

        return null;
    }

    /**
     * 公钥解密
     *
     * @param $content
     * @param $public_key
     * @return bool|null
     */
    public static function decodeByPublic($content, $public_key)
    {
        $decoded = '';
        $decodeString = base64_decode($content);

        if (strpos($decodeString, '[') === 0) {
            $arr = json_decode($decodeString, true);
            if (!$arr || !is_array($arr)) {
                return null;
            }
            foreach ($arr as $item) {
                $fieldDecode = '';
                $res = openssl_get_publickey(self::formatPubKey($public_key));
                $rel = openssl_public_decrypt(base64_decode($item), $fieldDecode, $res);
                if (!$rel) {
                    return null;
                }
                $decoded .= $fieldDecode;
            }
            return base64_decode($decoded);
        } else {
            $res = openssl_get_publickey(self::formatPubKey($public_key));
            $rel = openssl_public_decrypt(base64_decode($content), $decoded, $res);
            if ($rel) {
                return $decoded;
            }
        }
        return null;
    }


    /**
     * 私钥加密
     *
     * @param $content
     * @param $private_key
     * @return null|string
     */
    public static function encodeByPrivate($content, $private_key)
    {
        $encrypted = '';
        $res = openssl_get_privatekey(self::formatPriKey($private_key));
        $base64Content = base64_encode($content);
        $total = strlen($base64Content);
        $maxContentLength = self::KEY_LEN_1024 / 8 - 11;
        if ($total > $maxContentLength) {
            $encodeArr = [];
            $step = ceil($total / $maxContentLength);
            for ($i = 0; $i < $step; $i++) {
                $start = $i * $maxContentLength;
                $end = $total - $start >= $maxContentLength ? $maxContentLength : $total - $start;
                $newStr = substr($base64Content, $start, $end);
                $rel = openssl_private_encrypt($newStr, $encrypted, $res);
                if (!$rel) {
                    return null;
                }
                array_push($encodeArr, base64_encode($encrypted));
            }
            return base64_encode(json_encode($encodeArr));
        } else {
            $rel = openssl_private_encrypt($content, $encrypted, $res);
            if ($rel) {
                return base64_encode($encrypted);
            }
        }
        return null;
    }


    /**
     * 私钥解密
     *
     * @param $content
     * @param $private_key
     * @return null|string
     */
    public static function decodeByPrivate($content, $private_key)
    {
        $decoded = '';
        $decodeString = base64_decode($content);

        if (strpos($decodeString, '[') === 0) {
            $arr = json_decode($decodeString, true);
            if (!$arr || !is_array($arr)) {
                return null;
            }
            foreach ($arr as $item) {
                $fieldDecode = '';
                $res = openssl_get_privatekey(self::formatPriKey($private_key));
                $rel = openssl_private_decrypt(base64_decode($item), $fieldDecode, $res);
                if (!$rel) {
                    return null;
                }
                $decoded .= $fieldDecode;
            }
            return base64_decode($decoded);
        } else {
            $res = openssl_get_privatekey(self::formatPriKey($private_key));
            $rel = openssl_private_decrypt(base64_decode($content), $decoded, $res);
            if ($rel) {
                return $decoded;
            }
        }
        return null;
    }

    /**
     * 格式化公钥
     *
     * @param $pubKey
     * @return string
     */
    private static function formatPubKey($pubKey)
    {
        $fKey = "-----BEGIN PUBLIC KEY-----\n";
        $len = strlen($pubKey);
        for ($i = 0; $i < $len;) {
            $fKey = $fKey . substr($pubKey, $i, 64) . "\n";
            $i += 64;
        }
        $fKey .= "-----END PUBLIC KEY-----";
        return $fKey;
    }


    /**
     * 格式化私钥
     *
     * $priKey PKCS#1格式的私钥串
     * return pem格式私钥， 可以保存为.pem文件
     */
    private static function formatPriKey($priKey)
    {
        $fKey = "-----BEGIN RSA PRIVATE KEY-----\n";
        $len = strlen($priKey);
        for ($i = 0; $i < $len;) {
            $fKey = $fKey . substr($priKey, $i, 64) . "\n";
            $i += 64;
        }
        $fKey .= "-----END RSA PRIVATE KEY-----";
        return $fKey;
    }

}