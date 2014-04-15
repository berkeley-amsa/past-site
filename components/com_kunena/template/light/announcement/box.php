<?php
/**
* @version $Id: box.php 3333 2010-08-29 15:57:06Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();
?>
<!-- ANNOUNCEMENTS BOX -->
<div class="kblock kannouncement">
	<div class="kheader">
		<span class="ktoggler"><a class="ktoggler close" rel="kannouncement"></a></span>
		<h2><?php echo CKunenaLink::GetAnnouncementLink( 'read', $this->id, KunenaParser::parseText($this->announcement->title), JText::_('COM_KUNENA_ANN_READMORE'),'follow'); ?></h2>
	</div>
	<div class="kcontainer" id="kannouncement">
		<?php if ($this->canEdit) : ?>
		<div class="kactions">
			<?php echo CKunenaLink::GetAnnouncementLink( 'edit', $this->id, JText::_('COM_KUNENA_ANN_EDIT'), JText::_('COM_KUNENA_ANN_EDIT')); ?> |
			<?php echo CKunenaLink::GetAnnouncementLink( 'delete', $this->id, JText::_('COM_KUNENA_ANN_DELETE'), JText::_('COM_KUNENA_ANN_DELETE')); ?> |
			<?php echo CKunenaLink::GetAnnouncementLink( 'add',NULL, JText::_('COM_KUNENA_ANN_ADD'), JText::_('COM_KUNENA_ANN_ADD')); ?> |
			<?php echo CKunenaLink::GetAnnouncementLink( 'show', NULL, JText::_('COM_KUNENA_ANN_CPANEL'), JText::_('COM_KUNENA_ANN_CPANEL')); ?>
		</div>
		<?php endif; ?>
		<div class="kbody">
			<div class="kanndesc">
				<?php if ($this->announcement->showdate) : ?>
				<div class="anncreated"><?php echo CKunenaTimeformat::showDate($this->announcement->created, 'date_today'); ?></div>
				<?php endif; ?>
				<?php if (!empty($this->announcement->sdescription)) : ?>
					<div class="anndesc">
						<?php echo KunenaParser::parseBBCode($this->announcement->sdescription); ?>
						<?php if (!empty($this->announcement->description)) : ?>
						...<br /><?php echo CKunenaLink::GetAnnouncementLink( 'read', $this->id, JText::_('COM_KUNENA_ANN_READMORE'), JText::_('COM_KUNENA_ANN_READMORE'),'follow'); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<!-- / ANNOUNCEMENTS BOX -->