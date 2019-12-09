<h1 align="center"> ldap </h1>

<p align="center"> ldap.</p>


## Installing

```shell
$ composer require shencongcong/ldap ~1.0
```

## 配置

### Laravel 应用

1. 在 `config/app.php` 注册 ServiceProvider

```php
'providers' => [
    // ...
    Shencongcong\Ldap\ServiceProvider::class,
],
```

2. 创建配置文件：

```shell
php artisan vendor:publish --provider="Shencongcong\Ldap\ServiceProvider"
```

3. 修改应用根目录下的 `config/ldap.php` 中对应的参数即可


## 说明
| 网关 | 含义 | 
| :---- | :---- |
| home |内网域控地址  |
| idc |idc机房域控地址 |

## Usage

```php
use Shencongcong\Ldap\Ldap;

$config = [
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Shencongcong\Ldap\Strategies\OrderStrategy::class,
        // 默认可用的发送网关
        'gateways' => [
            'home','idc'
        ],
    ],
    'gateways' => [
        'home' => [
            'url' => 'LDAP://10.0.0.110/',
        ],
        'idc' => [
            'url' => 'LDAP://10.0.0.150/',
        ],
    ],
];

$ldap = new Ldap($config);

1. 验证账号密码
$username = 'xxxx';
$password = 'xxxx';
$gateways = ['home','idc'];
$res = $ldap->ldapCheck($username,$password,$gateways);

2. 根据用户名模糊查找用户
$username = 'xxxx';
$gateways = ['home','idc'];
$res = $ldap->userSearch($username,$gateways);

3. 根据邮箱模糊查找用户
$email = 'xxxx';
$gateways = ['home','idc'];
$res = $ldap->emailSearch($email,$gateways);

```



## License

MIT