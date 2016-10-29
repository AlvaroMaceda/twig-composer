<?php
namespace TwigComposer;

use \Nekoo\EventEmitter as EventEmitter;

class TwigComposer extends \Twig_Template
{
    use EventEmitter;

    protected static $global_notifier;

    static function getNotifier()
    {
        if(is_null(static::$global_notifier)) static::$global_notifier = new Relayer();
        return static::$global_notifier;
    }

    /**
     * Constructor.
     *
     * @param Twig_Environment $env A Twig_Environment instance
     */
    public function __construct(\Twig_Environment $env)
    {
        parent::__construct($env);
        $this->env = $env;

        $this->on($this->getTemplateName(),[static::getNotifier(),'relay']);
    }

    protected function emitRenderingEvent($context)
    {
        $event = $this->getTemplateName();
        $this->emit($event, $this->getTemplateName(), $context);
    }

    public function render(array $context)
    {
        $this->emitRenderingEvent($context);
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