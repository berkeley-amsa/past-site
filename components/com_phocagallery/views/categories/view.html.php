<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view');
jimport( 'joomla.filesystem.file' );
phocagalleryimport('phocagallery.access.access');
phocagalleryimport('phocagallery.path.path');
phocagalleryimport('phocagallery.file.file');
phocagalleryimport('phocagallery.render.renderinfo');
phocagalleryimport('phocagallery.picasa.picasa');
phocagalleryimport('phocagallery.image.imagefront');
phocagalleryimport('phocagallery.ordering.ordering');

class PhocaGalleryViewCategories extends JView
{
	public 		$tmpl;
	protected 	$params;
	
	public function display($tpl = null) {		
		
		$app 			= JFactory::getApplication();
		$user 			= &JFactory::getUser();
		$uri 			= &JFactory::getURI();
		$path			= &PhocaGalleryPath::getPath();
		$this->params	= $app->getParams();
		$this->tmplGeo	= array();
		
		$this->setLibraries();
		
		$this->tmpl['categories_description'] 	= $this->params->get( 'categories_description', '' );
		$image_categories_size 					= $this->params->get( 'image_categories_size', 4 );
		$medium_image_width 					= (int)$this->params->get( 'medium_image_width', 100 ) + 18;
		$medium_image_height 					= (int)$this->params->get( 'medium_image_height', 100 ) + 18;
		$small_image_width 						= (int)$this->params->get( 'small_image_width', 50 ) + 18;
		$small_image_height 					= (int)$this->params->get( 'small_image_height', 50 ) + 18;
		$this->tmpl['phocagallerywidth'] 		= $this->params->get( 'phocagallery_width', '' );
		$this->tmpl['categoriescolumns'] 		= $this->params->get( 'categories_columns', 1 );
		$this->tmpl['displayrating']			= $this->params->get( 'display_rating', 0 );
		$this->tmpl['phocagallerycenter']		= $this->params->get( 'phocagallery_center', '');
		$this->tmpl['categoriesimageordering']	= $this->params->get( 'categories_image_ordering', 9 );
		$this->tmpl['displayimagecategories'] 	= $this->params->get( 'display_image_categories', 1 );
		$display_categories_geotagging 			= $this->params->get( 'display_categories_geotagging', 0 );
		$display_access_category 				= $this->params->get( 'display_access_category', 1 );
		$display_empty_categories				= $this->params->get( 'display_empty_categories', 0 );
		$hideCatArray							= explode( ',', trim( $this->params->get( 'hide_categories', '' ) ) );
		$showCatArray    						= explode( ',', trim( $this->params->get( 'show_categories', '' ) ) );
		$this->tmpl['equalpercentagewidth']		= $this->params->get( 'equal_percentage_width');
		$this->tmpl['categoriesdisplayavatar']	= $this->params->get( 'categories_display_avatar');
		$this->tmpl['categoriesboxwidth']		= $this->params->get( 'categories_box_width');
		
		// Correct Picasa Images - get Info
		switch($image_categories_size) {
			// medium
			case 1:
			case 5:
				$this->tmpl['picasa_correct_width']	= (int)$this->params->get( 'medium_image_width', 100 );	
				$this->tmpl['picasa_correct_height']	= (int)$this->params->get( 'medium_image_height', 100 );
			break;
			
			case 0:
			case 4:
			default:
				$this->tmpl['picasa_correct_width']	= (int)$this->params->get( 'small_image_width', 50 );	
				$this->tmpl['picasa_correct_height']	= (int)$this->params->get( 'small_image_height', 50 );
			break;
		}
		
		// - - - - - - - - - - - - - - - 
	
		// Get background for the image		
		$catImg = PhocaGalleryImageFront::getCategoriesImageBackground($image_categories_size, $small_image_width, $small_image_height,  $medium_image_height, $medium_image_width);
		
		$this->tmpl['imagebg'] 		= $catImg->image;
		$this->tmpl['imagewidth'] 	= $catImg->width;
		
		//$total					= $this->get('total');
		//$this->tmpl['pagination']	= &$this->get('pagination');
		
		// Image next to Category in Categories View is ordered by Random as default
		$categoriesImageOrdering = PhocaGalleryOrdering::getOrderingString($this->tmpl['categoriesimageordering']);
		
		// MODEL
		$model					= &$this->getModel();
		$this->tmpl['ordering']	= &$model->getOrdering();
		$items					= $this->get('data');
		
		
		// Add link and unset the categories which user cannot see (if it is enabled in params)
		// If it will be unset while access view, we must sort the keys from category array - ACCESS
		$unSet = 0;
		foreach ($items as $key => $item) {

			// Unset empty categories if it is set
			if ($display_empty_categories == 0) {
				if($items[$key]->numlinks < 1) {
					unset($items[$key]);
					$unSet 		= 1;
					continue;
				}
			}
			 
			// Set only selected category ID    
			if (!empty($showCatArray[0]) && is_array($showCatArray)) {
				$unSetHCA = 0;
         
				foreach ($showCatArray as $valueHCA) {
            
					if((int)trim($valueHCA) == $items[$key]->id) {
						$unSetHCA 	= 0;
						$unSet 		= 0;
						break;
					} else {
						$unSetHCA 	= 1;
						$unSet 		= 1;
                    }
                }
				if ($unSetHCA == 1) {
					unset($items[$key]);
					continue;
				}
			}

			
			// Unset hidden category
			if (!empty($hideCatArray) && is_array($hideCatArray)) {
				$unSetHCA = 0;
				foreach ($hideCatArray as $valueHCA) {
					if((int)trim($valueHCA) == $items[$key]->id) {
						unset($items[$key]);
						$unSet 		= 1;
						$unSetHCA 	= 1;
						break;
					}
				}
				if ($unSetHCA == 1) {
					continue;
				}
			}
		
			
			// Link
			//$items[$key]->link	= JRoute::_('index.php?option=com_phocagallery&view=category&id='. $item->slug.'&Itemid='. JRequest::getVar('Itemid', 0, '', 'int') ); 
			$items[$key]->link = PhocaGalleryRoute::getCategoryRoute($item->id, $item->alias);
			
			// USER RIGHT - ACCESS - - - - -
			// First Check - check if we can display category
			$rightDisplay	= 1;
			if (!empty($items[$key])) {
			
				$rightDisplay = PhocaGalleryAccess::getUserRight('accessuserid', $items[$key]->accessuserid, $items[$key]->access, $user->authorisedLevels(), $user->get('id', 0), $display_access_category);
			}
			// Second Check - if we can display hidden category, set Key icon for them
			//                if we don't have access right to see them
			// Display Key Icon (in case we want to display unaccessable categories in list view)
			$rightDisplayKey  = 1;
		
			if ($display_access_category == 1) {
				// we simulate that we want not to display unaccessable categories
				// so if we get rightDisplayKey = 0 then the key will be displayed
				if (!empty($items[$key])) {
					$rightDisplayKey = PhocaGalleryAccess::getUserRight('accessuserid', $items[$key]->accessuserid, $items[$key]->access, $user->authorisedLevels(), $user->get('id', 0), 0); // 0 - simulation
				}
			}
			
			// Is Ext Image Album?
			$extCategory = PhocaGalleryImage::isExtImage($items[$key]->extid, $items[$key]->extfbcatid);
			
			// DISPLAY AVATAR, IMAGE(ordered), IMAGE(not ordered, not recursive) OR FOLDER ICON
			$displayAvatar = 0;
			if($this->tmpl['categoriesdisplayavatar'] == 1 && isset($items[$key]->avatar) && $items[$key]->avatar !='' && $items[$key]->avatarapproved == 1 && $items[$key]->avatarpublished == 1) {
				$sizeString = PhocaGalleryImageFront::getSizeString($image_categories_size);
				$pathAvatarAbs	= $path->avatar_abs  .'thumbs'.DS.'phoca_thumb_'.$sizeString.'_'. $items[$key]->avatar;
				$pathAvatarRel	= $path->avatar_rel . 'thumbs/phoca_thumb_'.$sizeString.'_'. $items[$key]->avatar;
				if (JFile::exists($pathAvatarAbs)){
					$items[$key]->linkthumbnailpath	=  $pathAvatarRel;
					$displayAvatar = 1;
				}
			}
			
			if ($displayAvatar == 0) {	
				if ($extCategory) {
					if ($this->tmpl['categoriesimageordering'] != 10) {
						$imagePic		= PhocaGalleryImageFront::getRandomImageRecursive($items[$key]->id, $categoriesImageOrdering, 1);
						$fileThumbnail	= PhocaGalleryImageFront::displayCategoriesExtImgOrFolder($imagePic->exts,$imagePic->extm, $imagePic->extw,$imagePic->exth, $image_categories_size, $rightDisplayKey);
					} else {	
						$fileThumbnail		= PhocaGalleryImageFront::displayCategoriesExtImgOrFolder($items[$key]->exts,$items[$key]->extm, $items[$key]->extw, $items[$key]->exth, $image_categories_size, $rightDisplayKey);
					}

					$items[$key]->linkthumbnailpath	= $fileThumbnail->rel;
					$items[$key]->extw				= $fileThumbnail->extw;
					$items[$key]->exth				= $fileThumbnail->exth;
					$items[$key]->extpic			= $fileThumbnail->extpic;
				} else {
					if ($this->tmpl['categoriesimageordering'] != 10) {
						$items[$key]->filename	= PhocaGalleryImageFront::getRandomImageRecursive($items[$key]->id, $categoriesImageOrdering);
					}
					$fileThumbnail	= PhocaGalleryImageFront::displayCategoriesImageOrFolder($items[$key]->filename, $image_categories_size, $rightDisplayKey);
					$items[$key]->linkthumbnailpath	= $fileThumbnail->rel;
					
				}
			}
		
			if ($rightDisplay == 0) {
				unset($items[$key]);
				$unSet = 1;
			}
			// - - - - - - - - - - - - - - - 	
			
		}
		
		$this->tmpl['mtb'] = PhocaGalleryRenderInfo::getPhocaIc((int)$this->params->get( 'display_phoca_info', 1 ));
		
		// ACCESS - - - - - - 
		// In case we unset some category from the list, we must sort the array new
		if ($unSet == 1) {
			$items = array_values($items);
		}
		// - - - - - - - - - - - - - - - -

		// Do Pagination - we can do it after reducing all unneeded items, not before
		$totalCount 		= count($items);
		$model->setTotal($totalCount);
		$this->tmpl['pagination']	= &$this->get('pagination');
		$items 				= array_slice($items,(int)$this->tmpl['pagination']->limitstart, (int)$this->tmpl['pagination']->limit);
		// - - - - - - - - - - - - - - - -

		// Display Image of Categories Description
	/*	if ($this->params->get('image') != -1) {
			$attribs['align']	= $this->params->get('image_align');
			$attribs['hspace']	= 6;
			// Use the static HTML library to build the image tag
			$this->tmpl['image'] 		= JHTML::_('image', 'images/stories/'.$this->params->get('image'), '', $attribs);
		}*/
		
		// ACTION
		$this->tmpl['action']	= $uri->toString();
		
		// ASSIGN
		$this->assignRef('tmpl',		$this->tmpl);
		$this->assignRef('params',		$this->params);
		$this->assignRef('categories',	$items);
		
		$this->_prepareDocument();
		
		if ($display_categories_geotagging == 1) {
		
			// PARAMS - - - - - - - - - -
			$this->tmplGeo['categorieslng'] 		= $this->params->get( 'categories_lng', '' );
			$this->tmplGeo['categorieslat'] 		= $this->params->get( 'categories_lat', '' );
			$this->tmplGeo['categorieszoom'] 		= $this->params->get( 'categories_zoom', 2 );
			$this->tmplGeo['googlemapsapikey'] 	= $this->params->get( 'google_maps_api_key', '' );
			$this->tmplGeo['categoriesmapwidth'] 	= $this->params->get( 'categories_map_width', '' );
			$this->tmplGeo['categoriesmapheight'] = $this->params->get( 'categorires_map_height', 500 );
			// - - - - - - - - - - - - - - -
			
			// if no lng and lat will be added, Phoca Gallery will try to find it in categories
			if ($this->tmplGeo['categorieslat'] == '' || $this->tmplGeo['categorieslng'] == '') {
				phocagalleryimport('phocagallery.geo.geo');
				$latLng = PhocaGalleryGeo::findLatLngFromCategory($items);
				$this->tmplGeo['categorieslng'] = $latLng['lng'];
				$this->tmplGeo['categorieslat'] = $latLng['lat'];
			}
		
			$this->assignRef('tmplGeo',	$this->tmplGeo);
			parent::display('map');
		} else {
			parent::display($tpl);
		}
	}
	
