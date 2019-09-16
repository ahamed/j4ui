<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/** @var \Joomla\Component\Content\Administrator\View\Article\HtmlView $this */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

HTMLHelper::_('script', 'com_contenthistory/admin-history-versions.js', ['version' => 'auto', 'relative' => true]);

$this->configFieldsets  = array('editorConfig');
$this->hiddenFieldsets  = array('basic-limited');
$fieldsetsInImages = ['image-intro', 'image-full'];
$fieldsetsInLinks = ['linka', 'linkb', 'linkc'];
$this->ignore_fieldsets = array_merge(array('jmetadata', 'item_associations'), $fieldsetsInImages, $fieldsetsInLinks);
$this->useCoreUI = true;

// Create shortcut to parameters.
$params = clone $this->state->get('params');
$params->merge(new Registry($this->item->attribs));

$app = Factory::getApplication();
$input = $app->input;

$assoc = Associations::isEnabled();
$hasAssoc = ($this->form->getValue('language', null, '*') !== '*');

// In case of modal
$isModal = $input->get('layout') == 'modal' ? true : false;
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

// Generate article view URL
$articleUrl = '';
$articleUrlClass = 'inactive';
if($this->item->id > 0) {
	// URL link to article
	$articleUrl = Route::_(JURI::root() . \ContentHelperRoute::getArticleRoute($this->item->id, $this->item->catid, $this->item->language));
	$articleUrlClass = 'active';
}

?>

