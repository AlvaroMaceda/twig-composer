<?php
namespace TwigComposerTests;

use TwigComposer\StaticEventEmitter;

/*
 *
 * IMPORTANT:
 * PHP 7.0+ is needed to run this tests
 * because anonymous classes are needed (new class...)
 *
 */


class AClassWithTheTrait
{
    use StaticEventEmitter; // This is NOT an error

    static function emitSomething($event,$data)
    {
        self::emit($event,$data);
    }
};

class StaticEventEmitterTest extends \PHPUnit_Framework_TestCase
{
    protected $AClassWithTheTrait;

    protected function createStubObjectWithACoupleOfMethods($methods)
    {
        return $this->getMockBuilder('stdClass')
            ->setMethods($methods)
            ->getMock();
    }

    protected function createAClassWithTheTrait()
    {
        // This does not work: it allways return the same class
        return new class
        {
            use StaticEventEmitter; // This is NOT an error: it works

            static function emitSomething($event,$data)
            {
                static::emit($event,$data);
            }
        };
    }

    public function setUp()
    {
        parent::setUp();

        // We need to create the class dinamically in each test
        // so we can start with a clean state
        $this->AClassWithTheTrait =  $this->createAClassWithTheTrait();
    }

    function tearDown()
    {
        parent::tearDown();
        $this->AClassWithTheTrait = null;
    }

    public function test_It_Can_Registers_Listeners_With_On()
    {
        $stub = $this->createStubObjectWithACoupleOfMethods(['method1','method2']);

        $callables = [
            [$stub,'method1'],
            function(){
            }
        ];

        ($this->AClassWithTheTrait)::on('event',$callables[0]);
        $listeners = ($this->AClassWithTheTrait)::getListeners('event');
        $this->assertEquals($listeners[0],$callables[0]);

        ($this->AClassWithTheTrait)::on('event',$callables[1]);
        $listeners = ($this->AClassWithTheTrait)::getListeners('event');
        $this->assertEquals($listeners,$callables,"\$canonicalize = true"); // canonicalize: Undocumented parameter so it does not check order of elements
    }

    public function test_It_Can_Registers_Listeners_With_addListener()
    {
        $stub = $this->createStubObjectWithACoupleOfMethods(['method1','method2']);

        $callables = [
            [$stub,'method1'],
            function(){
            }
        ];

        ($this->AClassWithTheTrait)::addListener('event',$callables[0]);
        $listeners = ($this->AClassWithTheTrait)::getListeners('event');
        $this->assertEquals($listeners[0],$callables[0]);

        ($this->AClassWithTheTrait)::addListener('event',$callables[1]);
        $listeners = ($this->AClassWithTheTrait)::getListeners('event');
        $this->assertEquals($listeners,$callables,"\$canonicalize = true"); // canonicalize: Undocumented parameter so it does not check order of elements
    }

    public function xtest_It_Can_Remove_Listeners_With_Off()
    {
        // TO-DO
    }

    public function xtest_It_Can_Remove_Listeners_With_removeListener()
    {
        // TO-DO
    }

    public function test_It_Can_Not_Register_More_Listener_Than_MaxListeners()
    {
        ($this->AClassWithTheTrait)::setMaxListeners(3);
        ($this->AClassWithTheTrait)::on('whatever',function(){});
        ($this->AClassWithTheTrait)::on('whatever',function(){});
        ($this->AClassWithTheTrait)::on('otherevent',function(){});
        ($this->AClassWithTheTrait)::on('whatever',function(){});

        $this->expectException ( \Exception :: class ) ;
        ($this->AClassWithTheTrait)::on('whatever',function(){});
    }

    public function xtest_It_Emitts_Events()
    {
        // TO-DO
    }


    public function xtestPRUEBA()
    {
        ($this->AClassWithTheTrait)::setMaxListeners(10);
    }

}