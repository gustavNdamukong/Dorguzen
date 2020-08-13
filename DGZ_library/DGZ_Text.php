
<?php


class DGZ_Text
{

    public static function converttoparas($text) 
    {
        $text = trim($text);
        return '<p>' . preg_replace('/[\r\n]+/', '</p><p>', $text) . '</p>';
    }



    public static function getFirstPara($text, $number=2) 
    {
        // use regex to split into sentences
        $sentences = preg_split('/([.?!]["\']?\s)/', $text, $number+1, PREG_SPLIT_DELIM_CAPTURE);
        if (count($sentences) > $number * 2) {
          $remainder = array_pop($sentences);
        } else {
              $remainder = '';
        }
        $result = array();
        $result[0] = implode('', $sentences);
        $result[1] = $remainder;
        return $result;
    }
}

?>