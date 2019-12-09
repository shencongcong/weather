<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/21
 * Time: 10:11
 */

namespace Shencongcong\Ldap;

use Shencongcong\Ldap\Contracts\GatewayInterface;
use Shencongcong\Ldap\Contracts\MessageInterface;
use Shencongcong\Ldap\Contracts\StrategyInterface;
use Shencongcong\Ldap\Exceptions\InvalidArgumentException;
use Shencongcong\Ldap\Strategies\OrderStrategy;
use Shencongcong\Ldap\Support\Config;

class Ldap
{
    protected $config;

    protected $defaultGateway;

    protected $customCreators = [];

    protected $strategies = [];

    protected $gateways = [];

    protected $messenger;

    public function __construct(array $config)
    {
        $this->config = new Config($config);
        if(!empty($config['default'])){
            $this->setDefaultGateway($config['default']);
        }

    }

    public function ldapCheck($username,$password, array $gateways = [])
    {
        $message = $this->formatMessage();
        $gateways = empty($gateways)?$message->getGateways():$gateways;
        if(empty($gateways)){
            $gateways = $this->config->get('default.gateways',[]);
        }

        return $this->getMessenger()->ldapCheck($username,$password,$this->formatGateways($gateways));
    }

    public function userSearch($username,array $gateways = [])
    {
        $message = $this->formatMessage();
        $gateways = empty($gateways)?$message->getGateways():$gateways;
        if(empty($gateways)){
            $gateways = $this->config->get('default.gateways',[]);
        }

        return $this->getMessenger()->userSearch($username,$this->formatGateways($gateways));
    }

    public function emailSearch($email,array $gateways = [])
    {
        $message = $this->formatMessage();
        $gateways = empty($gateways)?$message->getGateways():$gateways;
        if(empty($gateways)){
            $gateways = $this->config->get('default.gateways',[]);
        }

        return $this->getMessenger()->emailSearch($email,$this->formatGateways($gateways));
    }

    public function getMessenger()
    {
        return $this->messenger ?: $this->messenger = new Messenger($this);
    }

    public function setDefaultGateway($name)
    {
        $this->defaultGateway = $name;

        return $this;
    }

    public function getDefaultGateWay()
    {
        if(empty($this->defaultGateway)){
            throw new \RuntimeException("No default gateway configured.");
        }

        return $this->defaultGateway;
    }

    public function gateway($name = null)
    {
        $name = $name ?: $this->getDefaultGateway();

        if (!isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->createGateway($name);
        }

        return $this->gateways[$name];
    }

    protected function createGateway($name)
    {
        if (isset($this->customCreators[$name])) {
            $gateway = $this->callCustomCreator($name);
        } else {
            $className = $this->formatGatewayClassName($name);
            $gateway = $this->makeGateway($className, $this->config->get("gateways.{$name}", []));
        }

        if (!($gateway instanceof GatewayInterface)) {
            throw new InvalidArgumentException(\sprintf('Gateway "%s" must implement interface %s.', $name, GatewayInterface::class));
        }

        return $gateway;
    }

    protected function makeGateway($gateway, $config)
    {
        if (!\class_exists($gateway) || !\in_array(GatewayInterface::class, \class_implements($gateway))) {
            throw new InvalidArgumentException(\sprintf('Class "%s" is a invalid easy-sms gateway.', $gateway));
        }
        return new $gateway($config);
    }

    protected function formatGatewayClassName($name)
    {
        if (\class_exists($name) && \in_array(GatewayInterface::class, \class_implements($name))) {
            return $name;
        }

        $name = \ucfirst(\str_replace(['-', '_', ''], '', $name));

        return __NAMESPACE__."\\Gateways\\{$name}Gateway";
    }

    protected function callCustomCreator($gateway)
    {
        return \call_user_func($this->customCreators[$gateway], $this->config->get("gateways.{$gateway}", []));
    }

    protected function formatMessage()
    {
        $message = new Message();

        return $message;
    }

    protected function formatGateways(array $gateways)
    {
        $formatted = [];

        foreach ($gateways as $gateway => $setting) {
            if (\is_int($gateway) && \is_string($setting)) {
                $gateway = $setting;
                $setting = [];
            }

            $formatted[$gateway] = $setting;
            $globalSettings = $this->config->get("gateways.{$gateway}", []);

            if (\is_string($gateway) && !empty($globalSettings) && \is_array($setting)) {
                $formatted[$gateway] = new Config(\array_merge($globalSettings, $setting));
            }
        }

        $result = [];
        foreach ($this->strategy()->apply($formatted) as $name) {
            $result[$name] = $formatted[$name];
        }

        return $result;
    }

    public function strategy($strategy = null)
    {
        if (\is_null($strategy)) {
            $strategy = $this->config->get('default.strategy', OrderStrategy::class);
        }

        if (!\class_exists($strategy)) {
            $strategy = __NAMESPACE__.'\Strategies\\'.\ucfirst($strategy);
        }

        if (!\class_exists($strategy)) {
            throw new InvalidArgumentException("Unsupported strategy \"{$strategy}\"");
        }
        if (empty($this->strategies[$strategy]) || !($this->strategies[$strategy] instanceof StrategyInterface)) {
            $this->strategies[$strategy] = new $strategy($this);
        }

        return $this->strategies[$strategy];
    }


}