<?php
namespace ShaoZeMing\Crypt;


/**
 * 公共签名/验签
 * Class CryptService
 * User: ZeMing Shao
 * Email: szm19920426@gmail.com
 * @package App\Services\Library
 */
class Signature
{


    /**
     *
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param array $parameter
     * @param $appSecret
     * @return string
     */
    public static function sign(array $parameter, $appSecret)
    {

        self::arrayToString($parameter);
        ksort($parameter);
        $sign = md5(json_encode($parameter, true) . $appSecret);
        return $sign;

    }


    /**
     * 参数递归装换为字符串进行加密
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param array $array
     * @return array
     */
    protected static function arrayToString(array & $array)
    {

        $array = array_filter($array, function ($item) {
            return !empty($item);
        });

        $array = array_map(function (&$item) {
            if (is_array($item)) {
                return self::arrayToString($item);
            }
            return strval($item);
        }, $array);

        return $array;
    }

    /**
     * 效验签名
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param array $parameter
     * @param $sign
     * @param $appSecret
     * @return bool
     */
    public static function signCheck(array $parameter, $sign, $appSecret)
    {

        return (self::sign($parameter, $appSecret)) == $sign;
    }


}
