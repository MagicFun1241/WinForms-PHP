<?php

/*
    Класс, отвечающий за работу с событиями
*/

namespace VoidEngine;

class Events
{
    static $events = [];

    static function setObjectEvent (Control $object, string $eventName, $function)
    {
        $selector = $object->selector;

        self::$events[$selector][$eventName] = $function;

        VoidEngine::setObjectEvent ($selector, $eventName, "if (isset (VoidEngine\Events::\$events['$selector']['$eventName'])) VoidEngine\Events::\$events['$selector']['$eventName'] (VoidEngine\Components::getComponent ('$selector'), isset (\$args) ? \$args : false);");
    }

    static function removeObjectEvent (Control $object, string $eventName)
    {
        $selector = $object->selector;

        VoidEngine::removeObjectEvent ($selector, $eventName);
        unset (self::$events[$selector][$eventName]);
    }

    static function getObjectEvent (Control $object, string $eventName)
    {
        $selector = $object->selector;

        return self::$events[$selector][$eventName];
    }
}

class EventArgs
{
	protected $selector;
	
    final public function __construct (int $selector)
    {
        $this->selector = $selector;
    }
	
	final public function __get ($name)
	{
		if (method_exists ($this, $method = "get_$name"))
            return $this->$method ();
        
        else return $this->getProperty ($name);
	}
	
    final public function __set ($name, $value)
	{
		if (method_exists ($this, $method = "set_$name"))
            return $this->$method ($value);
        
        else $this->setProperty ($name, $value);
    }
    
    final public function get_selector ()
    {
        return $this->selector;
    }
	
	final protected function getProperty ($name)
    {
        return VoidEngine::getProperty ($this->selector, $name);
    }
	
    final protected function setProperty (string $name, $value)
    {
        VoidEngine::setProperty ($this->selector, $name, $value);
    }
	
}

?>
