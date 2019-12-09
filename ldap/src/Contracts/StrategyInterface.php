<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/21
 * Time: 14:43
 */

namespace Shencongcong\Ldap\Contracts;

/**
 * Interface StrategyInterface
 *
 * @package Shencongcong\Ldap\Contracts
 */
Interface StrategyInterface
{

    /**
     * @param array $gateways
     *
     * @author danielshen
     * @datetime   2019-08-21 14:45
     * @return mixed
     */
    public function apply(array $gateways);
}