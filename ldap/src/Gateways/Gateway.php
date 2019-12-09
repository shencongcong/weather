<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/21
 * Time: 15:43
 */

namespace Shencongcong\Ldap\Gateways;

use Shencongcong\Ldap\Contracts\GatewayInterface;
use Shencongcong\Ldap\Support\Config;

abstract class Gateway implements GatewayInterface
{
    const DEFAULT_TIMEOUT = 5.0;

    protected $config;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * Gateway constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    /**
     * Return timeout.
     *
     * @return int|mixed
     */
    public function getTimeout()
    {
        return $this->timeout ?: $this->config->get('timeout', self::DEFAULT_TIMEOUT);
    }

    /**
     * Set timeout.
     *
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = floatval($timeout);

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \strtolower(str_replace([__NAMESPACE__.'\\', 'Gateway'], '', \get_class($this)));
    }
}