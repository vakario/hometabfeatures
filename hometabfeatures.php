<?php
/*
 *
 * @author Florent Glauda <skeul73@live.fr>
 * 
 */

require_once _PS_MODULE_DIR_ . 'hometabfeatures/models/featuresmod.php';

class homeTabFeatures extends Module
{
	private $_html = '';
	protected $max_image_size = 1048576;
	
	
	public function __construct()
	{
		$this->name 			= 'hometabfeatures';
		$this->tab 				= 'front_office_features';
		$this->version			= '1.0';
		$this->author			= 'Vakario';
		$this->displayName		= $this->l('Home Tab Features');
		$this->description		= $this->l('Module d\'affichage de blocs de contenus dynamique sur votre page d\'acceuil.');
		$this->confirmUninstall = $this->l('Etes-vous sur de vouloir désinstaller ce module?');
		$this->context 			= Context::getContext();
		$this->bootstrap 		= true;
											
		$this->context->smarty->assign('module_hometab', $this->name);
		
		parent::__construct();

	}

	/**
	 *   module install
	 */
	public function install()
	{
		/* Adds Module */
			return parent :: install() &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('displayHome') &&
			$this->createTables();

	}
	/**
	*	module hook Header
	*
	*/
	public function hookdisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'css/hometabfeatures.css', 'all');
		$this->context->controller->addJS($this->_path.'js/hometabfeatures.js', 'all');
	}
	
	/**
	*	module hook Home
	*
	*/
	public function hookdisplayHome($params) 
	{
		$features = FeaturesMod::gets((int)$this->context->language->id);
		$this->context->smarty->assign('features', $features);
		return $this->display(__FILE__, 'hometabfeatures.tpl');
	}
	
	/**
	*	module hook Top
	*
	*/
	public function hookdisplayTop($params) 
	{
		return $this->hookdisplayHome($params);
	}
	
	
	/**
	*	module uninstall
	*
	*/
	public function uninstall()
	{
		return parent::uninstall() &&
		$this->uninstallImage() &&
		$this->deleteTables();
	}
	
	/**
	*	module configuration
	*
	*/
	public function getContent()
	{	
		// init
		$id_lang = (int)Context::getContext()->language->id;
		$languages = $this->context->controller->getLanguages();
		$default_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$f='';
		
		$sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.'hometabfeatures` WHERE `active` = 1';
		$toti =  Db::getInstance()->executeS($sql);
		/*$this->_html .= $toti[0]['COUNT(*)'];*/
		if (Tools::isSubmit('submitFeatures')) // si ajout d'un contenu
		{
			foreach ($languages as $key => $val)
			{
					$cont[$val['id_lang']] = Tools::getValue('content_text_'.(int)$val['id_lang']);
					$titl[$val['id_lang']] = Tools::getValue('title_'.(int)$val['id_lang']);
					$desc_titl[$val['id_lang']] = Tools::getValue('description_title_'.(int)$val['id_lang']);
					$desc_cont[$val['id_lang']] = Tools::getValue('description_content_'.(int)$val['id_lang']);
					$desc_cont_right[$val['id_lang']] = Tools::getValue('description_content_right_'.(int)$val['id_lang']);
			}
			
			$count_cont = count($cont);
			$count_titl = count($titl);
			$count_desc_cont = count($desc_cont);
			$count_desc_titl = count($desc_titl);
			$count_desc_cont_right = count($desc_cont_right);
			
			
			if ($count_cont && $count_titl && $count_desc_cont && $count_desc_titl && $count_desc_cont_right)
			{
					$logo = !empty($_FILES['logo']['name'])?$this->uploadLogo($_FILES['logo']):'';
					$image = !empty($_FILES['image']['name'])?$this->uploadImage($_FILES['image']):'';
					$f = new FeaturesMod();
					$f->title 						= $titl;
					$f->content_text 				= $cont;
					$f->description_title			= $desc_titl;
					$f->description_content			= $desc_cont;
					$f->description_content_right	= $desc_cont_right;
					$f->logo						= $logo;
					$f->image						= $image;
					$f->active						= $toti[0]['COUNT(*)']<3?Tools::getValue('active'):0;
					if($toti[0]['COUNT(*)']>=3)
					$this->_html .= $this->displayError($this->l('Votre contenu ne sera pas activé car vous avez déja trois contenus actifs.'));
					if($logo=='' || $image=='')
					$this->_html .= $this->displayError($this->l('Attention vous n\'avez pas ajouté toutes les images, votre contenu ne sera pas affiché correctement!!!'));
					$f->add();
					$this->_html .= $this->displayConfirmation($this->l('Le contenu à bien été ajouté.'));
			}
			else
			{
				$this->_html .=$this->displayError( $this->l('Veuillez remplir tous les champs.') );
			}
		}
		elseif(Tools::isSubmit('updatehometabfeatures'))  // si modif d'un contenu
		{
			$id_hometabfeatures = (int)Tools::getValue('id_hometabfeatures', 0);
			$logo = Db::getInstance()->getValue('SELECT logo FROM `'._DB_PREFIX_.'hometabfeatures` WHERE id_hometabfeatures = '.$id_hometabfeatures);
			$image = Db::getInstance()->getValue('SELECT image FROM `'._DB_PREFIX_.'hometabfeatures` WHERE id_hometabfeatures = '.$id_hometabfeatures);
			
			if(Tools::isSubmit('updateFeatures'))
			{
				$f= new FeaturesMod($id_hometabfeatures);
				if(empty($_FILES['logo']['name']))
				{
					$f->logo =$logo;
				}
				else
				{
					$log = $this->uploadLogo($_FILES['logo']);
					$f->logo =$log;
					$logo !='' ? $this->deleteImage($logo) : false;
				}
				if(empty($_FILES['image']['name']))
				{
					$f->image =$image;
				}
				else
				{
					$imag = $this->uploadImage($_FILES['image']);
					$f->image =$imag;
					$image !='' ? $this->deleteImage($image) : false;
				}
				foreach ($languages as $key => $val)
				{
					$cont[$val['id_lang']] = Tools::getValue('content_text_'.(int)$val['id_lang']);
					$titl[$val['id_lang']] = Tools::getValue('title_'.(int)$val['id_lang']);
					$desc_titl[$val['id_lang']] = Tools::getValue('description_title_'.(int)$val['id_lang']);
					$desc_cont[$val['id_lang']] = Tools::getValue('description_content_'.(int)$val['id_lang']);
					$desc_cont_right[$val['id_lang']] = Tools::getValue('description_content_right_'.(int)$val['id_lang']);
				}
				
				$f->title 						= $titl;
				$f->content_text 				= $cont;
				$f->description_title			= $desc_titl;
				$f->description_content			= $desc_cont;
				$f->description_content_right	= $desc_cont_right;
				
				if($f->active == 0)
				{
					$f->active						= $toti[0]['COUNT(*)']<3?Tools::getValue('active'):0; 
					if(Tools::getValue('active')==1 && $toti[0]['COUNT(*)']>=3)
					$this->_html .= $this->displayError($this->l('Votre contenu ne sera pas activé car vous avez déja trois contenus actifs.'));
				}
				else
				$f->active						= Tools::getValue('active');
				
				if($logo=='' || $image=='')
				$this->_html .= $this->displayError($this->l('Attention vous n\'avez pas ajouté toutes les images, votre contenu ne sera pas affiché correctement!!!'));
				$f->update();
				$this->_html .= $this->displayConfirmation($this->l('Le contenu à bien été mis à jour.'));
			}
			
		}
		elseif(Tools::isSubmit('deletehometabfeatures'))  // si suppression d'un contenu
		{
			$id_hometabfeatures = (int)Tools::getValue('id_hometabfeatures', 0);
			$logo = Db::getInstance()->getValue('SELECT logo FROM `'._DB_PREFIX_.'hometabfeatures` WHERE id_hometabfeatures = '.$id_hometabfeatures);
			$image = Db::getInstance()->getValue('SELECT image FROM `'._DB_PREFIX_.'hometabfeatures` WHERE id_hometabfeatures = '.$id_hometabfeatures);
			$f= new FeaturesMod($id_hometabfeatures);
			$f->delete();
			$logo !='' ? $this->deleteImage($logo) : false;
			$image !='' ? $this->deleteImage($image) : false;
		}
		
		$this->_html .= $this->renderList();
		$this->_html .= $this->renderAddForm();
		return $this->_html;
	}
	
	/**
	*   formulaire add image
	*
	*/
	public function renderAddForm()
	{
		$id_hometabfeatures = (int)Tools::getValue('id_hometabfeatures', 0);
		$f= new FeaturesMod($id_hometabfeatures);
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => (Tools::getIsset('updatehometabfeatures') && !Tools::getValue('updatehometabfeatures')) ? $this->l('Modifier un contenu') : $this->l('Ajouter un contenu'),
					'icon' 	=> 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Actif'),
						'name' => 'active',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
					array(
						'type'			=> 'file',
						'label'			=> $this->l('Choisissez un logo'),
						'name' 			=> 'logo',
						'display_image'	=> true,
						'image'			=> (Tools::getIsset('updatehometabfeatures') && !Tools::getValue('updatehometabfeatures')) ? '<img src="../modules/'.$this->name.'/img/'.$f->logo.'">' : false,
						'desc' 			=> $this->l('Le format ".png" est conseillé afin d\'éviter les superpositions de fond.'),
						'hint' 			=> $this->l('Ajouter le logo correspondant à votre contenu.'),
						
					),
					array(
						'type' 		=> 'text',
						'label'		=> $this->l('Titre'),
						'name' 		=> 'title',
						'required' 	=> true,
						'lang' 		=> true,
						'size'		=> 60,
						'desc' 		=> $this->l('ATTENTION! Le nombre de caractères est limité à 60. Si vous dépassez cette limite votre titre sera coupé. '),
					),
					array(
						'type' 		=> 'text',
						'label'		=> $this->l('Texte de description'),
						'name' 		=> 'content_text',
						'lang' 		=> true,
						'required' 	=> true,
						'size'		=> 200,
						'desc' 		=> $this->l('ATTENTION! Le nombre de caractères est limité à 200. Si vous dépassez cette limite votre description sera coupée. '),
					),
					array(
						'type' 			=> 'textarea',
						'label' 		=> $this->l('Bloc d\'introduction de contenu'),
						'name' 			=> 'description_title',
						'autoload_rte' 	=> true,
						'required' 		=> true,
						'lang' 			=> true,
					),
					array(
					'type'			=> 'file',
					'label' 		=> $this->l('Choisissez une image'),
					'name' 			=> 'image',
					'display_image' => true,
					'image' 		=> (Tools::getIsset('updatehometabfeatures') && !Tools::getValue('updatehometabfeatures')) ? '<img src="../modules/'.$this->name.'/img/'.$f->image.'">' : false,
					'hint' 			=> $this->l('Ajouter une image à votre contenu.'),
					),
					array(
						'type' 			=> 'textarea',
						'label' 		=> $this->l('Bloc de contenu gauche'),
						'name'			=> 'description_content',
						'autoload_rte' 	=> true,
						'required' 		=> true,
						'lang' 			=> true,
					),
					array(
						'type' 			=> 'textarea',
						'label' 		=> $this->l('Bloc de contenu droit'),
						'name'			=> 'description_content_right',
						'autoload_rte' 	=> true,
						'required' 		=> true,
						'lang' 			=> true,
					),
				),
				'submit' => array(
					'title' => $this->l('Add'),
					'name' => 'submitFeatures',
				)
			),
		);


		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		if (Tools::getIsset('updatehometabfeatures') && !Tools::getValue('updatehometabfeatures'))
			$fields_form['form']['submit'] = array(
				'name' => 'updatehometabfeatures',
				'title' => $this->l('Update')
			);
		if (Tools::isSubmit('updatehometabfeatures'))
		{			
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'updateFeatures');
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_hometabfeatures');			
		}
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'img/'
		);

		$helper->override_folder = '/';

		return $helper->generateForm(array($fields_form));
	}
	
	
	/*
	*	module get config values
	*
	*/
	public function getAddFieldsValues()
	{
		$fields = array();
		$languages = Language::getLanguages(false);
		
		if (Tools::getIsset('updatehometabfeatures') && !Tools::getValue('updatehometabfeatures'))
		{
			$id_hometabfeatures = (int)Tools::getValue('id_hometabfeatures', 0);
			$logo = Db::getInstance()->getValue('SELECT logo FROM `'._DB_PREFIX_.'hometabfeatures` WHERE id_hometabfeatures = '.$id_hometabfeatures);
			$f= new FeaturesMod($id_hometabfeatures);
			
			$fields['updateFeatures'] 				= '';
			$fields['id_hometabfeatures'] 			= $f->id_hometabfeatures;
			$fields['title']						= $f->title;
			$fields['content_text']					= $f->content_text;	
			$fields['description_title'] 			= $f->description_title;
			$fields['description_content'] 			= $f->description_content;
			$fields['description_content_right'] 	= $f->description_content_right;
			$fields['active']						= $f->active;
			
		}
		
		else
		{
			foreach ($languages as $lang)
			{
				$fields['title'][$lang['id_lang']] = '';
				$fields['content_text'][$lang['id_lang']] = '';
				$fields['description_title'][$lang['id_lang']] = '';
				$fields['description_content'][$lang['id_lang']] = '';
				$fields['description_content_right'][$lang['id_lang']] = '';
							
			}
			
			$fields['active'] = 0;
			$fields['updateFeatures'] = '';
			$fields['id_hometabfeatures'] = 0;
			$this->_html == '';
		}

		return $fields;
	}
	
	/*
	*	module get list
	*
	*/
	public function renderList()
	{
		
		$links = FeaturesMod::gets((int)$this->context->language->id);
		$fields_list = array(
			
			'id_hometabfeatures' => array(
				'title' => $this->l('ID'),
				'type' 	=> 'text',
			),
			'title' => array(
				'title' => $this->l('Titre'),
				'type' 	=> 'text',
			),
			'content_text' => array(
				'title' 	=> $this->l('Contenu'),
				'type' 		=> 'text',
				'maxlength' => 100,
			),
			'active' => array(
				'title' => $this->l('Actif'),
				'type' => 'bool',
				'align' => 'center',
				'active' => 'status',
			)
			
		);
		
		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_hometabfeatures';
		$helper->table = 'hometabfeatures';
		$helper->actions = array('edit', 'delete');
		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->title = $this->l('Liste de contenu');
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
				
		return $helper->generateList($links, $fields_list);
	}
	
	/**
	*	module create tables in DB
	*
	*/
	protected function createTables()
	{
		/* hometabfeatures config*/
		$res = (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'hometabfeatures` (
				`id_hometabfeatures` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`logo` varchar(255) NOT NULL,
				`image` varchar(255) NOT NULL,
				`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
				PRIMARY KEY (`id_hometabfeatures`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');
		
		/* hometabfeatures lang configuration */
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'hometabfeatures_lang` (
			  `id_hometabfeatures` int(10) unsigned NOT NULL,
			  `id_lang` int(10) unsigned NOT NULL,
			  `title` varchar(255) NOT NULL,
			  `content_text` varchar(255) NOT NULL,
			  `description_title` longtext NOT NULL,
			  `description_content` longtext NOT NULL,
			  `description_content_right` longtext NOT NULL,
			  PRIMARY KEY (`id_hometabfeatures`,`id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');

		return $res;
	}
	
	/*
	*	module remove tables from DB
	*
	*/
	protected function deleteTables()
	{
		
		return Db::getInstance()->execute('
			DROP TABLE IF EXISTS `'._DB_PREFIX_.'hometabfeatures`, `'._DB_PREFIX_.'hometabfeatures_lang`;
		');
	}
	
	/*
	*	module upload logo
	*
	*/
	protected function uploadLogo($image)
	{
		$res = false;
		if (is_array($image) && (ImageManager::validateUpload($image, $this->max_image_size) === false) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($image['tmp_name'], $tmp_name))
		{
			$salt = sha1(microtime());
			$img_name = $salt.'_'.$image['name'];
			if (ImageManager::resize($tmp_name, dirname(__FILE__).'/img/'.$img_name, 300, 200))
				$res = true;
		}

		if (!$res)
		{
			$this->_html .=$this->displayError( $this->l('Erreur lors du téléchargement de l\'image. Veuillez vérifier que la taille de l\'image est correcte et réessayez.') );
			return false;
		}

		return $img_name;
	}
	
	/*
	*	module upload image
	*
	*/
	protected function uploadImage($image)
	{
		$res = false;
		if (is_array($image) && (ImageManager::validateUpload($image, $this->max_image_size) === false) && ($tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS')) && move_uploaded_file($image['tmp_name'], $tmp_name))
		{
			$salt = sha1(microtime());
			$img_name = $salt.'_'.$image['name'];
			if (ImageManager::resize($tmp_name, dirname(__FILE__).'/img/'.$img_name, 540, 400))
				$res = true;
		}

		if (!$res)
		{
			$this->_html .=$this->displayError( $this->l('Erreur lors du téléchargement de l\'image. Veuillez vérifier que la taille de l\'image est correcte et réessayez.') );
			return false;
		}

		return $img_name;
	}
	
	/*
	*	module remove image from dir
	*
	*/
	protected function deleteImage($image)
	{
		$file_name = dirname(__FILE__).'/img/'.$image;

		if (realpath(dirname($file_name)) != realpath(dirname(__FILE__).'/img/'))
			Tools::dieOrLog(sprintf('Could not find upload directory'));

		if ($image != '' && is_file($file_name) && !strpos($file_name, 'banner-img') && !strpos($file_name, 'bg-theme') && !strpos($file_name, 'footer-bg'))
			unlink($file_name);
	}
	
	protected function uninstallImage()
	{
		$logo = Db::getInstance()->executeS('SELECT logo FROM `'._DB_PREFIX_.'hometabfeatures`');
		$image = Db::getInstance()->executeS('SELECT image FROM `'._DB_PREFIX_.'hometabfeatures`');
		foreach($logo as $log)
		deleteImage($log);
		
		foreach($image as $img)
		deleteImage($img);
	}
	
}
