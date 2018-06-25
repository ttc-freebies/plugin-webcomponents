<?php
use Joomla\CMS\Factory;
/**
 * Installation class to perform additional changes during install/uninstall/update
 */
class plgSystemWebcomponentsInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $adapter) {
		$this->minimumJoomla = '3.8';
		$this->minimumPhp = JOOMLA_MINIMUM_PHP;
	}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $adapter) {
		$this->copyLayouts();
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $adapter) {
		$this->copyLayouts();
	}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $adapter) {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$templateOverrides = static::getTemplates();

		// Initialize the error array
		$errorTemplates = array();

		// Loop the supported templates
		foreach ($templateOverrides as $template) {
			// Set the file paths
			$base = ($template->client_id === '1') ? JPATH_ADMINISTRATOR : JPATH_ROOT;
			$tmplRoot = $base . '/templates/' . $template->name;
			$destination = $base . '/templates/' . $template->name . '/js/system';

			// Make sure the template is actually installed
			if (is_dir($tmplRoot) && is_dir($destination)) {
				if (is_file($destination . '/core.js')) {
					unlink($destination . '/core.js');
				}

				if (is_file($destination . '/core-uncompressed.js')) {
					unlink($destination . '/core-uncompressed.js');
				}
			}
		}
	}

	/**
	 * Function to get all the templates
	 *
	 * @return  array
	 */
	protected static function getTemplates()
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT * FROM #__extensions WHERE type = "template"');

		return $db->loadObjectList();
	}

	/**
	 * Function to copy layout overrides for core templates at install or update
	 *
	 * @return  void
	 */
	private function copyLayouts()
	{
		jimport('joomla.filesystem.folder');

		$templateOverrides = static::getTemplates();

		// Initialize the error array
		$errorTemplates = array();

		// Loop the supported templates
		foreach ($templateOverrides as $template) {
			// Set the file paths
			$base = ($template->client_id === '1') ? JPATH_ADMINISTRATOR : JPATH_ROOT;
			$source = __DIR__ . '/tmpl';
			$tmplRoot = $base . '/templates/' . $template->name;
			$destination = $base . '/templates/' . $template->name . '/js';

			// Make sure the template is actually installed
			if (is_dir($tmplRoot)) {

				// Check if the js folder is already created or create it
				if (!is_dir($destination)) {
					mkdir($destination, 0700, true);
				}

				// If there's a failure in copying the overrides, log it to the error array
				try {
					if (!JFolder::copy($source, $destination, '', true)) {
						$errorTemplates[] = ucfirst($template);
					}
				} catch (RuntimeException $exception) {
					$errorTemplates[] = ucfirst($template);
				}
			}
		}

		// If errors notify the user
		if (count($errorTemplates) > 0) {
			Factory::getApplication()->enqueueMessage('Oops errors:', implode(', ', $errorTemplates));
		}
	}
}
