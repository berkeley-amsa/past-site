<?php
/**
 * @version $Id: message.attachments.php 3248 2010-08-19 22:12:31Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/

// Dont allow direct linking
defined ( '_JEXEC' ) or die ();
?>
<div>
	<div class="kmsgattach">
	<?php echo JText::_('COM_KUNENA_ATTACHMENTS');?>
		<ul class="kfile-attach">
		<?php foreach($this->attachments as $attachment) : ?>
			<li>
				<?php echo $attachment->thumblink; ?>
				<span>
					<?php echo $attachment->textLink; ?>
				</span>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
