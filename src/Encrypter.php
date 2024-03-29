<?php

namespace ShaoZeMing\Crypt;

use RuntimeException;
use ShaoZeMing\Crypt\Exceptions\CryptException;

class Encrypter
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
     * Create a new encrypter instance.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return void
     *
     * @throws \RuntimeException
     */
    public function __construct($key, $cipher = 'AES-128-CBC')
    {
        $key = (string) $key;

        if (static::supported($key, $cipher)) {
            $this->key = $key;
            $this->cipher = $cipher;
        } else {
            throw new RuntimeException('The only supported ciphers are AES-128-CBC and AES-256-CBC with the correct key lengths.');
        }
    }

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return bool
     */
    public static function supported($key, $cipher)
    {
        $length = mb_strlen($key, '8bit');

        return ($cipher === 'AES-128-CBC' && $length === 16) ||
               ($cipher === 'AES-256-CBC' && $length === 32);
    }

    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $cipher
     * @return string
     * @throws \Exception
     */
    public static function generateKey($cipher)
    {
        return random_bytes($cipher === 'AES-128-CBC' ? 16 : 32);
    }

    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $value
     * @param bool $serialize
     * @return string
     * @throws CryptException
     * @throws \Exception
     */
    public function encrypt($value, $serialize = true)
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));

        // First we will encrypt the value using OpenSSL. After this is encrypted we
        // will proceed to calculating a MAC for the encrypted value so that this
        // value can be verified later as not having been changed by the users.
        $value = \openssl_encrypt(
            $serialize ? serialize($value) : $value,
            $this->cipher, $this->key, 0, $iv
        );

        if ($value === false) {
            throw new CryptException(5014);
        }

        // Once we get the encrypted value we'll go ahead and base64_encode the input
        // vector and create the MAC for the encrypted value so we can then verify
        // its authenticity. Then, we'll JSON the data into the "payload" array.
        $mac = $this->hash($iv = base64_encode($iv), $value);

        $json = json_encode(compact('iv', 'value', 'mac'));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new CryptException(5014);
        }

        return base64_encode($json);
    }

    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $value
     * @return string
     * @throws CryptException
     */
    public function encryptString($value)
    {
        return $this->encrypt($value, false);
    }

    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $payload
     * @param bool $unserialize
     * @return mixed|string
     * @throws CryptException
     */
    public function decrypt($payload, $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
        $decrypted = \openssl_decrypt(
            $payload['value'], $this->cipher, $this->key, 0, $iv
        );

        if ($decrypted === false) {
            throw new CryptException(5015);
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $payload
     * @return mixed|string
     * @throws CryptException
     */
    public function decryptString($payload)
    {
        return $this->decrypt($payload, false);
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string  $iv
     * @param  mixed  $value
     * @return string
     */
    protected function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv.$value, $this->key);
    }

    /**
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $payload
     * @return mixed
     * @throws CryptException
     */
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (! $this->validPayload($payload)) {
            throw new CryptException(5016);
        }

        if (! $this->validMac($payload)) {
            throw new CryptException(5017);
        }

        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $payload
     * @return bool
     */
    protected function validPayload($payload)
    {
        return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']) &&
               strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length($this->cipher);
    }

    /**
     *  Determine if the MAC for the given payload is valid.
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param array $payload
     * @return bool
     * @throws \Exception
     */
    protected function validMac(array $payload)
    {
        $calculated = $this->calculateMac($payload, $bytes = random_bytes(16));

        return hash_equals(
            hash_hmac('sha256', $payload['mac'], $bytes, true), $calculated
        );
    }

    /**
     * Calculate the hash of the given payload.
     * User: ZeMing Shao
     * Email: szm19920426@gmail.com
     * @param $payload
     * @param $bytes
     * @return string
     */
    protected function calculateMac($payload, $bytes)
    {
        return hash_hmac(
            'sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true
        );
    }

    /**
     * Get the encryption key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
