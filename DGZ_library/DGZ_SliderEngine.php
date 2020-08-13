<?php
namespace DGZ_library;

/**
 *
 * @author Gustav
 */


class DGZ_SliderEngine
{

    protected $_images = [
        [
            'name' => 'slide1.jpg',
            'alt' => 'The perfect shine'
        ],
        [
            'name' => 'wegotyourback.jpg',
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
     * @Description: This method displays an HTML div with all the necessary JS classes n IDs for the imgs you pass to it to be animated as a slider
     *      You must have a folder in your 'images' folder (wh is in your site root folder) called 'sliderimgs' where you must have all the imgs you pass to this meth
     *
     * @params: pass it an array of images which must be a multi-dimensional array, where each one has a 'name', and 'alt' key n value respectively
     *
     *
     *
     */
    public function showSlider()
    { ?>
        <div id="amazingslider-1" style="display:block;position:relative;margin:16px auto 32px;">
            <ul class="amazingslider-slides" style="display:none;">
        <?php
            foreach ($this->_images as $img)
            {
                echo "<li><img src='assets/images/home_slide_images/{$img['name']}' alt='$img[alt]' width='1000' height='400' /></li>";
            } ?>
            </ul>
        </div>
    <?php
    }








}

?>




