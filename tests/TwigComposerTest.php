<?php
namespace TwigComposerTests;

use TwigComposer\TwigComposer;

// Used for testing function callbacks
function callbackFunction($context) {
    return TwigComposerTest::callbackFunction($context);
}


class TwigComposerTest extends \PHPUnit_Framework_TestCase
{
    const FIXTURES_DIRECTORY = 'tests/fixtures';
    const AN_EXAMPLE_CONTEXT = array(
        'AVariable' => 10,
        'AnotherVariable' => 'Hello',
        'OneMoreVariable' => ['foo','bar']
        );

    protected $twig;
    protected static $callbackFunctionMock;

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

    protected function createMockObjectWithACoupleOfMethods($methods)
    {
        return $this->getMockBuilder('stdClass')
                     ->setMethods($methods)
                     ->getMock();
    }

    public function callbackFunction($context)
    {
        self::$callbackFunctionMock->callbackFunction($context);
    }

    public function setUp()
    {
        parent::setUp();

        $this->configureTwig();
        self::$callbackFunctionMock = $this->createMockObjectWithACoupleOfMethods(['callbackFunction']);
    }

    function tearDown()
    {
        parent::tearDown();

        $this->twig = null;
    }

    public function xtest_Class_Instantiates()
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
    public function test_A_Method_Is_Called_When_Template_Renders($template)
    {
        $mock = $this->createMockObjectWithACoupleOfMethods(['method1','method2']);
        $context = self::AN_EXAMPLE_CONTEXT;

        TwigComposer::getNotifier()->on($template, [$mock,'method1']);

        $mock->expects($this->once())
            ->method('method1')
            ->with($this->equalTo($context));

        $mock->expects($this->never())
            ->method('method2')
            ->with($this->equalTo($context));

        $this->twig->render($template,$context);
    }

    public function providerTemplateList()
    {
        return array(
            array('base.twig'),
            array('block.twig')
        );
    }

    public function test_A_Function_Is_Called_When_Template_Renders()
    {
        $template = 'base.twig';
        $context = self::AN_EXAMPLE_CONTEXT;

        TwigComposer::getNotifier()->on($template, 'TwigComposerTests\callbackFunction');

        self::$callbackFunctionMock->expects($this->once())
            ->method('callbackFunction')
            ->with($this->equalTo($context));

        $this->twig->render($template,$context);
    }

    // include
    public function test_It_Notifies_When_Parent_Is_Rendered_And_We_Are_Listening_On_Included_Template()
    {
        $includes_template = 'includes.twig';
        $included_template = 'included_template.twig';
        $context = self::AN_EXAMPLE_CONTEXT;

        $mock = $this->createMockObjectWithACoupleOfMethods(['method1','method2']);

        $mock->expects($this->once())
            ->method('method1')
            ->with($this->equalTo($context));

        TwigComposer::getNotifier()->on($included_template, [$mock,'method1']);

        $this->twig->render($includes_template,$context);
    }

    // "embed": This one is tricky because Twig uses auxiliar classes with same templatename
    public function test_It_Notifies_Embeding_And_Embedded_Template_Only_Once()
    {
        $embedded_template = 'embedded_template.twig';
        $embeds_template = 'embeds.twig';
        $context = self::AN_EXAMPLE_CONTEXT;

        $mock_embedded = $this->createMockObjectWithACoupleOfMethods(['embedded']);
        $mock_embeds = $this->createMockObjectWithACoupleOfMethods(['embeds']);

        $mock_embedded->expects($this->once())
            ->method('embedded')
            ->with($this->equalTo($context));

        $mock_embeds->expects($this->once())
            ->method('embeds')
            ->with($this->equalTo($context));

        TwigComposer::getNotifier()->on($embedded_template, [$mock_embedded,'embedded']);
        TwigComposer::getNotifier()->on($embeds_template, [$mock_embeds,'embeds']);

        $this->twig->render($embeds_template,$context);
    }

    // "import" is used for macros: it should NOT notify when importing
    public function test_It_Not_Notifies_Imported_Templates()
    {
        $imported_template = 'imported_template.twig';
        $imports_template_all = 'imports_all_macros.twig';
        $imports_template_individually = 'imports_macros_individually.twig';
        $context = self::AN_EXAMPLE_CONTEXT;

        $mock = $this->createMockObjectWithACoupleOfMethods(['imports_all','imports_individually','imported']);

        TwigComposer::getNotifier()->on($imported_template, [$mock,'imported']);
        TwigComposer::getNotifier()->on($imports_template_all, [$mock,'imports_all']);
        TwigComposer::getNotifier()->on($imports_template_individually, [$mock,'imports_individually']);


        $mock->expects($this->once())
            ->method('imports_all')
            ->with($this->equalTo($context));
        $mock->expects($this->once())
            ->method('imports_individually')
            ->with($this->equalTo($context));
        $mock->expects($this->never())
            ->method('imported');

        $this->twig->render($imports_template_individually,$context);

        $this->twig->render($imports_template_all,$context);
    }

    // extends -> {% extends "base.html" %} {% extends "base.html" %}
    public function test_It_Notifies_Extended_And_Extending_Templates_When_Rendering_A_Template_Which_Extends_Another()
    {
        $extended_template = 'extended_template.twig';
        $extends_template = 'extends.twig';
        $context = self::AN_EXAMPLE_CONTEXT;

        $mock_extended = $this->createMockObjectWithACoupleOfMethods(['extended']);
        $mock_extends = $this->createMockObjectWithACoupleOfMethods(['extends']);

        $mock_extended->expects($this->once())
            ->method('extended')
            ->with($this->equalTo($context));

        $mock_extends->expects($this->once())
            ->method('extends')
            ->with($this->equalTo($context));

        TwigComposer::getNotifier()->on($extended_template, [$mock_extended,'extended']);
        TwigComposer::getNotifier()->on($extends_template, [$mock_extends,'extends']);

        $this->twig->render($extends_template,$context);
    }

    // It's possible to render the contents of the parent block by using the parent function.
    public function test_It_Notifies_Only_Once_The_Parent_Template_When_Using_Parent_Function_Multiple_Times()
    {
        $extended_template = 'extended_template.twig';
        $extends_template = 'extends_using_parent.twig';
        $context = self::AN_EXAMPLE_CONTEXT;

        $mock_extended = $this->createMockObjectWithACoupleOfMethods(['extended']);
        $mock_extends = $this->createMockObjectWithACoupleOfMethods(['extends']);

        $mock_extended->expects($this->once())
            ->method('extended')
            ->with($this->equalTo($context));

        $mock_extends->expects($this->once())
            ->method('extends')
            ->with($this->equalTo($context));

        TwigComposer::getNotifier()->on($extended_template, [$mock_extended,'extended']);
        TwigComposer::getNotifier()->on($extends_template, [$mock_extends,'extends']);

        $this->twig->render($extends_template,$context);
    }

}
