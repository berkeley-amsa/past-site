<?php
/**
* @package   ZOO Comment
* @file      list-h.php
* @version   2.4.2
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// include css
$zoo->document->addStylesheet('modules:mod_zoocomment/tmpl/list-h/style.css');

// include js
$zoo->document->addScript('modules:mod_zoocomment/mod_zoocomment.js');

$count = count($comments);
$table = $zoo->table->item;
?>

<div class="zoo-comment list-h">

	<?php if ($count) : ?>
	
		<ul>
			<?php $i = 0; foreach ($comments as $comment) : ?>
			
				<?php // set author name
					$author = $comment->getAuthor();
					$author->name = $author->name ? $author->name : JText::_('COM_ZOO_ANONYMOUS');
					$item = $table->get($comment->item_id);
				?>
			
				<li class="width<?php echo intval(100 / $count);?> <?php if ($i % 2 == 0) { echo 'odd'; } else { echo 'even'; } ?> <?php if ($author->isJoomlaAdmin()) echo 'comment-byadmin'; ?>">
					<div class="match-height">
				
						<?php if ($params->get('show_avatar', 1)) : ?>
						<div class="avatar">
							<?php if ($author->url) : ?><a href="<?php echo $author->url; ?>" title="<?php echo $author->url; ?>"><?php endif; ?>
							<?php echo $author->getAvatar($params->get('avatar_size', 50)); ?>
							<?php if ($author->url) : ?></a><?php endif; ?>
						</div>
						<?php endif; ?>
						
						<?php if ($params->get('show_author', 1)) : ?>
						<h3 class="author">
							<?php if ($author->url) : ?><a href="<?php echo $author->url; ?>" title="<?php echo $author->url; ?>"><?php endif; ?>
							<?php echo $author->name; ?>
							<?php if ($author->url) : ?></a><?php endif; ?>
						</h3>
						<?php endif; ?>
										
						<?php if ($params->get('show_meta', 1)) : ?>
						<p class="meta">
							<?php echo $zoo->html->_('date', $comment->created, $zoo->date->format(JText::_('ZOO_COMMENT_MODULE_DATE_FORMAT')), $zoo->date->getOffset()); ?>
							| <a class="permalink" href="<?php echo JRoute::_($zoo->route->comment($comment)); ?>">#</a>
						</p>
						<?php endif; ?>
					
						<p class="content">
							<?php echo $zoo->comment->filterContentOutput($zoo->string->truncate($comment->content, CommentModuleHelper::MAX_CHARACTERS)); ?>
						</p>
	
					</div>
				</li>
	
			<?php $i++; endforeach; ?>
		</ul>
		
	<?php else : ?>
		<?php echo JText::_('COM_ZOO_NO_COMMENTS_FOUND'); ?>
	<?php endif; ?>
	
</div>

<script type="text/javascript">
	jQuery(function($) {
		$('div.zoo-comment.list-h').each(function() { $(this).find('.match-height').matchHeight(); });
	});
</script>