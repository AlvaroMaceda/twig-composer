<?php
namespace TwigComposerTests;

use TwigComposer\TwigComposer;

class TwigComposerTest extends \PHPUnit_Framework_TestCase
{
    protected $twig;

    public function setUp()
    {
        parent::setUp();

        $loader1 = new \Twig_Loader_Array(array(
            'base' => '{% block content %}{% endblock %}',
        ));
        $loader2 = new \Twig_Loader_Array(array(
            'index' => '{% extends "base.html" %}{% block content %}Hello {{ name }}{% endblock %}',
            'base'  => 'Will never be loaded',
        ));

        $loader = new \Twig_Loader_Chain(array($loader1, $loader2));

        // Twig as renderer
        $this->twig = new \Twig_Environment(
            $loader,
            array(
                'debug' => true,
                'base_template_class' => 'TwigComposer\TwigComposer',
                'cache' => 'tests/twig_tmp',
            ));
    }

    public function testTrueIsTrue()
    {
        $foo = true;
        $this->assertTrue($foo);
    }

    public function testClassInstantiation()
    {
        $instance = new TwigComposer($this->twig);
        $this->assertNotNull($instance);
    }

    public function testTemplateRenders()
    {
        $this->twig->render('base');
    }
}
