<?php

namespace Oryzone\Bundle\ImageResizerBundle\Image;

use Imagine\ImagineInterface;

class ImagesResizer
{

	/**
	* The imagine instance
	* 
	* @var ImagineInterface $imagine
	*/
	protected $imagine;
	
	/**
	* The temporary folder where images will be stored
	*
	* @var string $tempFolder
	*/
	protected $tempFolder;

	protected $formats;

	protected $defaultFormats;
	
	/**
	* Constructor
	*  
	* @param ImagineInterface 	$imagine 		the imagine instance to use
	* @param string 			$tempFolder 	The temporary folder where images will be stored
	*/ 
	public function __construct(ImagineInterface $imagine, $tempFolder, $defaultFormats = array())
	{
		$this->imagine = $imagine;
		$this->tempFolder = $tempFolder;
		$this->defaultFormats = $defaultFormats;
		$this->loadDefaultFormats();
	}

	public function clearFormats()
	{
		$this->formats = array();
		return $this;
	}

	public function setFormats($formats)
	{
		$this->formats = $formats;
		return $this;
	}

	public function getFormats()
	{
		return $this->formats;
	}

	public function addFormat(ImageFormat $format)
	{
		$this->formats[] = $format;
		return $this;
	}

	public function loadDefaultFormats()
	{
		$this->formats = $this->defaultFormats;
	}

	public function generate($file)
	{
		$generated = array();
		foreach($this->formats as $format)
		{
			if( ($result = $this->processFormat($file, $format)) )
				$generated[$format->getName()] = $result;
		}

		return $generated;
	}

	protected function processFormat($file, ImageFormat $format)
	{

	}
	
}