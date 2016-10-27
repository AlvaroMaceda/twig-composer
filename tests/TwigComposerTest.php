<?php
namespace TwigComposerTests;

use TwigComposer\TwigComposer;

class TwigComposerTest extends \PHPUnit_Framework_TestCase
{
    protected $twig;

    protected function configureTwig()
    {
        $loader1 = new \Twig_Loader_Array(array(
            'base' => '{% block content %}{% endblock %}',
        ));

        $loader2 = new \Twig_Loader_Array(array(
            'index' => '{% extends "base" %}{% block content %}Hello {{ name }}{% endblock %}',
            'base'  => 'Will never be loaded',
        ));

        $loader = new \Twig_Loader_Chain(array($loader1, $loader2));

        $this->twig = new \Twig_Environment(
            $loader,
            array(
                'debug' => true,
                'base_template_class' => 'TwigComposer\TwigComposer',
                'cache' => 'tests/twig_tmp',
            ));
    }

    protected function createObjectWithMockMethods($methods)
    {
        $defaultmethods = ['callBack','anotherCallBack'];
        return $this->getMockBuilder('stdClass')
            ->setMethods($methods ?: $defaultmethods)
            ->getMock();
    }

    public function setUp()
    {
        parent::setUp();

        $this->configureTwig();
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

    /**
     * @param string $template Template to be rendered
     *
     * @dataProvider providerTemplateList
     */
    public function testAMethodIsNotCalledWhenNotConfigured($template)
    {
        $objectWithCallback = $this->createObjectWithMockMethods(['callBack']);

        $objectWithCallback->expects($this->never())
            ->method('callBack');

        $this->twig->render($template);
    }

    /**
     * @param string $template Template to be rendered
     *
     * @dataProvider providerTemplateList
     */
    public function testAMethodIsCalledWhenTemplateRendered($template)
    {
        $objectWithCallback = $this->createObjectWithMockMethods(['callBack']);

        TwigComposer::$callable = [$objectWithCallback,'callBack'];

        $objectWithCallback->expects($this->once())
            ->method('callBack');

        $this->twig->render($template);
    }

    public function testOnlyConfiguredTemplatesExecuteCallback()
    {
        $objectWithCallback = $this->createObjectWithMockMethods(['base_template_callback','index_template_callback']);

        TwigComposer::on('base', [$objectWithCallback,'base_template_callback']);

        $this->twig->render('base');

        $objectWithCallback->expects($this->once())
            ->method('base_template_callback');

        $objectWithCallback->expects($this->never())
            ->method('index_template_callback');

    }

    public function providerTestAMethodIsCalledWhenTemplateRendered()
    {
        return array(
            array('base'),
            array('index')
        );
    }

    public function providerTemplateList()
    {
        return array(
            array('base'),
            array('index')
        );
    }

}
