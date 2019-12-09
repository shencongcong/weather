<?php
/**
 * Created by PhpStorm.
 * User: danielshen
 * Date: 2019/8/1
 * Time: 15:14
 */

namespace Shencongcong\Jwt\Exceptions;

class TokenExpiredException extends JWTException
{
    protected $statusCode = 401;
}