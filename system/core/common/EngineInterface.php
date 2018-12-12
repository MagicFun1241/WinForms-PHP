<?php

/*
    Класс-интерфейс VoidEngine'а
    Отвечает за работу с WinForms-ядром со стороны PHP (.NET Framework)
*/

namespace VoidEngine;

class VoidEngine
{
    static function createObject (WFObject $object, ...$args)
    {
        return winforms_objectcreate ($object->getResourceLine (), ...$args);
    }

    static function removeObject (int ...$selectors)
    {
        winforms_objectdelete (...$selectors);
    }

    static function buildObject (WFObject $object)
    {
        return winforms_objectget ($object->getResourceLine ());
    }

    static function loadModule (string $path)
    {
        $assembly = self::buildObject (new WFObject ('System.Reflection.Assembly', 'mscorlib'));
        
        return self::callMethod ($assembly, 'LoadFrom', 'object', $path, 'string'); 
    }

    static function getProperty (int $selector, string $propertyName, string $type = 'string')
    {
        if ($type == 'auto')
            throw new \Exception ('getProperty function can\'t read "auto" type');

        return winforms_getprop ($selector, $propertyName, $type);
    }

    static function setProperty (int $selector, string $propertyName, $value, string $type = 'auto')
    {
        if ($type == 'auto')
            $type = getLogicalVarType ($value);

        winforms_setprop ($selector, $propertyName, $value, $type);
    }

    static function callMethod (int $selector, string $methodName, string $type = 'string', ...$args)
    {
        if ($type == 'auto')
            throw new \Exception ('callMethod function can\'t read "auto" type');

        /*return (is_array ($args) && sizeof ($args) > 0) ? 
            winforms_callmethod ($selector, $methodName, $type, $args) :
            winforms_callmethod ($selector, $methodName, $type);*/

        return winforms_callmethod ($selector, $methodName, $type, ...$args);
    }

    static function getArrayValue (int $selector, int $index, string $type = 'string')
    {
        if ($type == 'auto')
            throw new \Exception ('getArrayValue function can\'t read "auto" type');

        return winforms_getindex ($selector, $index, $type);
    }

    static function setArrayValue (int $selector, int $index, $value, string $type = 'auto')
    {
        if ($type == 'auto')
            $type = getLogicalVarType ($value);

        winforms_setindex ($selector, $index, $value, $type);
    }

    static function setObjectEvent (int $selector, string $eventName, string $code = '')
    {
        winforms_setevent ($selector, $eventName, $code);
    }

    static function removeEvent (int $selector, string $eventName)
    {
        winforms_delevent ($selector, $eventName);
    }
}

class WFObject
{
    public $version  = '4.0.0.0';
    public $culture  = 'neutral';
    public $token    = 'b77a5c561934e089';
    public $postArgs = [];

    public $className;
    public $classGroup;

    public $onlyClassInfo;

    public function __construct (string $className, string $classGroup = 'System.Windows.Forms', bool $onlyClassInfo = false)
    {
        $this->className     = $className; // System.Windows.Forms.Application
        $this->classGroup    = $classGroup; // System.Windows.Forms
        $this->onlyClassInfo = $onlyClassInfo;
    }

    public function getResourceLine ()
    {
        if ($this->onlyClassInfo)
            return ($this->classGroup) ?
                $this->className .', '. $this->classGroup :
                $this->className;

        $postArgs = '';

        if (isset ($this->postArgs) && is_array ($this->postArgs))
            foreach ($this->postArgs as $name => $value)    
                $postArgs .= ", $name=$value";

        return $this->className .', '. $this->classGroup .', Version='. $this->version .', Culture='. $this->culture .', PublicKeyToken='. $this->token .$postArgs;
    }

    public function __get ($name)
    {
        return (isset ($this->$name) ? $this->$name : false);
    }

    public function __set ($name, $value)
    {
        if (isset ($this->$name))
            $this->$name = $value;
    }
}

?>
