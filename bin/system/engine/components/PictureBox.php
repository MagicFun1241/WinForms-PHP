<?php

namespace VoidEngine;

class PictureBox extends Control
{
	protected $image;
	
	public function __construct (Control $parent = null)
	{
        parent::__construct ($parent, self::class);
        
		$this->image = new PictureBoxImage ($this->selector);
	}
}

class PictureBoxImage
{
    protected $selector;
    protected $pictureBoxSelector;
    protected $clipboard;
    
	public function __construct (int $pictureBoxSelector)
	{
		$this->pictureBoxSelector = $pictureBoxSelector;
		$this->selector			  = VoidEngine::getProperty ($pictureBoxSelector, 'Image');
		$this->clipboard		  = new WFClass ('System.Windows.Forms.Clipboard');
	}
	
	public function loadFromFile (string $file)
	{
        VoidEngine::setProperty ($this->pictureBoxSelector, 'Image', (new Image ())->loadFromFile (text ($file))->selector);
	}
	
	public function saveToFile (string $file)
	{
		VoidEngine::callMethod ($this->selector, 'Save', text ($file));
	}
	
	public function loadFromClipboard ()
	{
		VoidEngine::setProperty ($this->pictureBoxSelector, 'Image', $this->clipboard->getImage ());
	}
	
	public function saveToClipboard ()
	{
		$this->clipboard->setImage (VoidEngine::getProperty ($this->pictureBoxSelector, 'Image'));
	}
}

?>
