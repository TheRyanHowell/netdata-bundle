<?php

declare(strict_types=1);

namespace TheRyanHowell\NetdataBundle\Exceptions;

class NetdataError extends \Exception
{
    protected $message = 'Generic netdata error.';
    protected $code = 500;
}
