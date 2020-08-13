<?php

namespace DGZ_library\DGZ_Uploader;


use DGZ_library\DGZ_Uploader\DGZ_Thumbnail;
use DGZ_library\DGZ_Uploader\DGZ_Upload;
use settings\Settings;

class DGZ_Uploader extends DGZ_Upload {

	protected $_thumbDestination;

	







	/**
	 * This constructor takes two arguments; the upload destination folder as a string, and a boolean whether to delete a previous copy of the same file
	 * if one is found, or rename the new one and keep both files.
	 *
	 * @param $path string
	 * @param $uniqeSubFolder string (optional) that will contain the a sub-folder name for cases where unique records have their own sub folders, for example;
	 * 	the images of a listed item in an e-commerce application. Take note that just like with the $path value; the trailing slash appended to $uniqeSubFolder is crucial.
	 *
	 * @return void
	 */
	public function __construct($path, $uniqeSubFolder = '') {

		//set upload path dynamically (value of $path is for example 'gallery')
		$settings = new Settings();
		if ($uniqeSubFolder != '') {
			$destination = trim($settings->getSettings()[$path].$uniqeSubFolder.'/');
		}
		else
		{
			$destination = config("dgz_uploader.$path") ;
		}

		//set the file size
		$maxFileUploadSize = $settings->getSettings()['maxFileUploadSize'];

		$this->setMaxSize($maxFileUploadSize);

		parent::__construct($destination);
		$this->_thumbDestination = $destination;
	}








	public function setThumbDestination($path, $secondThumbDestiny) {
		if (!is_dir($path) || !is_writable($path)) {
			throw new Exception("$path must be a valid, writable directory.");
		} else {
			$this->_thumbDestination = $path; }
		if ($this->_secondThumb) {
			if (!is_dir($secondThumbDestiny) || !is_writable($secondThumbDestiny)) {
				throw new Exception("$secondThumbDestiny must be a valid, writable directory.");
			} else {
				$this->_secondThumbDestiny = $secondThumbDestiny; }
		}
	}









	public function setThumbSuffix($suffix) {
		//if (preg_match('/\w+/', $suffix)) {
		//if (strpos($suffix, '_') !== 0) {
		// $this->_suffix = '_' . $suffix;
		//} else {
		//$this->_suffix = $suffix;
		//}
		//} else {
		$this->_suffix = '';
		//}
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
	 * This method overrides that of the parent class (processFile()).
	 *
	 * Having extended the DGZ_Upload parent class, notice how it calls the createThumbnail() method to generate a thumbnail from the uploaded image;
	 * something its parent class does not do. The parent class only does an upload, that's it. So the DGZ_Thumbnail class which the createThumbnail instantiates behind the scenes
	 * was a class created just for this child class's use, so that it basically extends its parent's function of merely uploading, to uploading and thumbnail creation.
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

			/* //We are uploading videos so we will ignore the check for the filetype (Besides, it's only an authenticated admin user that will have access to this facility)
				  $sizeOK = $this->checkSize($filename, $size);
				  $typeOK = $this->checkType($filename, $type);
				  if ($sizeOK && $typeOK) {
				  */

			//----------------------------------------------

			/////$sizeOK = $this->checkSize($filename, $size);
			/////$typeOK = $this->checkType($filename, $type);
			if ($sizeOK && $typeOK) {
				$name = $this->createFileName($filename, $overwrite);
				$success = move_uploaded_file($tmp_name, $this->_destination . $name);
				if ($success) {
					// add the amended filename to the array of file names
					$this->_filenames[] = $name;
					$message = "$filename uploaded successfully";
					if ($this->_renamed) {
						$message .= " and renamed $name";
					}
					$this->_messages[] = $message;

					// create a thumbnail from the uploaded image if $modify == 'resize'
					//it is possible to modify this script here so that a thumbnail is created in a different location while
					//preserving the earlier uploaded large file
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