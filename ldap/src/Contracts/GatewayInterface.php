<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/21
 * Time: 15:42
 */

namespace Shencongcong\Ldap\Contracts;

use Shencongcong\Ldap\Support\Config;

Interface GatewayInterface
{

    public function getName();

    public function ldapCheck($username, $passport, Config $config);

    public function userSearch($username, Config $config);

    public function emailSearch($email, Config $config);
}