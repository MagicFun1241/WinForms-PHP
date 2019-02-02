<?php

namespace VoidEngine;

class ColorDialog extends CommonDialog
{
    public function __construct ()
    {
        parent::__construct (self::class);
    }
	
    public function get_color ()
    {
        return $this->getProperty (['Color', 'color']);
    }
	
    public function set_color (int $color)
    {
        $this->setProperty ('Color', [$color, 'color']);
    }
	
    public function get_anyColor ()
    { 
        return $this->getProperty ('AnyColor');
    }
	
    public function set_anyColor (bool $anyColor)
    {
        $this->setProperty ('AnyColor', $anyColor);
    }
	
    public function get_fullOpen ()
    {
        return $this->getProperty ('FullOpen');
    }
	
    public function set_fullOpen (bool $fullOpen)
    {
        $this->setProperty ('FullOpen', $fullOpen);
    }
	
    public function get_customColors ()
    {
        return $this->getArrayProperty ('CustomColors', 'int');
    }
}

?>
