<?php
namespace TwigComposerTests;

use TwigComposer\StaticEventEmitter;

class AClassWithTheTrait
{
    use StaticEventEmitter;

    static function emitSomething($event,$data)
    {
        self::emit($event,$data);
    }
}

class StaticEventEmitterTest extends \PHPUnit_Framework_TestCase
{

    protected function createStubObjectWithACoupleOfMethods($methods)
    {
        return $this->getMockBuilder('stdClass')
            ->setMethods($methods)
            ->getMock();
    }

    public function setUp()
    {
        parent::setUp();
    }

    function tearDown()
    {
        parent::tearDown();
    }

    public function test_Registers_Listeners()
    {
        $stub = $this->createStubObjectWithACoupleOfMethods(['method1','method2']);

        $callables = [
            [$stub,'method1'],
            function(){
            }
        ];

        AClassWithTheTrait::on('event',$callables[0]);
        $listeners = AClassWithTheTrait::getListeners('event');
        $this->assertEquals($listeners[0],$callables[0]);

        AClassWithTheTrait::on('event',$callables[1]);
        $listeners = AClassWithTheTrait::getListeners('event');
        $this->assertEquals($listeners,$callables,"\$canonicalize = true"); // canonicalize: Undocumented parameter so it does not check order of elements
    }

    public function xtest_Emmits_Events()
    {
//        AClassWithTheTrait::

    }

    public function xtestPRUEBA()
    {
        AClassWithTheTrait::setMaxListeners(10);
    }

}