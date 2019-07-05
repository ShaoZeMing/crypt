<?php

namespace ShaoZeMing\Crypt\Exceptions;
/**
 *  SmsException.php
 *
 * @author szm19920426@gmail.com
 * $Id: SmsException.php 2017-08-16 上午11:05 $
 */
class CryptException extends \Exception
{
    protected static $errorMsgs = [
        5010 =>'签名失败',
        5011 =>'验签失败',
        5012 =>'加密失败',
        5013 =>"解密失败",
        5014 =>"Could not encrypt the data",
        5015 =>"Could not decrypt the data.",
        5016 =>"The payload is invalid.",
        5017 =>"The MAC is invalid.",
        5018 =>"配置文件缺少app_secret配置.",
    ];
    public function __construct($code)
    {
        parent::__construct(self::$errorMsgs[$code], $code);
    }
}
