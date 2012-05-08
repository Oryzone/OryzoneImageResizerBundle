<?php

namespace Oryzone\Bundle\ImageResizerBundle\Image;

use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface;

class ImageResizer
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
	 * @param array             $defaultFormats An array of formats to be used as default formats
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
		$formats = array();

		foreach($this->defaultFormats as $defaultFormat)
			if($defaultFormat instanceof ImageFormat)
				$formats[] = $defaultFormat;
			else
				$formats[] = ImageFormat::newFromArray($defaultFormat);

		$this->formats = $formats;
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
		/**
		 * @var ImageInterface $image
		 */
		$image = $this->imagine->open( $file );

		list($originalWidth, $originalHeight) = getimagesize($file);
		$width = $format->getWidth();
		$height = $format->getHeight();

		if( $format->getResizeMode() == ImageFormat::RESIZE_MODE_PROPORTIONAL )
		{
			//calculate missing dimension
			if($width === NULL)
				$width = round( $originalWidth * $height / $originalHeight );
			elseif($height === NULL)
				$height = round( $width * $originalHeight / $originalWidth );
		}

		// TODO validate sizes

		$box = new \Imagine\Image\Box($width, $height);

		if($format->getResizeMode() == ImageFormat::RESIZE_MODE_PROPORTIONAL || $format->getResizeMode() == ImageFormat::RESIZE_MODE_STRETCH)
			$image->resize($box);
		elseif( $format->getResizeMode() == ImageFormat::RESIZE_MODE_CROP )
			$image->thumbnail($box);

		$outputName = rtrim($this->tempFolder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . md5(uniqid().$file) . '.' . $format->getFormat();
		$image->save($outputName, array('quality' => $format->getQuality()));

		return $outputName;
	}
	
}