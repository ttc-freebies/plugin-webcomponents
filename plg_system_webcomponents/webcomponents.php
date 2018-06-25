<?php
/**
 * @package     ttc-freebies.plugin-core-webcomponents
 *
 * @copyright   Copyright (C) 2018 Dimitrios Grammatikogiannis, All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class PlgSystemWebcomponents extends JPlugin
{
	public function onAfterRoute()
	{
		if (Factory::getDocument()->_type !== 'html') {
			return;
		}

		// Register listeners for JHtml helpers
		if (!HTMLHelper::isRegistered('webcomponent')) {
			HTMLHelper::register('webcomponent', 'PlgSystemWebcomponents::webcomponent');
		}
	}

	/**
	 * Loads the path of a custom element or webcomponent into the scriptOptions object
	 *
	 * @param   string  $file     The path of the web component (expects the ES6 version). File need to have also an
	 *                            -es5(.min).js version in the same folder for the non ES6 Browsers.
	 * @param   array   $options  The extra options for the script
	 *
	 * @since   4.0.0
	 *
	 * @see     HTMLHelper::stylesheet()
	 * @see     HTMLHelper::script()
	 *
	 * @return  void
	 */
	public static function webcomponent(string $file, array $options = array())
	{
		if (empty($file)) {
			return;
		}

		// Script core.js is responsible for the polyfills and the async loading of the web components
		HTMLHelper::_('behavior.core');
		Factory::getDocument()->addScriptOptions('system.paths', array('root' => Uri::root(true), 'base' => Uri::base(true), 'rootFull' => Uri::root(false)));
		$version = '';
		$mediaVersion = Factory::getDocument()->getMediaVersion();

		// Add the css if exists
		HTMLHelper::stylesheet(str_replace('.js', '.css', $file), $options);

		$options['relative'] = $options['relative'] ?? true;
		$options['detectBrowser'] = $options['detectBrowser'] ?? false;
		$options['detectDebug'] = $options['detectDebug'] ?? false;
		$options['pathOnly'] = true;

		$path = HTMLHelper::script($file, $options);

		if (empty($path)) {
			return;
		}

		if (isset($options['version'])) {
			if ($options['version'] === 'auto') {
				$version = '?' . $mediaVersion;
			} else {
				$version = '?' . $options['version'];
			}
		}

		$potential = $path . ((strpos($path, '?') === false) ? $version : '');

		if (!in_array($potential, Factory::getDocument()->getScriptOptions('webcomponents'))) {
			Factory::getDocument()->addScriptOptions('webcomponents', array($potential));
			return;
		}

		return;
	}
}
