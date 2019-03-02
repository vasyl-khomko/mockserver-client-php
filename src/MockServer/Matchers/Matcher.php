<?php

namespace MockServer\Matchers;


interface Matcher
{
    function matches(HttpRequest $context, $t): bool;
}