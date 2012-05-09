<?php

namespace Oryzone\Bundle\ImageResizerBundle\Image;

use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface;

/**
 * Class that ease the process of generate different version (in size) of a given image.
 * Suppose you have to generate Small, big, and medium variants of an original image. By using this class
 * you only have to specify the output formats (by using the <ImageFormat> class) and call the method
 * <ImageResizer->generate()>, then you will get an array containing the paths of all the generated files.
 * This class depends from the imagine library (https://github.com/avalanche123/Imagine), so you have to provide an
 * instance of Imagine when you instance a new <ImageResizer>.
 *
 * @author Luciano Mammino <lmammino@oryzone.com>
 * @version 1.0
 *
 * @Example
 *
 * <code>
 *
 *      $picture = __DIR__ . DIRECTORY_SEPARATOR . 'picture.jpg';
 *      $tempFolder = __DIR__ . DIRECTORY_SEPARATOR . 'temp';
 *
 *      $formatBig = new Oryzone\Bundle\ImageResizerBundle\Image\ImageFormat('big', 800, 600);
 *      $formatSmall = new Oryzone\Bundle\ImageResizerBundle\Image\ImageFormat('small', 150, 100);
 *
 *      $imageResizer = new Oryzone\Bundle\ImageResizerBundle\Image\ImageResizer($imagine, $tempFolder);
 *
 *      $generatedFiles = $imageResizer->addFormat($formatBig)
 *                                     ->addFormat($formatSmall)
 *                                     ->generateFrom($picture)
 *
 * </code>
 *
 * <code>$imagine</code> is your imagine instance.
 */
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

	/**
	 * An array of formats to apply
	 *
	 * @var array<ImageFormat> $formats
	 */
	protected $formats;

	/**
	 * An array of formats or an array of arrays to be used as default formats (added on initialization)
	 *
	 * @var array|array<ImageFormat> $defaultFormats
	 */
	protected $defaultFormats;

	/**
	 * An array containing the list of all generated files
	 *
	 * @var array $generatedFiles
	 */
	protected $generatedFiles;

	/**
	 * Flag used to indicate whether to avoid stretching small images into bigger ones
	 *
	 * @var bool $skipBiggerFormatsEnabled
	 */
	protected $skipBiggerFormatsEnabled;
	
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
		$this->generatedFiles = array();
		$this->skipBiggerFormatsEnabled = false;
	}

	/**
	 * Removes all currently attached formats
	 *
	 * @return ImageResizer to enable fluent syntax
	 */
	public function clearFormats()
	{
		$this->formats = array();
		return $this;
	}

	/**
	 * Replaces the current array of formats with a new one
	 *
	 * @param array<ImageFormat> $formats
	 * @return ImageResizer to enable fluent syntax
	 */
	public function setFormats($formats)
	{
		$this->formats = $formats;
		return $this;
	}

	/**
	 * Gets the currently set formats
	 *
	 * @return array<ImageFormat>
	 */
	public function getFormats()
	{
		return $this->formats;
	}

	/**
	 * Sets the flag used to indicate whether avoid stretchig small images into big ones
	 *
	 * @param bool $enabled <code>true</code> to enable skipping, <code>false</code> otherwise
	 * @return ImageResizer to enable fluent syntax
	 */
	public function skipBiggerFormats($enabled = true)
	{
		$this->skipBiggerFormatsEnabled = $enabled;
		return $this;
	}

	/**
	 * Get temp folder
	 *
	 * @return string
	 */
	public function getTempFolder()
	{
		return $this->tempFolder;
	}

	/**
	 * Sets the temporary folder
	 *
	 * @param string $tempFolder
	 * @return ImageResizer to enable fluent syntax
	 */
	public function setTempFolder($tempFolder)
	{
		$this->tempFolder = $tempFolder;
		return $this;
	}

	/**
	 * Gets the array of generated files. The array is organized as a <code>filename => path</code>  key/value array
	 *
	 * @return array
	 */
	public function getGeneratedFiles()
	{
		return $this->generatedFiles;
	}

	/**
	 * Add a new format to the list of the formats to generate
	 *
	 * @param ImageFormat $format
	 * @return ImageResizer to enable fluent syntax
	 */
	public function addFormat(ImageFormat $format)
	{
		$this->formats[] = $format;
		return $this;
	}

	/**
	 * Load the default formats (if any) into the list of current formats. Useful if you have previously removed the
	 * attached formats and want the default ones again.
	 *
	 * @return ImageResizer to enable fluent syntax
	 */
	public function loadDefaultFormats()
	{
		$formats = array();

		foreach($this->defaultFormats as $defaultFormat)
			if($defaultFormat instanceof ImageFormat)
				$formats[] = $defaultFormat;
			else
				$formats[] = ImageFormat::newFromArray($defaultFormat);

		$this->formats = $formats;
		return $this;
	}

	/**
	 * Generats all the variants of a given file using all the attached formats
	 *
	 * @param string $file the path of the file to process
	 * @return array an array containing the paths of all the generated files. The array is organized as a key/value
	 * array where values are the file paths and keys are the related <ImageFormat> names.
	 * @throws \RuntimeException if cannot create or use the current temporary folder
	 */
	public function generateFrom($file)
	{
		if( !file_exists($this->tempFolder) )
		{
			if(!(@mkdir($this->tempFolder)))
				throw new \RuntimeException('Cannot create temporary folder "'.$this->tempFolder."'");
		}
		elseif( file_exists($this->tempFolder) && !is_dir($this->tempFolder) )
			throw new \RuntimeException('The path "'.$this->tempFolder."' given as temporary folder is not a folder");

		$generated = array();
		foreach($this->formats as $format)
		{
			if( ($result = $this->processFormat($file, $format)) )
				$generated[$format->getName()] = $result;
		}

		return $generated;
	}

	/**
	 * Processes a single format of an image
	 *
	 * @param string $file the path of the file to process
	 * @param ImageFormat $format
	 * @return null|string
	 */
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

		if(
			$this->skipBiggerFormatsEnabled &&
			($originalWidth < $width || $originalHeight < $height)
		)
			return NULL;

		$box = new \Imagine\Image\Box($width, $height);

		if($format->getResizeMode() == ImageFormat::RESIZE_MODE_PROPORTIONAL || $format->getResizeMode() == ImageFormat::RESIZE_MODE_STRETCH)
			$image->resize($box);
		elseif( $format->getResizeMode() == ImageFormat::RESIZE_MODE_CROP )
			$image = $image->thumbnail($box, ImageInterface::THUMBNAIL_OUTBOUND);

		$filename = md5($file).'_'.$format->getName();
		$outputName = rtrim($this->tempFolder, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename . '.' . $format->getFormat();
		$image->save($outputName, array('quality' => $format->getQuality()));

		$this->generatedFiles[$filename] = $outputName;

		return $outputName;
	}

	/**
	 * Deletes all generated files (if still exist)
	 *
	 * @return ImageResizer
	 * @throws \RuntimeException if some file can't be deleted
	 */
	public function deleteGeneratedFiles()
	{
		foreach($this->generatedFiles as $filename => $path)
		{
			if( file_exists($path) && is_file($path) )
				if(!@unlink($path))
					throw new \RuntimeException('Cannot delete the file "'.$filename.'" ("'.$path.'")');
		}

		return $this;
	}

}