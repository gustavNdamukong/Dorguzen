<?php

namespace DGZ_library\DGZ_Uploader;

use Exception;

class DGZ_Upload {

	protected $_uploaded = array();



	protected $_destination;



	protected $_max = 51200;
	//protected $_max = 85899346; //10 GB



	protected $_messages = array();



	protected $_permitted = array('image/gif',
								'image/jpeg',
								'image/pjpeg',
								'image/png'
								);


	protected $_renamed = false;



	protected $_filenames = array();






  public function __construct($path) {
	  try {
		  if (!is_dir($path) || !is_writable($path)) {
			  throw new \DGZ_library\DGZ_Exception("$path must be a valid, writable directory.");
		  }
	  }
	  catch (\Exception $e)
	  {
		  if ($e instanceof \DGZ_library\DGZ_Exception) {

			  $view = \DGZ_library\DGZ_View::getView('DGZExceptionView', null, 'html');
			  $view->show($e);
		  }
		  else {
			  $view = \DGZ_library\DGZ_View::getView('ExceptionView', null, 'html');
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




	/**
	 * Upload the file
	 * @param String $modify has 3 options
	 * 		i) 'original' to upload the file as is with check for file type but no check for file size
	 * 		iii) 'original-allow' to upload a file without checking if its size or type is permitted. This will allow you upload audios and video files.
	 * 			You probably want to use this only in sections of your application used by authenticated admin users.
	 * 		iii) 'resize' to resize the file upon upload according to the specified file upload size, and also check if the file type is allowed.
	 *
	 * 	By default, 'original' is used.  This means that your files are uploaded as they are with no resizing, and the file type is checked against the list
	 *  of allowed file types and rejected if not found in there.
	 * @param Boolean $overwrite to determine whether to replace any previous copy of the file at the destination, or to rename and keep both
	 *
	 */
	public function move($modify = 'original',$overwrite = false) {
		$path = $this->_destination;

		if ($this->_uploaded) {
			$field = current($this->_uploaded);
			if (is_array($field['name'])) {
				foreach ($field['name'] as $number => $filename) {
					// process multiple upload
					$this->_renamed = false;
					$this->processFile($filename, $field['error'][$number], $field['size'][$number], $field['type'][$number], $field['tmp_name'][$number], $path, $modify, $overwrite);
				}
			}
			else {
				$this->processFile($field['name'], $field['error'], $field['size'], $field['type'], $field['tmp_name'], $path, $modify, $overwrite);
			}
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




	/**
	 * Additional doc types to allow in your application. I have added these so your application also allows these text document types.
	 * Feel free to add to the list more document types that you wish to allow into your application.
	 *
	 * @param $types array of file types you want to check if your application accepts
	 * @return void
	 * @throws Exception
	 *
	 */
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





	/**
	 * Checks if a file with the same name previously exists in the upload destination and overwrites the previous file if $overwrite is true
	 * or renames the uploaded file and keeps both files if $overwrite is false
	 *
	 * @param variable $name name of uploaded file
	 * @param Boolean $overwrite true or false whether to replace existing file or not
	 * @return string
	 */
	protected function createFileName($name, $overwrite) {
		$nospaces = str_replace(' ', '_', $name);
		if ($nospaces != $name) {
			$this->_renamed = true;
		}
		if (!$overwrite) {
			$existing = scandir($this->_destination);
			//check if an image with that name already exists
			if (in_array($nospaces, $existing)) {
				$dot = strrpos($nospaces, '.');
				if ($dot) {
					$base = substr($nospaces, 0, $dot);
					$extension = substr($nospaces, $dot);
				} else {
					$base = $nospaces;
					$extension = '';
				}
				//rename the file
				$i = 1;
				do {
					$nospaces = $base . '_' . $i++ . $extension;
				} while (in_array($nospaces, $existing));
				//mark the file as renamed
				$this->_renamed = true;
			}
		}
		//return the new file name
		return $nospaces;
	}






	protected function processFile($filename, $error, $size, $type, $tmp_name, $path, $modify, $overwrite) {
		$OK = $this->checkError($filename, $error);
		if ($OK) {
			$sizeOK = $this->checkSize($filename, $size);
			$typeOK = $this->checkType($filename, $type);
			if ($sizeOK && $typeOK) {
				$name = $this->createFileName($filename, $overwrite);

				$success = move_uploaded_file($tmp_name, $path . $name);
				if ($success) {
					// add the amended filename to the array of file names
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


}
