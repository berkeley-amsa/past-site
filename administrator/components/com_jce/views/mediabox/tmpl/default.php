<?php 
/**
 * @version		$Id: default.php 201 2011-05-08 16:27:15Z happy_noodle_boy $
 * @package   	JCE
 * @copyright 	Copyright © 2009-2011 Ryan Demmer. All rights reserved.
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license   	GNU/GPL 2 or later
 * This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php" method="post" name="adminForm">
    <div id="jce">
    	<fieldset class="adminform panelform">
			 <legend><?php echo JText :: _('WF_MEDIABOX_PARAMETERS');?></legend>
		    <ul class="adminformlist">
			<?php 
				foreach ($this->params as $param) {
					echo '<li>' . $param[0] . $param[1] . '</li>';
				}
		    ?>
			</ul>
		</fieldset>
    </div>
    <input type="hidden" name="option" value="com_jce" />
    <input type="hidden" name="client" value="<?php echo $this->client; ?>" />
    <input type="hidden" name="view" value="mediabox" />
    <input type="hidden" name="task" value="" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>