<?php

namespace Oryzone\Bundle\ThumbnailsGeneratorBundle\Image;

use Imagine\ImagineInterface;

class ThumbnailsGenerator
{
	
	/**
	* The imagine instance
	* 
	* @var ImagineInterface $imagine
	*/
	protected $imagine;
	
	/**
	* The temporary folder where thumbnails will be stored
	*
	* @var string $tempFolder
	*/
	protected $tempFolder;
	
	/**
	* Constructor
	*  
	* @param ImagineInterface 	$imagine 		the imagine instance to use
	* @param string 			$tempFolder 	The temporary folder where thumbnails will be stored
	*/ 
	public function __construct(ImagineInterface $imagine, $tempFolder)
	{
		$this->imagine = $imagine;
		$this->tempFolder = $tempFolder;
	}
	
}