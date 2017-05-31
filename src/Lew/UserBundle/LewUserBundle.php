<?php

namespace Lew\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LewUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
