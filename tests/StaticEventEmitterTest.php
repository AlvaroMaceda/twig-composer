<?php
namespace TwigComposerTests;

use TwigComposer\StaticEventEmitter;

class AClassWithTheTrait
{
    use StaticEventEmitter;
}

class StaticEventEmitterTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    function tearDown()
    {
        parent::tearDown();
    }

    public function testPRUEBA()
    {
        AClassWithTheTrait::setMaxListeners(10);
    }

}