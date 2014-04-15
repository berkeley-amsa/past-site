<?php
/**
 * @version $Id: infomessage.php 3184 2010-08-12 12:35:49Z fxstein $
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 **/
defined ( '_JEXEC' ) or die ();

?>
<div class="kblock kinfomessage">
	<div class="kheader">
		<h2><span><?php echo $this->header; ?></span></h2>
	</div>
	<div class="kcontainer">
		<div class="kbody">
			<?php echo $this->contents; ?>
		</div>
	</div>
</div>