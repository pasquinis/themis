<?php

namespace Themis\Controller;

class HelloController
{
    public function show($name)
    {
        return "Hello {$name}";
    }
}
