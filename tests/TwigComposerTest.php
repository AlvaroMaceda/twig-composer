<?php
namespace TwigComposerTests;

use TwigComposer\TwigComposer;

// Used for testing function callbacks
function callbackFunction($context) {
    return TwigComposerTest::$functions->callbackFunction($context);
}

class foo
{
    public function tee($context)
    {
        var_dump($context);
    }
}

class TwigComposerTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURES_DIRECTORY = 'tests/fixtures';

    protected $twig;
    protected $callbackFunctionStub;
    protected $objectWithACoupleOfMethodsStub;
    const STUB_OBJECT_FUNCTIONS = [
        'method1',
        'method2'
    ];

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

    protected function createStubObjectWithACoupleOfMethods($methods)
    {
        return $this->getMockBuilder('stdClass')
                     ->setMethods($methods)
                     ->getMock();
    }

    protected function callbackFunction($context)
    {
        $this->callbackFunctionStub->callbackFunction();
    }

    public function setUp()
    {
        parent::setUp();

        $this->configureTwig();
        $this->callbackFunctionStub = $this->createStubObjectWithACoupleOfMethods(['callbackFunction']);
        $this->objectWithACoupleOfMethodsStub = $this->createStubObjectWithACoupleOfMethods(self::STUB_OBJECT_FUNCTIONS);
    }

    function tearDown()
    {
        parent::tearDown();

        $this->twig = null;
        $this->objectWithACoupleOfMethodsStub = null;
    }

    public function test_Class_Instantiates()
    {
        $instance = new TwigComposer($this->twig);
        $this->assertNotNull($instance);
    }

    /**
     * @param string $template Template to be rendered
     * @param string $expected Expected rendered results
     *
     * @dataProvider provider_Templates_Renders_As_Expected
     */
    public function test_Template_Renders_As_Expected($template, $expected)
    {
        $rendered = $this->twig->render($template);
        $expected = $this->loadRenderedFixture($expected);
        $this->assertEquals($expected,$rendered);
    }

    public function provider_Templates_Renders_As_Expected()
    {
        return array(
            array('base.twig','base.rendered.1'),
            array('block.twig','block.rendered.1'),
        );
    }

    /**
     * @param string $template Template to be rendered
     * @param string/array $callable Callable to be executed
     *
     * @dataProvider providerTemplateList
     */
    public function test_A_Callable_Method_Is_Called_When_Template_Renders($template)
    {
        $stub = $this->createStubObjectWithACoupleOfMethods(['method1','method2']);

        TwigComposer::getNotifier()->on($template, [$stub,'method1']);

        $stub->expects($this->once())
            ->method('method1');

        $stub->expects($this->never())
            ->method('method2');

        $this->twig->render($template);
    }

    public function providerTemplateList()
    {
        return array(
            array('base.twig'),
            array('block.twig')
        );
    }

}
