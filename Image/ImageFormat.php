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

	public function __construct($name, $width, $height, $resizeMode = self::RESIZE_MODE_STRETCH)
	{
		// TODO validate parameters

		$this->name = $name;
		$this->width = $width;
		$this->height = $height;
		$this->resizeMode = $resizeMode;
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

	function __toString()
	{
		return $this->name;
	}


}