<?php
namespace TwigComposer;


class TwigComposer extends \Twig_Template
{
//    public function render(array $context)
//    {
//        //vendor/laravel/framework/src/Illuminate/Events/Dispatcher.php tiene un dispatcher
//        $debug = var_export($context, true);
//        $trace = '<h1>OLA K ASE</h1>'.$debug."<br>".get_class($this)."<br>".$this->getTemplateName();
//        $trace .= "<pre>".var_export($this->env->getLoader(), true)."</pre>";
//        $trace .= "<pre>".var_export($this->env->getLoader()->getPaths('test1'), true)."</pre>";
//        return str_replace(
//            "</body>",
//            $trace."\n</body>",
//            parent::render($context)
//        );
//    }


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