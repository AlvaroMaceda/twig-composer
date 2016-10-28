<?php
namespace TwigComposer;
use \Nekoo\EventEmitter as EventEmitter;

class EmitterRelayer
{
    use EventEmitter;
}

trait StaticEventEmitter
{
    static private $emitter; // EmitterRelayer();

    protected static function initializeClass()
    {
        self::$emitter = new EmitterRelayer();
    }

    protected static function isClassInitialized()
    {
        return !is_null(self::$emitter);
    }

    protected static function callAMethodOfTheEmitter()
    {
        if(!self::isClassInitialized()) self::initializeClass();

        $args = func_get_args();
        $method = array_shift($args);
        return call_user_func_array([self::$emitter,$method], func_get_args());
    }

    public static function setMaxListeners()
    {
        call_user_func_array([__CLASS__,'callAMethodOfTheEmitter'],array_merge(['setMaxListeners'], func_get_args()));
    }

    public function on($event, callable $handler)
    {
        return self::callAMethodOfTheEmitter('on',$event,$handler);
    }

    public function addListener()
    {
        return call_user_func_array([__CLASS__,'callAMethodOfTheEmitter'],array_merge(['addListener'], func_get_args()));
    }

    public function all(callable $handler) {
        self::callAMethodOfTheEmitter('all',$handler);
    }

    public function once($event, callable $handler) {
        return self::callAMethodOfTheEmitter('once',$event,$handler);
    }

    public function off($event, callable $handler) {
        return self::callAMethodOfTheEmitter('off',$event,$handler);
    }

    public function removeListener() {
        return call_user_func_array([__CLASS__,'callAMethodOfTheEmitter'],array_merge(['removeListener'], func_get_args()));
    }

    public function removeAllListeners($event = null) {
        return self::callAMethodOfTheEmitter('removeAllListeners',$event);
    }

    public function getListeners($event) {
        return self::callAMethodOfTheEmitter('getListeners',$event);
    }
}