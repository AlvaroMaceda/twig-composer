<?php
namespace TwigComposer;


class TwigComposer extends \Twig_Template
{

    public static $callable;

    public function render(array $context)
    {
        $callable = self::$callable;
        if($callable) call_user_func($callable);
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