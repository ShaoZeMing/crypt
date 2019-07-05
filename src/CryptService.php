<?php


namespace ShaoZeMing\Crypt;

use ShaoZeMing\Crypt\Exceptions\CryptException;

/**
 * 公共签名/验签、加密、解密类
 * Class CryptService
 * User: ZeMing Shao
 * Email: szm19920426@gmail.com
 * @package App\Services\Library
 */
class CryptService
{


    /**
     * The encryption key.
     *
     * @var string
     */
    protected $key;

    /**
     * The algorithm used for encryption.
     *
     * @var string
     */
    protected $cipher;


    /**
     * CryptService constructor.
     * @param array $config
     * @throws CryptException
     */
    public function __construct(array $config = [])
    {
        $config = $config ?: include(__DIR__ . '/../config/crypt.php');

        if (!isset($config['app_secret'])) {
            throw new CryptException(5018);
        }
        $this->key = $config['app_secret'];
        $this->cipher = isset($config['cipher']) ? $config['cipher'] : 'AES-256-CBC';

    }

    /**
     * 签名
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param array $parameter
     * @param null $appSecret
     * @return string
     */
    public function sign(array $parameter, $appSecret = null)
    {
        $appSecret = is_null($appSecret) ? $this->key : $appSecret;

        return Signature::sign($parameter, $appSecret);
    }


    /**
     * 效验签名
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param array $parameter
     * @param string $sign
     * @param null $appSecret
     * @return bool
     */
    public function signCheck(array $parameter,string $sign, $appSecret = null)
    {

        return (self::sign($parameter, $appSecret)) == $sign;
    }


    /**
     * 加密
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $parameter
     * @param null $appSecret
     * @return string
     * @throws CryptException
     */
    public function encrypt(array $parameter, $appSecret = null)
    {

        $appSecret = is_null($appSecret) ? $this->key : $appSecret;

        $encrypt = new Encrypter($appSecret, $this->cipher);
        $payload = $encrypt->encrypt($parameter);
        return $payload;

    }


    /**
     * 解密
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param string $payload
     * @param null $appSecret
     * @return mixed|string
     * @throws CryptException
     */
    public  function decrypt(string $payload, $appSecret = null)
    {

        $appSecret = is_null($appSecret) ? $this->key : $appSecret;
        $encrypt = new Encrypter($appSecret, $this->cipher);
        $data = $encrypt->decrypt($payload);

        return $data;

    }




}
