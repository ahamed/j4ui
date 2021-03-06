<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_feed
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.framework');

// Check if feed URL has been set
if (empty ($rssurl))
{
	echo '<div>' . Text::_('MOD_FEED_ERR_NO_URL') . '</div>';

	return;
}

if (!empty($feed) && is_string($feed))
{
	echo $feed;
}
else
{
	$lang      = $app->getLanguage();
	$myrtl     = $params->get('rssrtl', 0);
	$direction = ' ';

	if ($lang->isRtl() && $myrtl == 0)
	{
		$direction = ' redirect-rtl';
	}
	// Feed description
	elseif ($lang->isRtl() && $myrtl == 1)
	{
		$direction = ' redirect-ltr';
	}
	elseif ($lang->isRtl() && $myrtl == 2)
	{
		$direction = ' redirect-rtl';
	}
	elseif ($myrtl == 0)
	{
		$direction = ' redirect-ltr';
	}
	elseif ($myrtl == 1)
	{
		$direction = ' redirect-ltr';
	}
	elseif ($myrtl == 2)
	{
		$direction = ' redirect-rtl';
	}

	if ($feed != false) :
		// Image handling
		$iUrl   = $feed->image ?? null;
		$iTitle = $feed->imagetitle ?? null;
		?>
		<div style="direction: <?php echo $rssrtl ? 'rtl' : 'ltr'; ?>; text-align: <?php echo $rssrtl ? 'right' : 'left'; ?> !important" class="feed">
		<div class="p-4">
			<?php
			// Feed title
			if (!is_null($feed->title) && $params->get('rsstitle', 1)) : ?>
				<h2 class="<?php echo $direction; ?>">
					<a href="<?php echo str_replace('&', '&amp;', $rssurl); ?>" target="_blank">
					<?php echo $feed->title; ?></a>
				</h2>
			<?php endif;
			// Feed date
			if ($params->get('rssdate', 1)) : ?>
				<h3>
				<?php echo HTMLHelper::_('date', $feed->publishedDate, Text::_('DATE_FORMAT_LC3')); ?>
				</h3>
			<?php endif; ?>

			<?php // Feed description ?>
			<?php if ($params->get('rssdesc', 1)) : ?>
				<?php echo $feed->description; ?>
			<?php endif; ?>

			<?php // Feed image ?>
			<?php if ($params->get('rssimage', 1) && $iUrl) : ?>
				<img src="<?php echo $iUrl; ?>" alt="<?php echo @$iTitle; ?>">
			<?php endif; ?>
		</div>
		<hr class="m-0" />

	<?php // Show items ?>

	<?php if (!empty($feed)) : ?>
		<div class="j-card-event-list">
			<?php for ($i = 0; $i < $params->get('rssitems', 3); $i++) :

			if (!$feed->offsetExists($i)) :
				break;
			endif;
			$uri  = $feed[$i]->uri || !$feed[$i]->isPermaLink ? trim($feed[$i]->uri) : trim($feed[$i]->guid);
			$uri  = !$uri || stripos($uri, 'http') !== 0 ? $rssurl : $uri;
			$text = $feed[$i]->content !== '' ? trim($feed[$i]->content) : '';
			?>
				<div class="j-card-event-item">
					<?php if ($params->get('rssitemdate', 0)) : ?>
						<time datetime="<?php echo HTMLHelper::_('date', $feed[$i]->publishedDate, Text::_('DATE_FORMAT_LC3')); ?>">
							<span><?php echo HTMLHelper::_('date', $feed[$i]->publishedDate, 'd'); ?></span>
							<?php echo HTMLHelper::_('date', $feed[$i]->publishedDate, 'M Y'); ?>
						</time>
					<?php endif; ?>

					<div class="j-card-event-content">
						<?php if (!empty($uri)) : ?>
							<h5 class="feed-link">
							<a href="<?php echo $uri; ?>" target="_blank">
							<?php echo trim($feed[$i]->title); ?></a></h5>
						<?php else : ?>
							<h5 class="feed-link"><?php echo trim($feed[$i]->title); ?></h5>
						<?php endif; ?>

						<?php if ($params->get('rssitemdesc', 1) && $text !== '') : ?>
							<div class="feed-item-description text-muted">
							<?php
								// Strip the images.
								$text = OutputFilter::stripImages($text);
								$text = HTMLHelper::_('string.truncate', $text, $params->get('word_count', 0), true, false);
								echo str_replace('&apos;', "'", $text);
							?>
							</div>
						<?php endif; ?>
					</div>
				</div>
		<?php endfor; ?>
		</div>
	<?php endif; ?>
	</div>
	<?php endif;
}
