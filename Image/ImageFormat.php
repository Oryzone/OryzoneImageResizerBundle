<?php

namespace Oryzone\Bundle\ImageResizerBundle\Image;

/**
 * Class used to define formats for <ImageResizer>.
 * Every instance of this class is immutable, i.e. their attributes cannot be changed after initialization.
 *
 * @author Luciano Mammino <lmammino@oryzone.com>
 * @version 1.0
 */
class ImageFormat
{
	/**
	 * Constant for 'stretch' resize mode
	 */
	const RESIZE_MODE_STRETCH = 'stretch';

	/**
	 * Constant for 'proportional' resize mode
	 */
	const RESIZE_MODE_PROPORTIONAL = 'proportional';

	/**
	 * Constant for 'crop' resize mode
	 */
	const RESIZE_MODE_CROP = 'crop';

	/**
	 * The name of the format
	 *
	 * @var string $name
	 */
	protected $name;

	/**
	 * The width of the new image
	 *
	 * @var int $width
	 */
	protected $width;

	/**
	 * The height of the new image
	 *
	 * @var int $height
	 */
	protected $height;

	/**
	 * The currently set resize mode
	 *
	 * @var string $resizeMode
	 */
	protected $resizeMode;

	/**
	 * The file format of the new file (e.g. 'jpg', 'gif')
	 *
	 * @var string $format
	 */
	protected $format;

	/**
	 * The quality of the new image
	 *
	 * @var int $quality
	 */
	protected $quality;

	/**
	 * An array of all the available resize modes
	 *
	 * @var array
	 */
	protected static $RESIZE_MODES = array
	(
		self::RESIZE_MODE_CROP,
		self::RESIZE_MODE_PROPORTIONAL,
		self::RESIZE_MODE_STRETCH
	);

	/**
	 * Constructor
	 *
	 * @param   string      $name       The name of the format
	 * @param   int         $width      The width of the image
	 * @param   int         $height     The height of the image
	 * @param   null|string $resizeMode The resize mode (use one of the RESIZE_MODE_ constants)
	 * @param   null|string $format     The file format of the image (e.g. jpg, gif, png)
	 * @param   null|int    $quality    An integer representing the file quality (from 0 to 100)
	 *
	 * @throws \InvalidArgumentException if one or more arguments are not valid or inconsistent
	 */
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

	/**
	 * Gets the name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get the height
	 *
	 * @return int
	 */
	public function getHeight()
	{
		return $this->height;
	}

	/**
	 * Get the resize mode
	 *
	 * @return string
	 */
	public function getResizeMode()
	{
		return $this->resizeMode;
	}

	/**
	 * Get the width
	 *
	 * @return int
	 */
	public function getWidth()
	{
		return $this->width;
	}

	/**
	 * Get the format
	 *
	 * @return string
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * Get the quality
	 *
	 * @return int
	 */
	public function getQuality()
	{
		return $this->quality;
	}

	/**
	 * To string method
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}

	/**
	 * Generates a new <ImageFormat> instance from an array. The array must contain the following keys:
	 *
	 * - name
	 * - width
	 * - height
	 * - resizeMode
	 * - format
	 * - quality
	 *
	 * @static
	 * @param array $array
	 * @return ImageFormat
	 */
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