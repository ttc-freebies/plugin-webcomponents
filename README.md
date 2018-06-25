# plugin-webcomponents

### What is this?
It's a tiny plugin that brings support for web components to Joomla 3.x.

Basically you can load the custom element or web component:

```php

  HTMLHelper::_('webcomponent', 'mod_mymodule/mymodule.min.js', ['version' => 'auto', 'relative' => true]);

```

### Requirements

- Joomla 3.8.8+

- Not having another plugin that overrides the core.js file!

- Your template should not override the core.js (all your templates will get a copy of the updated core.js, the distributed from the Joomla staging repo plus the extra bits needed for the web components polyfills and lazy loading form the 4.0-dev repo)


