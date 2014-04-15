<?php
/**
* @version $Id: read.php 2979 2010-07-10 14:03:27Z mahagr $
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
**/
// Dont allow direct linking
defined( '_JEXEC' ) or die();

$document = JFactory::getDocument();
$document->setTitle(JText::_('COM_KUNENA_ANN_ANNOUNCEMENTS') . ' - ' . $this->escape($this->config->board_title));
?>
<div class="kblock kannouncement">
	<div class="kheader">
		<h1><?php echo KunenaParser::parseText($this->announcement->title); ?></h1>
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
				<?php if ($this->announcement->showdate > 0) : ?>
				<div class="anncreated" title="<?php echo CKunenaTimeformat::showDate($this->announcement->created, 'ago'); ?>">
					<?php echo CKunenaTimeformat::showDate($this->announcement->created, 'date_today'); ?>
				</div>
				<?php endif; ?>
				<div class="anndesc"><?php echo !empty($this->announcement->description) ? KunenaParser::parseBBCode($this->announcement->description) : KunenaParser::parseBBCode($this->announcement->sdescription); ?></div>
			</div>
		</div>
	</div>
</div>