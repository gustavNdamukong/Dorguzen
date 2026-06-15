<?php

namespace Dorguzen\Core\DGZ_Uploader;

use Dorguzen\Config\Config;
use Exception;

class DGZ_Uploader extends DGZ_Upload {

	protected $_thumbDestination;
	protected $_thumbMaxSize = 200;




	/**
	 * @param $path string  Either a key from configs/app.php that resolves to an absolute
	 *                      directory path, OR an absolute path directly.
	 * @param $uniqeSubFolder string (optional) sub-directory appended to the destination —
	 *                      useful when each record stores its images in its own folder,
	 *                      e.g. a product ID or portfolio item slug. Include a trailing slash.
	 */
	public function __construct($path, $uniqeSubFolder = '') {
		$config = container(Config::class);
		if (array_key_exists($path, $config->getconfig()))
		{
			if ($uniqeSubFolder != '') {
				$destination = trim($config->getconfig()[$path].$uniqeSubFolder.'/');
			}
			else
			{
				$destination = $config->getconfig()[$path];
			}
		}
		else
		{
			if ($uniqeSubFolder != '') {
				$destination = $path.$uniqeSubFolder.'/';
			}
			else
			{
				$destination = $path;
			}
		}

		$maxFileUploadSize = $config->getconfig()['maxFileUploadSize'];

		$this->setMaxSize($maxFileUploadSize);

		parent::__construct($destination);
		$this->_thumbDestination = $destination;
	}




	/**
	 * Set the maximum pixel dimension for generated thumbnails (default: 200).
	 * The thumbnail is scaled proportionally so neither width nor height exceeds this value.
	 * Call before move('resize').
	 */
	public function setThumbMaxSize(int $size): self {
		$this->_thumbMaxSize = abs($size);
		return $this;
	}




	/**
	 * Direct thumbnails to a different folder from the originals.
	 * Call before move(). If not called, thumbnails land in the same folder as the original.
	 */
	public function setThumbDestination($path) {
		if (!is_dir($path) || !is_writable($path)) {
			throw new Exception("$path must be a valid, writable directory.");
		}
		else {
			$this->_thumbDestination = $path;
		}
	}




	protected function createThumbnail($image) {
		$thumb = new DGZ_Thumbnail($image);
		$thumb->setDestination($this->_thumbDestination);
		$thumb->setMaxSize($this->_thumbMaxSize);
		$thumb->create();
		$messages = $thumb->getMessages();
		$this->_messages = array_merge($this->_messages, $messages);
	}




	/**
	 * Overrides DGZ_Upload::processFile() to add optional thumbnail generation.
	 *
	 * move('original')       — upload only, no thumbnail (image types only, size-checked)
	 * move('original-allow') — upload only, no validation (use in admin areas; allows any type)
	 * move('resize')         — upload original + auto-generate _thb thumbnail in one step
	 *
	 * Use DGZ_Upload (the parent class) directly for videos, audio, PDFs, and other
	 * non-image files where you need more control over validation.
	 */
	protected function processFile($filename, $error, $size, $type, $tmp_name, $path, $modify, $overwrite)
	{
		$OK = $this->checkError($filename, $error);
		if ($OK) {
			if ($modify == 'original-allow')
			{
				$sizeOK = true;
				$typeOK = true;
			}
			else
			{
				$sizeOK = $this->checkSize($filename, $size);
				$typeOK = $this->checkType($filename, $type);
			}

			if ($sizeOK && $typeOK) {
				$name = $this->createFileName($filename, $overwrite);
				$success = move_uploaded_file($tmp_name, $this->_destination . $name);
				if ($success) {
					$this->_filenames[] = $name;
					$message = "$filename uploaded successfully";
					if ($this->_renamed) {
						$message .= " and renamed $name";
					}
					$this->_messages[] = $message;

					if ($modify == 'resize') {
						$this->createThumbnail($this->_destination . $name);
					}
				}
				else {
					$this->_messages[] = "Could not upload $filename";
				}
			}
		}
	}
}
