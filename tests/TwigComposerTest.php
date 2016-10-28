<?php
namespace TwigComposerTests;

use TwigComposer\TwigComposer;

class TwigComposerTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURES_DIRECTORY = 'tests/fixtures';

    protected $twig;

    protected function loadRenderedFixture($rendered)
    {
        return file_get_contents(self::FIXTURES_DIRECTORY . DIRECTORY_SEPARATOR . $rendered);
    }

    protected function configureTwig()
    {
        $loader_file = new \Twig_Loader_Filesystem(self::FIXTURES_DIRECTORY);

        $loader_memory = new \Twig_Loader_Array(array(
            'index' => '{% extends "base.twig" %}{% block content %}Hello {{ name }}{% endblock %}',
        ));

        // The last have least priority in case of same name
        $loader = new \Twig_Loader_Chain(array($loader_file, $loader_memory));

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

    function tearDown()
    {
        parent::tearDown();

        $this->twig = null;
    }

    public function testClassInstantiation()
    {
        $instance = new TwigComposer($this->twig);
        $this->assertNotNull($instance);
    }

    /**
     * @param string $template Template to be rendered
     *
     * @dataProvider providerTemplateRenders
     */
    public function testTemplateRenders($template,$expected)
    {
        $rendered = $this->twig->render($template);
        $expected = $this->loadRenderedFixture($expected);
        $this->assertEquals($expected,$rendered);
    }

    public function providerTemplateRenders()
    {
        return array(
            array('base.twig','base.rendered.1'),
            array('block.twig','block.rendered.1'),
        );
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
            array('base.twig'),
            array('index')
        );
    }

}
