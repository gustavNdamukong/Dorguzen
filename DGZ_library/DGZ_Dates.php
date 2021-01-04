<?php
namespace DGZ_library;



class DGZ_Dates
{

    public static function convertDateToMySQL($month, $day, $year) 
    {
        $month = trim($month);
        $day = trim($day);
        $year = trim($year);
        $result[0] = false;
        if (empty($month) || empty($day) || empty($year)) 
        {
            $result[1] = 'Please fill in all fields';
        } 
        elseif (!is_numeric($month) || !is_numeric($day) || !is_numeric($year)) 
        {
              $result[1] = 'Please use numbers only';
        } 
        elseif (($month < 1 || $month > 12) || ($day < 1 || $day > 31) || ($year < 1000 || $year > 9999)) 
        {
          $result[1] = 'Please use numbers within the correct range';
        } 
        elseif (!checkdate($month,$day,$year)) 
        {
              $result[1] = 'You have used an invalid date';
        } 
        else 
        {
              $result[0] = true;
              $result[1] = "$year-$month-$day";
        }
        
        return $result;
    }



    /**
     * Converts a date in DD/MM/YYYY (or DD-MM-YYYY) format into YYYY-MM-DD format
     * suitable for Postgres
     *
     * @param string $date The date to convert
     * @return string The same date in YYYY-MM-DD format
     */
    public static function DDMMYYYYtoYYYYMMDD($date) {

        return date('Y-m-d', strtotime(str_replace('/', '-', $date)));

    }





    /**
     * Converts a date from YYYY/MM/DD/ (or YYY-MM-DD) format into DD-MM-YYYY format
     *
     * @param string $date The date to convert
     * @return string The same date in DD-MM-YYYY format
     */
    public static function YYYYMMDDtoDDMMYYYY($date) {

        return date('d-m-Y', strtotime(str_replace('/', '-', $date)));

    }



    /**
     * Converts a Postgres/ANSI timestamp to a UK date/time format
     *
     * @param string $timestamp The timestamp to convert
     * @return string A formatted date
     */
    public static function timestampToDateTime($timestamp) {

        return date('d/m/Y H:i:s', strtotime($timestamp));
    }
}

?>