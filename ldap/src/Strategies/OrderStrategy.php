<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/21
 * Time: 14:46
 */

namespace Shencongcong\Ldap\Strategies;

use Shencongcong\Ldap\Contracts\StrategyInterface;

class OrderStrategy implements StrategyInterface
{
    public function apply(array $gateways)
    {
        return array_keys($gateways);
    }
}