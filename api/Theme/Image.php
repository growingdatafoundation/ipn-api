<?php
Class Image{
	
	 function __construct(){
	 	
	 }
	 
	public static function renderImage($src, $title, $extra = '', $url = false){
		$a1 = ($url) ? '<a href="'.$url.'" target="_blank">' : '';
		$a2 = ($url) ? '</a>' : '';
	 	return '<div class="imageItem">'.$a1.'<img src="'.$src.'" alt="'.$title.'">'.$a2.$extra.'</div>';
	 	
	 }
	 public static function renderOwner($name, $link){
	 	return '<div class="owner">Flickr user: <a href="'.$link.'" target="blank">'.$name.'</a></div>';
	 }
	
}