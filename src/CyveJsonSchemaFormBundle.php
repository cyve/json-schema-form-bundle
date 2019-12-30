<?php

namespace Cyve\JsonSchemaFormBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CyveJsonSchemaFormBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
