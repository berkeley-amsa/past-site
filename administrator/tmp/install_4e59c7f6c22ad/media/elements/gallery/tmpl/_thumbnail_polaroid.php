<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="thumbnail">
	<div class="polaroid-m">
		<div class="polaroid-l">
			<div class="polaroid-r">
			
				<div class="tape"></div>
				<a href="<?php echo $thumb['img']; ?>" title="<?php echo $thumb['name']; ?>" <?php echo $a_attribs; ?>>
					<img src="<?php echo $thumb['thumb']; ?>" alt="<?php echo $thumb['name']; ?>" width="<?php echo $thumb['thumb_width']; ?>" height="<?php echo $thumb['thumb_height']; ?>" />
				</a>
				
			</div>
		</div>
	</div>
</div>