	protected function _prepareDocument() {
		
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway 	= $app->getPathway();
		//$this->params		= &$app->getParams();
		$title 		= null;
		
		$this->tmpl['gallerymetakey'] 		= $this->params->get( 'gallery_metakey', '' );
		$this->tmpl['gallerymetadesc'] 		= $this->params->get( 'gallery_metadesc', '' );
		

		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		} else if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}
		$this->document->setTitle($title);

		if (empty($title)) {
			$title = $this->item->title;
		}
		$this->document->setTitle($title);
		
		if ($this->tmpl['gallerymetadesc'] != '') {
			$this->document->setDescription($this->tmpl['gallerymetadesc']);
		} else if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		} 

		if ($this->tmpl['gallerymetakey'] != '') {
			$this->document->setMetadata('keywords', $this->tmpl['gallerymetakey']);
		} else if ($this->params->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
		}

		if ($app->getCfg('MetaTitle') == '1' && $this->params->get('menupage_title', '')) {
			$this->document->setMetaData('title', $this->params->get('page_title', ''));
		}

		/*if ($app->getCfg('MetaAuthor') == '1') {
			$this->document->setMetaData('author', $this->item->author);
		}

		/*$mdata = $this->item->metadata->toArray();
		foreach ($mdata as $k => $v) {
			if ($v) {
				$this->document->setMetadata($k, $v);
			}
		}*/
		
		// Breadcrumbs TODO (Add the whole tree)
		/*if (isset($this->category[0]->parentid)) {
			if ($this->category[0]->parentid == 1) {
			} else if ($this->category[0]->parentid > 0) {
				$pathway->addItem($this->category[0]->parenttitle, JRoute::_(PhocaDocumentationHelperRoute::getCategoryRoute($this->category[0]->parentid, $this->category[0]->parentalias)));
			}
		}

		if (!empty($this->category[0]->title)) {
			$pathway->addItem($this->category[0]->title);
		}*/
	}

	protected function setLibraries() {
		
		$document	= &JFactory::getDocument();
		
		// Libraries
		$library 				= &PhocaGalleryLibrary::getLibrary();
		$libraries['pg-css-ie'] = $library->getLibrary('pg-css-ie');
		
		// CSS for IE 8
		if ( $libraries['pg-css-ie']->value == 0 ) {
			$document->addCustomTag("<!--[if lt IE 8]>\n<link rel=\"stylesheet\" href=\""
			.JURI::base(true)
			."/components/com_phocagallery/assets/phocagalleryieall.css\" type=\"text/css\" />\n<![endif]-->");
			$library->setLibrary('pg-css-ie', 1);
		}
		
		// CSS
		JHTML::stylesheet('components/com_phocagallery/assets/phocagallery.css' );
	}
}
?>