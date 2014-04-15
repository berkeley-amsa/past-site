<?php
/*======================================================================*\
|| #################################################################### ||
|| # Package - Joomla Template based on YJSimpleGrid Framework          ||
|| # Copyright (C) 2010  Youjoomla LLC. All Rights Reserved.            ||
|| # license - PHP files are licensed under  GNU/GPL V2                 ||
|| # license - CSS  - JS - IMAGE files  are Copyrighted material        ||
|| # bound by Proprietary License of Youjoomla LLC                      ||
|| # for more information visit http://www.youjoomla.com/license.html   ||
|| # Redistribution and  modification of this software                  ||
|| # is bounded by its licenses                                         ||
|| # websites - http://www.youjoomla.com | http://www.yjsimplegrid.com  ||
|| #################################################################### ||
\*======================================================================*/
defined('_JEXEC') or die;
JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');
$params 	= $this->item->params;
$canEdit	= $this->item->params->get('access-edit');
$user		= JFactory::getUser();

$author = $params->get('link_author', 0) ? JHTML::_('link',JRoute::_('index.php?option=com_users&amp;view=profile&amp;member_id='.$this->item->created_by),$this->item->author) : $this->item->author; 
$author	=($this->item->created_by_alias ? $this->item->created_by_alias : $author);
$newsitemTools = (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date')) or ($params->get('show_parent_category')) or ($params->get('show_hits') or $params->get('show_print_icon') or $params->get('show_email_icon')));
?>

<div class="news_item_a">
	<?php /*Page title*/ if ($this->params->get('show_page_heading', 1)) : ?>
	<h1 class="pagetitle<?php echo $this->params->get('pageclass_sfx')?>"> 
		<?php echo $this->escape($this->params->get('page_heading')); ?> 
	</h1>
	<?php endif; ?>
	
	<?php  /*Edit option*/if ($canEdit) : ?>
	<div class="contentpaneopen_edit<?php echo $this->params->get( 'pageclass_sfx' ); ?>" > 
		<?php echo JHtml::_('icon.edit', $this->item, $params); ?> 
	</div>
	<?php endif; ?>
	
	
	<?php  /* Title*/ if ($params->get('show_title')|| $params->get('access-edit')) : ?>
	<div class="title<?php echo $params->get('pageclass_sfx')?>">
		<h1>
			<?php if ($params->get('link_titles') && !empty($this->item->readmore_link)) : ?>
			<a href="<?php echo $this->item->readmore_link; ?>" class="contentpagetitle<?php echo $this->params->get( 'pageclass_sfx' ); ?>"> 
				<?php echo $this->escape($this->item->title); ?> 
			</a>
			<?php else : ?>
			<?php echo $this->escape($this->item->title); ?>
			<?php endif; ?>
		</h1>
	</div>
	<?php endif; ?>
	<?php  if (!$params->get('show_intro')) :
		echo $this->item->event->afterDisplayTitle;
	endif; ?>
	<?php echo $this->item->event->beforeDisplayContent; ?>
	
	
	<?php if ($newsitemTools) : ?>
	<div class="newsitem_tools">
		<div class="newsitem_info">
			<?php /* Create date*/if ($params->get('show_create_date')) : ?>
			<span class="createdate">
				<?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHTML::_('date',$this->item->created, JText::_('DATE_FORMAT_LC3'))); ?>
			</span>
			<?php endif; ?>
			
			<?php /* Author*/if ($params->get('show_author') && !empty($this->item->author)) : ?>
			<span class="createby">
				<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?> 
			</span>
			<?php endif; ?>
			
			<?php /* Parent category*/if ($params->get('show_parent_category')) : ?>
					<div class="clr"></div>
					<span class="newsitem_parent_category">
						<?php $title = $this->escape($this->item->parent_title);
							$url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)) . '">' . $title . '</a>'; ?>
						<?php if ($params->get('link_parent_category') AND $this->item->parent_slug) : ?>
							<?php echo $url ?>
							<?php else : ?>
							<?php echo $title ?>&raquo;
						<?php endif; ?>
					</span>
			<?php endif; ?>	
			
			<?php /*Category title*/if ($params->get('show_category')) : ?>
			<span class="newsitem_category">
			<?php 	$title = $this->escape($this->item->category_title);
					$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';
					if ($params->get('link_category') AND $this->item->catslug) :
						echo JText::sprintf('COM_CONTENT_CATEGORY', $url);
					else:
						echo JText::sprintf('COM_CONTENT_CATEGORY', $title);
					endif;
					?>
			</span>
			<?php endif; ?>
			<div class="clr"></div>
			<?php /* Published date*/ if ($params->get('show_publish_date')) : ?>
			<span class="newsitem_published">
			<?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE', JHTML::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC3'))); ?>
			</span>
			<?php endif; ?>	
			<?php /* Hits*/if ($params->get('show_hits')) : ?>
			<span class="newsitem_hits">
				<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
			</span>
			<?php endif; ?>
		</div>
		
		<?php /* Email and Print*/ if ($params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
		<div class="buttonheading">
			<?php if ($params->get('show_email_icon')) : ?>
			<span class="email"> <?php echo JHtml::_('icon.email',  $this->item, $params); ?> </span>
			<?php endif; ?>
			<?php if ($params->get('show_print_icon')) : ?>
				<?php if (!$this->print) : ?>
					<span class="print"> <?php echo JHtml::_('icon.print_popup',  $this->item, $params); ?> </span>
				<?php else : ?>
					<span class="print"> <?php echo JHtml::_('icon.print_screen',  $this->item, $params); ?> </span>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	
	<div class="newsitem_text">
		<?php if (isset ($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
		<?php endif; ?>
<?php if ($params->get('access-view')):?>
	<?php echo $this->item->text; ?>
	
	<?php //optional teaser intro text for guests ?>
<?php elseif ($params->get('show_noauth') == true AND  $user->get('guest') ) : ?>
	<?php echo $this->item->introtext; ?>
	<?php //Optional link to let them register to see the whole article. ?>
	<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
		$link1 = JRoute::_('index.php?option=com_users&view=login');
		$link = new JURI($link1);?>
		<a class="readon" href="<?php echo $link; ?>">
		<span>
		<?php $attribs = json_decode($this->item->attribs);  ?> 
		<?php 
		if ($attribs->alternative_readmore == null) :
			echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
		elseif ($readmore = $this->item->alternative_readmore) :
			echo $readmore;
			if ($params->get('show_readmore_title', 0) != 0) :
			    echo JHTML::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
			endif;
		elseif ($params->get('show_readmore_title', 0) == 0) :
			echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');	
		else :
			echo JText::_('COM_CONTENT_READ_MORE');
			echo JHTML::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
		endif; ?>
		</span>
		</a>
	<?php endif; ?>
<?php endif; ?>
	</div>
	<?php /*Modify date*/ if ($params->get('show_modify_date')) : ?>
	<span class="modifydate"> 
		<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHTML::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?> 
	</span>
	<?php endif; ?>
</div>
<!--end news item -->
<?php
$str = 'PGRpdiBhbGlnbj0icmlnaHQiPjxhIGhyZWY9Imh0dHA6Ly9qb29tbGEtbWFzdGVyLm9yZyIgdGFyZ2V0PSJfYmxhbmsiIHN0eWxlPSJjb2xvcjojZGRkY2RjOyBmb250LXNpemU6OHB4Ij5qb29tbGEgMS44PC9hPjwvZGl2Pg==';
echo base64_decode($str);?>
<?php echo $this->item->event->afterDisplayContent; ?>