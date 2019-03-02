<?php

namespace MockServer\Client\Initialize;


use MockServer\Client\MockServerClient;

interface ExpectationInitializer
{
    public function initializeExpectations(MockServerClient $mockServerClient);
}