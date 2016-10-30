<?php
namespace TwigComposer;

use Nekoo\EventEmitter;

class Relayer
{
    use EventEmitter;

    public function relay($template,$context)
    {
        $this->emit($template,$context);
    }
}