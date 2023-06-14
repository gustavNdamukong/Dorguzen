<?php

namespace DGZ_library\DGZ_Uploader;

use configs\config;
use Exception;

class DGZ_Uploader extends DGZ_Upload {

	protected $_thumbDestination;




	/**
	 * This constructor takes two arguments; the upload destination folder as a string, and an optional sub folder therein which could be
	 * for example, an ID or image or product name.
	 *
	 * @param $path string the array key referencing the file upload destination path you set in configs\config.php
	 * @param $uniqeSubFolder string (optional) that will contain the a sub-folder name for cases where unique records have their own sub folders, for example;
	 * 	the images of a listed item in an e-commerce application. Take note that just like with the $path value; the trailing slash appended to
	 * 	$uniqeSubFolder is crucial.
	 */
	public function __construct($path, $uniqeSubFolder = '') {
		$config = new config();
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








	public function setThumbDestination($path) {
		if (!is_dir($path) || !is_writable($path)) {
			throw new Exception("$path must be a valid, writable directory.");
		}
		else {
			$this->_thumbDestination = $path;
		}
	}









	public function setThumbSuffix($suffix) {
		if (preg_match('/\w+/', $suffix))
		{
			if (strpos($suffix, '_') !== 0)
			{
				$this->_suffix = '_' . $suffix;
			}
			else
			{
				$this->_suffix = $suffix;
			}
		}
		else {
			$this->_suffix = '';
		}
	}








	protected function createThumbnail($image) {
		$thumb = new DGZ_Thumbnail($image);
		$thumb->setDestination($this->_thumbDestination);
		//$thumb->setSuffix($this->_suffix);
		$thumb->create();
		$messages = $thumb->getMessages();
		$this->_messages = array_merge($this->_messages, $messages);
	}








	/**
	 * This method overrides that of the parent class (processFile()) in order to not only upload as its parent does, but to
	 * resize the file.
	 *
	 * Having extended the DGZ_Upload parent class, notice how it calls the createThumbnail() method to generate a thumbnail from the uploaded image;
	 * something its parent class does not do. The parent class only does an upload, that's it.
	 * The DGZ_Thumbnail class which the createThumbnail method instantiates behind the scenes was a class
	 * created just for this child class's use. So this class basically extends its parent's functionality of
	 * only uploading, to uploading and resizing (thumbnail creation).
	 * Use the parent DGZ_Upload class or DGZ_library\FileUploader for uploading large files like videos, audios,
	 * large images.
	 * But use this DGZ_Uploader class for uploading and resizing images
	 *
	 * @param $filename
	 * @param $error
	 * @param $size
	 * @param $type
	 * @param $tmp_name
	 * @param $path
	 * @param $modify
	 * @param $overwrite
	 *
	 * @return void
	 */
	protected function processFile($filename, $error, $size, $type, $tmp_name, $path, $modify, $overwrite)
	{
		$OK = $this->checkError($filename, $error);
		if ($OK) {
			//------------------------------------------------------
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