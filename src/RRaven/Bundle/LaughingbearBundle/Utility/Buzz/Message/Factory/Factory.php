<?php

namespace RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Factory;

use Buzz\Message\Factory\Factory as BaseFactory;
use RRaven\Bundle\LaughingbearBundle\Utility\Buzz\Message\Response\Response;

class Factory extends BaseFactory
{
    public function createResponse()
    {
        return new Response();
    }
}
