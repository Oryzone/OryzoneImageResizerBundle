<?php

namespace Oryzone\Bundle\ImageResizerBundle\Image;

class ImageFormat
{
	const RESIZE_MODE_STRETCH = 'stretch';
	const RESIZE_MODE_PROPORTIONAL = 'proportional';
	const RESIZE_MODE_CROP = 'crop';

	protected $name;
	protected $width;
	protected $height;
	protected $resizeMode;
	protected $format;
	protected $quality;

	protected static $RESIZE_MODES = array
	(
		self::RESIZE_MODE_CROP,
		self::RESIZE_MODE_PROPORTIONAL,
		self::RESIZE_MODE_STRETCH
	);

	public function __construct($name, $width, $height, $resizeMode = NULL, $format = NULL, $quality = NULL)
	{
		if($resizeMode === NULL)
			$resizeMode = self::RESIZE_MODE_STRETCH;

		if($format === NULL)
			$format = 'jpg';

		if($quality === NULL)
			$quality = 100;

		if( !in_array($resizeMode, self::$RESIZE_MODES) )
			throw new \InvalidArgumentException('Invalid resize mode');

		foreach( array('width' => $width, 'height' => $height) as $dimension => $size )
			if( !($size === NULL || (is_int($size) && $size > 0)) )
				throw new \InvalidArgumentException( $dimension.' must be NULL or a positive integer');

		if( $resizeMode == self::RESIZE_MODE_PROPORTIONAL &&
			(
				($width != NULL && $height != NULL) ||
				($width == NULL && $height == NULL)
			)
		  )
			throw new \InvalidArgumentException('If you use "proportional" resize mode you must specify only one dimension ("width" or "height") and leave the other as NULL');

		if(
			($resizeMode == self::RESIZE_MODE_STRETCH || $resizeMode == self::RESIZE_MODE_CROP) &&
			($width == NULL || $height == NULL)
		  )
			throw new \InvalidArgumentException('If you use "stretch" or "crop" resize mode you must specify both "width" and "height" ');

		if(!(is_int($quality) && $quality >= 0 && $quality <= 100 ))
			throw new \InvalidArgumentException('"quality" must be an integer between 0 and 100');

		$this->name = $name;
		$this->width = $width;
		$this->height = $height;
		$this->resizeMode = $resizeMode;
		$this->format = $format;
		$this->quality = $quality;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getHeight()
	{
		return $this->height;
	}

	public function getResizeMode()
	{
		return $this->resizeMode;
	}

	public function getWidth()
	{
		return $this->width;
	}

	public function getFormat()
	{
		return $this->format;
	}

	public function getQuality()
	{
		return $this->quality;
	}

	public function __toString()
	{
		return $this->name;
	}

	public static function newFromArray($array)
	{
		return new self(
			$array['name'],
			$array['width'],
			$array['height'],
			$array['resizeMode'],
			$array['format'],
			$array['quality']
		);
	}
}