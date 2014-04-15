<?php
/**
* @package   com_zoo Component
* @file      related.php
* @version   2.4.9 May 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<?php if ($this->checkPosition('media')) : ?>
<div class="sub-pos-media">
	<?php echo $this->renderPosition('media'); ?>
</div>
<?php endif; ?>

<?php if ($this->checkPosition('title')) : ?>
<h4 class="sub-pos-title">
	<?php echo $this->renderPosition('title'); ?>
</h4>
<?php endif; ?>

<?php if ($this->checkPosition('subtitle')) : ?>
<p class="sub-pos-subtitle">
	<?php echo $this->renderPosition('subtitle', array('style' => 'comma')); ?>
</p>
<?php endif; ?>

<?php if ($this->checkPosition('links')) : ?>
<p class="sub-pos-links">
	<?php echo $this->renderPosition('links', array('style' => 'pipe')); ?>
</p>
<?php endif; ?>