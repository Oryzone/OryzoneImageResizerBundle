<?php

namespace Oryzone\Bundle\ImageResizerBundle\Image;

class ImageFormat
{
	const MODE_STRETCH = 'stretch';
	const MODE_PROPORTIONAL = 'proportional';
	const MODE_CROP = 'crop';

	protected $name;
	protected $width;
	protected $height;
	protected $mode;

	public function __construct($name, $width, $height, $mode = self::MODE_PROPORTIONAL)
	{
		$this->name = $name;
		$this->width = $width;
		$this->height = $height;
		$this->mode = $mode;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getHeight()
	{
		return $this->height;
	}

	public function getMode()
	{
		return $this->mode;
	}

	public function getWidth()
	{
		return $this->width;
	}

	function __toString()
	{
		return $this->name;
	}


}