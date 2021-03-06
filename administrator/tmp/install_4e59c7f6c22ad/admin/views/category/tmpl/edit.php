<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/
	defined('_JEXEC') or die('Restricted access');

	$this->app->html->_('behavior.tooltip');

	// add script
	$this->app->document->addScript('assets:js/alias.js');
	$this->app->document->addScript('assets:js/category.js');

	// filter output
	JFilterOutput::objectHTMLSafe($this->category, ENT_QUOTES, array('params'));
?>

<form id="category-default" action="index.php" method="post" name="adminForm" accept-charset="utf-8">

<?php echo $this->partial('menu'); ?>

<div class="box-bottom">

	<div class="col col-left width-60">

		<fieldset class="creation-form">
		<legend><?php echo JText::_('Details'); ?></legend>
			<div class="element element-name">
				<strong><?php echo JText::_('Name'); ?></strong>
				<div id="name-edit">
					<div class="row">
						<input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->category->name; ?>" />
						<span class="message-name"><?php echo JText::_('Please enter valid name.'); ?></span>
					</div>
					<div class="slug">
						<span><?php echo JText::_('Slug'); ?>:</span>
						<a class="trigger" href="#" title="<?php echo JText::_('Edit Category Slug');?>"><?php echo $this->category->alias; ?></a>
						<div class="panel">
							<input type="text" name="alias" value="<?php echo $this->category->alias; ?>" />
							<input type="button" class="accept" value="<?php echo JText::_('Accept'); ?>"/>
							<a href="#" class="cancel"><?php echo JText::_('Cancel'); ?></a>
						</div>
					</div>
				</div>
			</div>
			<div class="element element-published">
				<strong><?php echo JText::_('Published'); ?></strong>
				<?php echo $this->lists['select_published']; ?>
			</div>
			<div class="element element-parent-item">
				<strong><?php echo JText::_('Parent Category'); ?></strong>
				<?php echo $this->lists['select_parent']; ?>
			</div>
		<div class="element element-description">
			<strong><?php echo JText::_('Description'); ?></strong>
			<div>
				<?php
					// parameters : areaname, content, width, height, cols, rows, show xtd buttons
					echo $this->app->system->editor->display('description', $this->category->description, null, null, '60', '20', array('pagebreak', 'readmore', 'article')) ;
				?>
			</div>
		</div>
		</fieldset>

	</div>

	<div class="col col-right width-40">


		<?php $form = $this->app->parameterform->create(dirname(__FILE__).'/params.xml'); ?>

		<div id="parameter-accordion">
			<h3 class="toggler"><?php echo JText::_('Content'); ?></h3>
			<div class="content">
				<?php echo $this->application->getParamsForm()->setValues($this->params->get('content.'))->render('params[content]', 'category-content'); ?>
			</div>
			<h3 class="toggler"><?php echo JText::_('Config'); ?></h3>
			<div class="content">
				<?php echo $this->application->getParamsForm()->setValues($this->params->get('config.'))->render('params[config]', 'category-config'); ?>
			</div>
			<h3 class="toggler"><?php echo JText::_('Template'); ?></h3>
			<div class="content">
				<?php
					if ($template = $this->application->getTemplate()) {
						echo $template->getParamsForm(true)->setValues($this->params->get('template.'))->render('params[template]', 'category');
					} else {
						echo '<em>'.JText::_('Please select a Template').'</em>';
					}
				?>
			</div>
			<h3 class="toggler"><?php echo JText::_('Metadata'); ?></h3>
			<div class="content">
				<?php echo $form->setValues($this->params->get('metadata.'))->render('params[metadata]', 'metadata'); ?>
			</div>
		</div>

	</div>

</div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
<input type="hidden" name="cid[]" value="<?php echo $this->category->id; ?>" />
<?php echo $this->app->html->_('form.token'); ?>

</form>

<script type="text/javascript">
	jQuery(function($) {
		$('#category-default').EditCategory();
		$('#name-edit').AliasEdit({ edit: <?php echo (int) $this->category->id; ?> });
		$('#name-edit').find('input[name="name"]').focus();
	});
</script>

<?php echo ZOO_COPYRIGHT; ?>