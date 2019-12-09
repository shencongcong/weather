<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/1
 * Time: 15:15
 */

namespace Shencongcong\Jwt\Exceptions;

class PayloadException extends JWTException
{
    protected $statusCode = 500;
}