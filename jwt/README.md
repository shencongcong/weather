<h1 align="center"> jwt </h1>

<p align="center"> a easy jwt achieve.</p>


## Installing

```shell
$ composer require shencongcong/jwt -vvv
```

## Usage

```php
use Shencongcong\Jwt\Jwt;

$jwt = \Shencongcong\Jwt\Jwt::getInstance('123');

$playLoad = ['user'=>'xxx','exp'=>1564726174];

// 生成token
$token = $jwt->getToken($playLoad);

// 获取负载
$getPlayLoad = $jwt->verifyToken($token);

```


TODO

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/shencongcong/jwt/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/shencongcong/jwt/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT