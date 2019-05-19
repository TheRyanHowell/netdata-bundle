<?php

declare(strict_types=1);

namespace TheRyanHowell\NetdataBundle\Exceptions;

use TheRyanHowell\NetdataBundle\Exceptions\NetdataError;

class AuthenticationError extends NetdataError
{
    protected $message = 'Unable to authenticate to netdata server.';
    protected $code = 401;
}
