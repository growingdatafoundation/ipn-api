<?php
Class Wikipedia{
	 
	public static function renderEntryLink($src, $title){
	 	return '<div class="item item-detail"><a href="'.$src.'" title="'.$title.'" target="_blank">'.$title.'</a></div>';
	 	
	 }
	
}
