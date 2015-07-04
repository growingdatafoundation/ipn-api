<?php
class Helpers{
	/**
	 * @TODO: radius => zoom
	 */
	public static function googleMapsUri($latitude, $longitude){
		return 'http://maps.google.com/?q='.$latitude.','.$longitude;
	}
}//c
