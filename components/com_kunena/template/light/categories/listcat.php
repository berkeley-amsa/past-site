<?php
/**
 * @version $Id: listcat.php 3108 2010-07-26 21:48:48Z mahagr $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
// Dont allow direct linking
defined ( '_JEXEC' ) or die ();

$this->displayAnnouncement ();
CKunenaTools::showModulePosition ( 'kunena_announcement' );
$this->displayPathway ();
?>

<div class="klist-markallcatsread kcontainer">
	<div class="ksectionbody">
		<div class="fltlft">
			<?php if ($this->my->id != 0) : ?>
			<form action="<?php echo KUNENA_LIVEURLREL; ?>" name="markAllForumsRead" method="post">
				<input type="hidden" name="markaction" value="allread" />
				<input type="submit" class="kbutton button ks" value="<?php echo JText::_('COM_KUNENA_GEN_MARK_ALL_FORUMS_READ'); ?>" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
			<?php endif; ?>
		</div>
		<div class="fltrt">
			<?php $this->displayForumJump (); ?>
		</div>
	</div>
</div>



<?php
	if (count ( $this->categories [0] )) {
	$this->displayCategories ();
	$this->displayWhoIsOnline ();
	$this->displayStats ();
?>

<?php
} else {
	$this->displayInfoMessage ();
}
?>
