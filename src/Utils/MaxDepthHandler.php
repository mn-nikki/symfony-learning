<?php

namespace App\Utils;

class MaxDepthHandler
{
    public function __invoke(object $innerObject, object $outerObject, string $attributeName, string $format = null, array $context = [])
    {
        return '/link/' . $innerObject->getId();
    }
}
