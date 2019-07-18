# Translate for PHP

---
[![](https://travis-ci.org/ShaoZeMing/crypt.svg?branch=master)](https://travis-ci.org/ShaoZeMing/crypt) 
[![](https://img.shields.io/packagist/v/ShaoZeMing/crypt.svg)](https://packagist.org/packages/shaozeming/crypt) 
[![](https://img.shields.io/packagist/dt/ShaoZeMing/crypt.svg)](https://packagist.org/packages/stichoza/shaozeming/crypt)


## Installing

```shell
$ composer require shaozeming/crypt -v
```

### configuration 

```php
// config/crypt.php

   
    /**
     * 本项目的app_secret
     */
    'app_secret' =>'12345678912345678912345678912312',

    /**
     * 加密规则,支持AES-128-CBC，AES-256-CBC
     */
    'cipher' => 'AES-256-CBC',

```


## Usage


Example:


```php
use ShaoZeMing\Crypt\CryptService;

$config = include($youerpath.'/crypt.php');

$obj = new CryptService($config);

$data = ['test'=>123];
$sign = $obj->sign($data);   //签名
print_r($sign);
$check = $obj->signCheck($data,$sign);   //延签
print_r($check);

$payload =  $obj->encrypt($data);  //加密
print_r($payload);
$data = $obj->decrypt($payload);   //解密
print_r($data);

```



## License

MIT

