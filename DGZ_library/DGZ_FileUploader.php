<?php

	namespace DGZ_library;

	use Exception;
	use DGZ_library\DGZ_Exception;


	/**
	 * Use this class for uploading big files like audios and video files
	 *
	 * Class DGZ_FileUploader
	 * @package DGZ_library
	 */
	class DGZ_FileUploader {

		  protected $_uploaded = array();



		  protected $_destination;



		  //protected $_max = 51200;
		  protected $_max = 85899346; //10 GB



		  protected $_messages = array();



		  protected $_permitted = array('image/gif',
										'image/jpeg',
										'image/pjpeg',
										'image/png');
		  protected $_renamed = false;



		  protected $_filenames = array();






		  public function __construct($path) {
			  try {
				  if (!is_dir($path) || !is_writable($path)) {
					  throw new DGZ_Exception("$path must be a valid, writable directory.");
				  }
			  }
			catch (DGZ_Exception $e)
			{
				if ($e instanceof DGZ_Exception) {

					$view = \DGZ_library\DGZ_View::getView('DGZExceptionView', null, 'html');
					$view->show($e);
				}
			}
			$this->_destination = $path;
			$this->_uploaded = $_FILES;
		  }








		  public function getMaxSize() {
			return number_format($this->_max/1024, 1) . 'kB';
		  }








		  public function setMaxSize($num) {
			if (!is_numeric($num)) {
			  throw new Exception("Maximum size must be a number.");
			}
			$this->_max = (int) $num;
		  }








		  public function move($overwrite = false) {
				$path = $this->_destination;
				$field = current($this->_uploaded);
				if (is_array($field['name'])) {
					// process multiple upload
				  	foreach ($field['name'] as $number => $filename) {
						$this->_renamed = false;
						$this->processFile($filename, $field['error'][$number], $field['size'][$number], $field['type'][$number], $field['tmp_name'][$number], $path, $overwrite);
				  	}
				} else {
				  		$this->processFile($field['name'], $field['error'], $field['size'], $field['type'], $field['tmp_name'], $path, $overwrite);
				}
		  }










		  public function getMessages() {
			return $this->_messages;
		  }









		  protected function checkError($filename, $error) {
			switch ($error) {
			  case 0:
				return true;
			  case 1:
			  case 2:
				$this->_messages[] = "$filename exceeds maximum size: " . $this->getMaxSize();
				return true;
			  case 3:
				$this->_messages[] = "Error uploading $filename. Please try again.";
				return false;
			  case 4:
				$this->_messages[] = 'No file selected.';
				return false;
			  default:
				$this->_messages[] = "System error uploading $filename. Contact webmaster.";
				return false;
			}
		  }









		  protected function checkSize($filename, $size) {
			if ($size == 0) {
			  return false;
			} elseif ($size > $this->_max) {
			  $this->_messages[] = "$filename exceeds maximum size: " . $this->getMaxSize();
			  return false;
			} else {
			  return true;
			}
		  }









		  protected function checkType($filename, $type) {
			if (empty($type)) {
			  return false;
			} elseif (!in_array($type, $this->_permitted)) {
			  $this->_messages[] = "$filename is not a permitted type of file.";
			  return false;
			} else {
			  return true;
			}
		  }









		  public function addPermittedTypes($types) {
			$types = (array) $types;
			$this->isValidMime($types);
			$this->_permitted = array_merge($this->_permitted, $types);
		  }









		  public function getFilenames() {
			return $this->_filenames;
		  }









		  protected function isValidMime($types) {
			$alsoValid = array('image/tiff',
							   'application/pdf',
							   'text/plain',
							   'text/rtf');
			$valid = array_merge($this->_permitted, $alsoValid);
			foreach ($types as $type) {
			  if (!in_array($type, $valid)) {
				throw new Exception("$type is not a permitted MIME type");
			  }
			}
		  }










		  protected function checkName($name, $overwrite)
		  {
				$nospaces = str_replace(' ', '_', $name);

				if ($nospaces != $name) {
				  $this->_renamed = true;
				}
				if (!$overwrite) {
					$existing = scandir($this->_destination);
					if (in_array($nospaces, $existing)) {
						$dot = strrpos($nospaces, '.');
						if ($dot) {
							$base = substr($nospaces, 0, $dot);
							$extension = substr($nospaces, $dot);
						} else {
							$base = $nospaces;
							$extension = '';
						}

						$i = 1;
						do {
							$nospaces = $base . '_' . $i++ . $extension;
						} while (in_array($nospaces, $existing));
							$this->_renamed = true;
				  }
				}
				return $nospaces;
		  }









		  protected function processFile($filename, $error, $size, $type, $tmp_name, $path, $overwrite) {
				$OK = $this->checkError($filename, $error);
				if ($OK) {
					$name = $this->checkName($filename, $overwrite);

					$success = move_uploaded_file($tmp_name, $this->_destination . $name);
					if ($success) {
					  $this->_filenames[] = $name;
						$message = "$filename uploaded successfully";
						if ($this->_renamed) {
						  $message .= " and renamed $name";
						}
						$this->_messages[] = $message;
					} else {
					  $this->_messages[] = "Could not upload $filename";
					}
				}
		  }

	}