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

    // TODO: Delete after all tests pass
    public function render(array $context)
    {
        return parent::render($context);
    }

    // We need to extract the index from classname
    // Example class name with index:
    // '__TwigTemplate_cb5720da3b9950d085d8c7bac9eb9e1bc6959494c261857c9bb0aeee7a4dc287_10992';
    // Example class name withouth index:
    // '__TwigTemplate_cb5720da3b9950d085d8c7bac9eb9e1bc6959494c261857c9bb0aeee7a4dc287_10992';
    protected function getIndex()
    {
        $classname = get_class($this);
        $pattern = '/__TwigTemplate_([^_]*)(.*)/';
        $coincidences = preg_match_all($pattern, $classname, $matches);
        return $coincidences == 0 ? "" : $matches[2][0] ;
    }

    // If class have index, is part of the same template.
    // We should notify only once, on the main template
    protected function isEmbeddedSubclass()
    {
        $index = $this->getIndex();
        return $index != "";
    }

    // We need to override this method, not render()
    // to be sure that extended templates emits events
    public function display(array $context, array $blocks = array())
    {
        if(!$this->isEmbeddedSubclass()) $this->emitRenderingEvent($context);
        parent::display($context,$blocks);
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