<?php

namespace DGZ_library\DGZ_functions;

/**
 * Contains functions for converting between the various representations of dates
 *
 * @author brittaind
 */
class DateConversion {
	
	/**
	 * Converts a date in DD/MM/YYYY (or DD-MM-YYYY) format into YYYY-MM-DD format
	 * suitable for Postgres
	 * 
	 * @param string $date The date to convert
	 * @return string The same date is YYYY-MM-DD format
	 */
	public static function DDMMYYYYtoYYYYMMDD($date) {
		
		return date('Y-m-d', strtotime(str_replace('/', '-', $date)));
		
	}
	
	/**
	 * Converts a date in YYYY-MM-DD format (postgres) into
	 * English DD/MM/YYYY format
	 * 
	 * @param string $date The date to be converted
	 */
	public static function YYYYMMDDtoDDMMYYYY($date) {
		
		return date('d/m/Y', strtotime($date));

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


	/**
	 * Converts a Postgres/ANSI timestamp to a UK date format
	 *
	 * @param $timestamp
	 * @return mixed
	 */
	public static function timestampToDate($timestamp) {

		return date('d/m/Y', strtotime($timestamp));


	}
	
	
	
}
