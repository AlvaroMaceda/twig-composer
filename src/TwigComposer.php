<?php
namespace TwigComposer;
use \Nekoo\EventEmitter as EventEmitter;

class TwigComposer extends \Twig_Template
{
    use EventEmitter;

    public function render(array $context)
    {
        $this->emit($this->getTemplateName(), $context);
        return parent::render($context);
    }


    // Never called: it's overriden in child-generated classes and
    // the override method does not call parent's
    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
    }

    // Never called: it's overriden in child-generated classes and
    // the override method does not call parent's
    /**
     * @codeCoverageIgnore
     */
    protected function doDisplay(array $context, array $blocks = array())
    {
    }
}