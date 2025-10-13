<?php

namespace App\Attributes;


use App\Enums\HttpMethod;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class Route
{

    public function __construct(public string $routePath,public HttpMethod $method = HttpMethod::Get)
    {
    }

}