<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/21
 * Time: 14:46
 */

namespace Shencongcong\Ldap\Strategies;

use Shencongcong\Ldap\Contracts\StrategyInterface;

class RandomStrategy implements StrategyInterface
{
    public function apply(array $gateways)
    {
        uasort($gateways,function(){
           return mt_rand() - mt_rand();
        });

        return array_keys($gateways);
    }
}