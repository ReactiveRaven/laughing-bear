<?php

namespace RRaven\Bundle\HWIOAuthOverrideBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use HWI\Bundle\OAuthBundle\HWIOAuthBundle;

class RRavenHWIOAuthOverrideBundle extends HWIOAuthBundle
{
  
    public function getParent()
    {
        return 'HWIOAuthBundle';
    }
    
}
