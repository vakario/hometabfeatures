<?php

/**
 *  
 * @author Florent Glauda <skeul73@live.fr>
 */
class FeaturesMod extends ObjectModel {
	
	public $id_hometabfeatures;
	public $title;
	public $content_text;
	public $logo;
	public $image;
	public $description_title;
	public $description_content;
	public $description_content_right;
	public $active;
	
	public static $definition = array(
  		'table' 	=> 'hometabfeatures',
  		'primary' 	=> 'id_hometabfeatures',
  		'multilang' => true,
 		'fields' => array(
   			'id_hometabfeatures' => array(
   				'type' => ObjectModel :: TYPE_INT,
			),
			'title' => array(
   				'type' => ObjectModel :: TYPE_STRING,
				'validate' => 'isString',	
				'lang' => true,				
			),
			'content_text' => array(
   				'type' => ObjectModel :: TYPE_STRING,
				'validate' => 'isString',
				'lang' => true,
			), 
			'logo' =>	array(
				'type' => self::TYPE_STRING, 
				'validate' => 'isCleanHtml',
			),
			'image' =>	array(
				'type' => self::TYPE_STRING, 
				'validate' => 'isCleanHtml',
			),
			'description_title' => array(
				'type' => self::TYPE_HTML, 
				'lang' => true, 
				'validate' => 'isCleanHtml', 
				'size' => 3999999999999				
			),
			'description_content' => array(
				'type' => self::TYPE_HTML, 
				'lang' => true, 
				'validate' => 'isCleanHtml', 
				'size' => 3999999999999
			),
			'description_content_right' => array(
				'type' => self::TYPE_HTML, 
				'lang' => true, 
				'validate' => 'isCleanHtml', 
				'size' => 3999999999999
			),

			'active' => array(
   				'type' 		=> ObjectModel :: TYPE_BOOL, 
			),	
  		),
	);
	
	/*
	*	module function get list
	*
	*/
	public static function gets($id_lang)
	{
		$sql = 'SELECT l.id_hometabfeatures, l.active, l.logo, l.image, ll.content_text, ll.title, ll.description_title, ll.description_content, ll.description_content_right
				FROM '._DB_PREFIX_.'hometabfeatures l
				LEFT JOIN '._DB_PREFIX_.'hometabfeatures_lang ll ON (l.id_hometabfeatures = ll.id_hometabfeatures AND ll.id_lang = '.(int)$id_lang.')';

		return Db::getInstance()->executeS($sql);
	}
	
}