<form action="<?php echo Route::_('index.php?option=com_content&layout=' . $layout . $tmpl . '&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate">

	<div class="row">
		<div class="col-lg-9">
			<?php echo LayoutHelper::render('joomla.edit.title', $this); ?>
		</div>
		<div class="col-lg-3">
			<div class="bg-white px-3 card-body">
				<a class="field-view-url <?php echo $articleUrlClass; ?>" target="_blank" href="<?php echo $articleUrl; ?>"><?php echo JText::_('COM_CONTENT_ARTICLE_URL'); ?></a>
			</div>
		</div>
	</div>

	<div class="row mt-4">
		<div class="col-lg-9">
			<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'general')); ?>

			<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'general', Text::_('COM_CONTENT_ARTICLE_CONTENT')); ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body">
							<fieldset class="adminform">
								<?php echo $this->form->getLabel('articletext'); ?>
								<?php echo $this->form->getInput('articletext'); ?>
							</fieldset>
						</div>
					</div>
				</div>
			</div>

			<?php echo HTMLHelper::_('uitab.endTab'); ?>

			<?php // Do not show the images and links options if the edit form is configured not to. ?>
			<?php if ($params->get('show_urls_images_backend') == 1) : ?>
				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'images', Text::_('COM_CONTENT_FIELDSET_URLS_AND_IMAGES')); ?>
				<div class="row">
					<div class="col-12 col-lg-6">
					<?php foreach ($fieldsetsInImages as $fieldset) : ?>
						<fieldset id="fieldset-<?php echo $fieldset; ?>" class="options-grid-form options-grid-form-full">
							<legend><?php echo Text::_($this->form->getFieldsets()[$fieldset]->label); ?></legend>
							<div>
							<?php echo $this->form->renderFieldset($fieldset); ?>
							</div>
						</fieldset>
					<?php endforeach; ?>
					</div>
					<div class="col-12 col-lg-6">
					<?php foreach ($fieldsetsInLinks as $fieldset) : ?>
						<fieldset id="fieldset-<?php echo $fieldset; ?>" class="options-grid-form options-grid-form-full">
							<legend><?php echo Text::_($this->form->getFieldsets()[$fieldset]->label); ?></legend>
							<div>
							<?php echo $this->form->renderFieldset($fieldset); ?>
							</div>
						</fieldset>
					<?php endforeach; ?>
					</div>
				</div>

				<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php endif; ?>

			<?php $this->show_options = $params->get('show_article_options', 1); ?>
			<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

			<?php // Do not show the publishing options if the edit form is configured not to. ?>
			<?php if ($params->get('show_publishing_options', 1) == 1) : ?>
				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('COM_CONTENT_FIELDSET_PUBLISHING')); ?>
				<div class="row">
					<div class="col-12 col-lg-6">
						<fieldset id="fieldset-publishingdata" class="options-grid-form options-grid-form-full">
							<legend><?php echo Text::_('JGLOBAL_FIELDSET_PUBLISHING'); ?></legend>
							<div>
							<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
							</div>
						</fieldset>
					</div>
					<div class="col-12 col-lg-6">
						<fieldset id="fieldset-metadata" class="options-grid-form options-grid-form-full">
							<legend><?php echo Text::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'); ?></legend>
							<div>
							<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
							</div>
						</fieldset>
					</div>
				</div>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php endif; ?>

			<?php if (!$isModal && $assoc) : ?>
				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'associations', Text::_('JGLOBAL_FIELDSET_ASSOCIATIONS')); ?>
				<?php if ($hasAssoc) : ?>
					<fieldset id="fieldset-associations" class="options-grid-form options-grid-form-full">
					<legend><?php echo Text::_('JGLOBAL_FIELDSET_ASSOCIATIONS'); ?></legend>
					<div>
					<?php echo LayoutHelper::render('joomla.edit.associations', $this); ?>
					</div>
					</fieldset>
				<?php endif; ?>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php elseif ($isModal && $assoc) : ?>
				<div class="hidden"><?php echo LayoutHelper::render('joomla.edit.associations', $this); ?></div>
			<?php endif; ?>

			<?php if ($this->canDo->get('core.admin')) : ?>
				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'editor', Text::_('COM_CONTENT_SLIDER_EDITOR_CONFIG')); ?>
				<fieldset id="fieldset-editor" class="form-no-margin options-grid-form">
					<legend><?php echo Text::_('COM_CONTENT_SLIDER_EDITOR_CONFIG'); ?></legend>
					<div>
					<?php echo $this->form->renderFieldset('editorConfig'); ?>
					</div>
				</fieldset>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php endif; ?>

			<?php if ($this->canDo->get('core.admin')) : ?>
				<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_('COM_CONTENT_FIELDSET_RULES')); ?>
				<fieldset id="fieldset-rules" class="options-grid-form options-grid-form-full">
					<legend><?php echo Text::_('COM_CONTENT_FIELDSET_RULES'); ?></legend>
					<div>
					<?php echo $this->form->getInput('rules'); ?>
					</div>
				</fieldset>
				<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<?php endif; ?>

			<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

			<?php // Creating 'id' hiddenField to cope with com_associations sidebyside loop ?>
			<?php if ($params->get('show_publishing_options', 1) == 0) : ?>
				<?php $hidden_fields = $this->form->getInput('id'); ?>
				<div class="hidden"><?php echo $hidden_fields; ?></div>
			<?php endif; ?>

			<input type="hidden" name="task" value="">
			<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>">
			<input type="hidden" name="forcedLanguage" value="<?php echo $input->get('forcedLanguage', '', 'cmd'); ?>">
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
		<div class="col-lg-3">
			<div class="bg-white px-3 form-no-margin card-body">
				<?php echo LayoutHelper::render('joomla.edit.alias', $this); ?>
				<?php 
					$this->fieldset = 'sidebar-featured';
					echo LayoutHelper::render('joomla.edit.fieldset', $this); 
				?>
			</div>

			<div class="bg-white px-3 mt-4 form-no-margin card-body">
				<?php 
					$this->fieldset = 'sidebar-media';
					echo LayoutHelper::render('joomla.edit.fieldset', $this); 
				?>
				<?php //echo LayoutHelper::render('joomla.edit.field_generator', array( 'fields' => $fields2, 'getForm' => $this->getForm(),  'hidden_fields' => $this->get('hidden_fields') )); ?>
			</div>

			<!-- published -->
			<div class="bg-white px-3 mt-4 form-no-margin card-body">
				<?php echo LayoutHelper::render('joomla.edit.fields', array( 'fields' => array( 'transition', array('parent', 'parent_id'), array('published', 'state', 'enabled') ), 'getForm' => $this->getForm(),  'hidden_fields' => $this->get('hidden_fields') )); ?>
			</div>

			<!-- category -->
			<div class="bg-white px-3 mt-4 form-no-margin card-body">
				<?php echo LayoutHelper::render('joomla.edit.fields', array( 'fields' => array( array('category', 'catid') ), 'getForm' => $this->getForm(),  'hidden_fields' => $this->get('hidden_fields') )); ?>
			</div>

			<div class="bg-white px-3 mt-4 form-no-margin card-body">
				<?php 
					$this->fieldset = 'sidebar-access';
					echo LayoutHelper::render('joomla.edit.fieldset', $this); 
				?>
			</div>

			<div class="bg-white px-3 mt-4 form-no-margin card-body">
				<?php 
					$this->fieldset = 'sidebar-language';
					echo LayoutHelper::render('joomla.edit.fieldset', $this); 
				?>
			</div>
			
			<div class="bg-white px-3 mt-4 form-no-margin card-body">
				<?php 
					$this->fieldset = 'sidebar-tags';
					echo LayoutHelper::render('joomla.edit.fieldset', $this); 
				?>
			</div>

		</div>
	</div>
</form>
