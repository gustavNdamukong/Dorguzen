<?php
namespace DGZ_library;

/**
 *
 * @author Gustav
 */
use settings\Settings;

class DGZ_SliderEngine
{

    protected $_images = [
        [
            'name' => 'slide1.jpg',
            'alt' => 'The perfect shine'
        ],
        [
            'name' => 'slide2.jpg',
            'alt' => 'Professionalism'
        ],
        [
            'name' => 'slide3.jpg',
            'alt' => 'Reliability'
        ],
        [
            'name' => 'slide4.jpg',
            'alt' => 'Deep Cleaning'
        ],
        [
            'name' => 'slide5.JPG',
            'alt' => 'Reliability'
        ]
    ];





    public function __construct($freshImages = [])
    {
        //If a fresh array of ims is passed to this class, that means the programmer wants to slide a new set of imgs, so replace the default ones
        if (!empty($freshImages))
        {
            $this->_images = array();
            $this->_images = $freshImages;
        }

    }






    /**
     *
     * @Description: This method displays an HTML div with all the necessary JS classes n IDs for the images you pass to it to be animated as a slider.
     *      You must have a folder called 'home_slide_images' (or whatever you want to call it-just remember to edit the 'src' attribute of the img tag
     *      in this method with) in your 'assets/images' directory in which you have all the images listed in the $_images field, or the images you
     *      passed to the constructor of this class when you called it.
     *home_slide_images
     * @params: pass it an multidimensional array of images, where each array has a 'name' and an 'alt' key.
     *
     *
     *
     */
    public function showSlider()
    {
        $settings = new Settings();
        ?>
        <div id="amazingslider-1" style="display:block;position:relative;margin:16px auto 32px;">
            <ul class="amazingslider-slides" style="display:none;">
        <?php
            foreach ($this->_images as $img)
            { ?>
                <li><img src="<?=$settings->getFileRootPath()?>assets/images/home_slide_images/<?=$img['name']?>" alt="<?=$img['alt']?>" width='1000' height='400' /></li>
                <?php
            } ?>
            </ul>
        </div>
    <?php
    }








}

?>




