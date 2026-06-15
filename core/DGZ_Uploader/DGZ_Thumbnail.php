<?php

namespace Dorguzen\Core\DGZ_Uploader;

class DGZ_Thumbnail {
  protected $_original;
  protected $_originalwidth;
  protected $_originalheight;
  protected $_thumbwidth;
  protected $_thumbheight;
  protected $_maxSize = 500;
  protected $_canProcess = false;
  protected $_imageType;
  protected $_destination;
  protected $_name;
  protected $_suffix = '_thb';
  protected $_quality = 82;  // JPEG/WebP quality (1-100). 82 = excellent visual quality at ~60% the size of quality 100.
  protected $_messages = array();

  public function __construct($image) {
	if (is_file($image) && is_readable($image)) {
	  $details = getimagesize($image);
	} else {
	  $details = null;
	  $this->_messages[] = "Cannot open $image.";
	}

	if (is_array($details)) {
  	  $this->_original = $image;
	  $this->_originalwidth = $details[0];
	  $this->_originalheight = $details[1];

      $this->checkType($details['mime']);
	} else {
	  $this->_messages[] = "$image doesn't appear to be an image.";
	}
  }

  public function setDestination($destination) {
	if (is_dir($destination) && is_writable($destination)) {
       // get last character
	   $last = substr($destination, -1);
	   // add a trailing slash if missing
	  if ($last == '/' || $last == '\\') {
		$this->_destination = $destination;
	  } else {
	    $this->_destination = $destination . DIRECTORY_SEPARATOR;
	  }
	} else {
	  $this->_messages[] = "Cannot write to $destination.";
	}
  }

  public function setMaxSize($size) {
	if (is_numeric($size)) {
	  $this->_maxSize = abs($size);
	}
  }

  public function setSuffix($suffix) {
	if (preg_match('/^\w+$/', $suffix)) {
		if (strpos($suffix, '_') !== 0) {
			$this->_suffix = '_' . $suffix;
		} else {
			$this->_suffix = $suffix;
		}
	} else {
		$this->_suffix = '';
	}
  }

  /**
   * Set JPEG and WebP output quality (1-100). Default is 82.
   * Has no effect on PNG (which uses fixed compression level 6) or GIF output.
   */
  public function setQuality(int $quality): void {
	$this->_quality = max(1, min(100, $quality));
  }

  public function create() {
    if ($this->_canProcess && $this->_originalwidth != 0) {
	  $this->calculateSize($this->_originalwidth, $this->_originalheight);
	  $this->getName();
	  $this->createThumbnail();
	} elseif ($this->_originalwidth == 0) {
	  $this->_messages[] = 'Cannot determine size of ' . $this->_original;
	}
  }

  public function getMessages() {
	return $this->_messages;
  }

  public function test() {
	echo 'File: ' . $this->_original . '<br>';
	echo 'Original width: ' . $this->_originalwidth . '<br>';
	echo 'Original height: ' . $this->_originalheight . '<br>';
	echo 'Image type: ' . $this->_imageType . '<br>';
	echo 'Destination: ' . $this->_destination . '<br>';
	echo 'Max size: ' . $this->_maxSize .  '<br>';
	echo 'Suffix: ' . $this->_suffix .  '<br>';
	echo 'Thumb width: ' . $this->_thumbwidth . '<br>';
	echo 'Thumb height: ' . $this->_thumbheight . '<br>';
	echo 'Base name: ' . $this->_name . '<br>';
	if ($this->_messages) {
	  print_r($this->_messages);
	}
  }

  protected function checkType($mime) {
	$mimetypes = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
	if (in_array($mime, $mimetypes)) {
	  $this->_canProcess = true;
	  $this->_imageType = substr($mime, 6);
	}
  }

  protected function calculateSize($width, $height) {
	if ($width <= $this->_maxSize && $height <= $this->_maxSize) {
	  $ratio = 1;
	} elseif ($width > $height) {
	  $ratio = $this->_maxSize/$width;
	} else {
	  $ratio = $this->_maxSize/$height;
	}
	$this->_thumbwidth = round($width * $ratio);
	$this->_thumbheight = round($height * $ratio);
  }

  protected function getName() {
	$extensions = array('/\.jpg$/i', '/\.jpeg$/i', '/\.png$/i', '/\.gif$/i', '/\.webp$/i');
	$this->_name = preg_replace($extensions, '', basename($this->_original));
  }

  protected function createImageResource() {
	if ($this->_imageType == 'jpeg') {
	  return imagecreatefromjpeg($this->_original);
	} elseif ($this->_imageType == 'png') {
	  return imagecreatefrompng($this->_original);
	} elseif ($this->_imageType == 'gif') {
      return imagecreatefromgif($this->_original);
	} elseif ($this->_imageType == 'webp') {
	  return imagecreatefromwebp($this->_original);
	}
  }

  protected function createThumbnail() {
	$resource = $this->createImageResource();
	$thumb = imagecreatetruecolor($this->_thumbwidth, $this->_thumbheight);

	// Preserve alpha channel for PNG and WebP
	if ($this->_imageType == 'png' || $this->_imageType == 'webp') {
	  imagealphablending($thumb, false);
	  imagesavealpha($thumb, true);
	}

	imagecopyresampled($thumb, $resource, 0, 0, 0, 0, $this->_thumbwidth, $this->_thumbheight, $this->_originalwidth, $this->_originalheight);

	$newname = $this->_name . $this->_suffix;  // suffix (_thb by default) was previously missing

	if ($this->_imageType == 'jpeg') {
	  $newname .= '.jpg';
	  // Quality 82: excellent visual quality at roughly 60% the file size of quality 100
	  $success = imagejpeg($thumb, $this->_destination . $newname, $this->_quality);
	} elseif ($this->_imageType == 'png') {
	  $newname .= '.png';
	  // PNG compression 0-9. Level 6 is the web standard — good size reduction, fast decode.
	  $success = imagepng($thumb, $this->_destination . $newname, 6);
	} elseif ($this->_imageType == 'gif') {
	  $newname .= '.gif';
	  $success = imagegif($thumb, $this->_destination . $newname);
	} elseif ($this->_imageType == 'webp') {
	  $newname .= '.webp';
	  $success = imagewebp($thumb, $this->_destination . $newname, $this->_quality);
	}

	if ($success) {
	  $this->_messages[] = "$newname created successfully.";
	} else {
      $this->_messages[] = "Couldn't create a thumbnail for " . basename($this->_original);
	}
	imagedestroy($resource);
	imagedestroy($thumb);
  }
}
