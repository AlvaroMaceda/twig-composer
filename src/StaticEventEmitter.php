<?php
namespace TwigComposer;
use \Nekoo\EventEmitter as EventEmitter;

class EmitterRelayer
{
    use EventEmitter;
}

trait StaticEventEmitter
{
    /*
     * Methods and variables not belonging to the trait interface
     * are prefixed wit __se__ to avoid collisions
     */

    static private $__se__emitter; // EmitterRelayer();

    protected static function __se__initializeClass()
    {
        self::$__se__emitter = new EmitterRelayer();
    }

    protected static function __se__isClassInitialized()
    {
        return !is_null(self::$__se__emitter);
    }

    protected static function __se__callAMethodOfTheEmitter()
    {
        /*
         * We need to check if class has been "initialized"
         * each time, because PHP does not have a method to
         * initialize variables in a trait
         */
        if(!self::__se__isClassInitialized()) self::__se__initializeClass();

        $args = func_get_args();
        $method = array_shift($args);
        return call_user_func_array([self::$__se__emitter,$method], $args);
    }

    /* -------------------------------------------------------------------------
     * Proxy Methods
     * ------------------------------------------------------------------------- */

    public static function setMaxListeners($value)
    {
        self::__se__callAMethodOfTheEmitter('setMaxListeners',$value);
    }

    // This function admits variable parameters
    protected static function emit() {
        return call_user_func_array([__CLASS__, '__se__callAMethodOfTheEmitter'],array_merge(['emit'], func_get_args()));
    }

    public static function on($event, callable $handler)
    {
        return self::__se__callAMethodOfTheEmitter('on',$event,$handler);
    }

    // This function admits variable parameters
    public static function addListener()
    {
        return call_user_func_array([__CLASS__, '__se__callAMethodOfTheEmitter'],array_merge(['addListener'], func_get_args()));
    }

    public static function all(callable $handler) {
        self::__se__callAMethodOfTheEmitter('all',$handler);
    }

    public static function once($event, callable $handler) {
        return self::__se__callAMethodOfTheEmitter('once',$event,$handler);
    }

    public static function off($event, callable $handler) {
        return self::__se__callAMethodOfTheEmitter('off',$event,$handler);
    }

    // This function admits variable parameters
    public static function removeListener() {
        return call_user_func_array([__CLASS__, '__se__callAMethodOfTheEmitter'],array_merge(['removeListener'], func_get_args()));
    }

    public static function removeAllListeners($event = null) {
        return self::__se__callAMethodOfTheEmitter('removeAllListeners',$event);
    }

    public static function getListeners($event) {
        return self::__se__callAMethodOfTheEmitter('getListeners',$event);
    }
}