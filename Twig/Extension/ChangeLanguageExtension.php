<?php

namespace Craue\TwigExtensionsBundle\Twig\Extension;

/**
 * Twig extension providing helpers for implementing a language change mechanism and handling localized routes.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @author Christophe Coevoet
 * @copyright 2011 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class ChangeLanguageExtension extends AbstractLocaleAwareExtension {

	/**
	 * @var array
	 */
	protected $availableLocales = array();

	/**
	 * @var boolean
	 */
	protected $showForeignLanguageNames = true;

	/**
	 * @var boolean
	 */
	protected $showFirstUppercase = false;

	/**
	 * @var string
	 */
	protected $languageNameAlias = null;

	/**
	 * @var string
	 */
	protected $cleanRouteParametersAlias = null;

	/**
	 * @var string
	 */
	protected $availableLocalesAlias = null;

	/**
	 * Sets the available locales.
	 * @param array $availableLocales
	 */
	public function setAvailableLocales(array $availableLocales = array()) {
		$this->availableLocales = $availableLocales;
	}

	/**
	 * Sets whether each language's name will be shown in its foreign language.
	 * @param boolean $showForeignLanguageNames
	 */
	public function setShowForeignLanguageNames($showForeignLanguageNames) {
		$this->showForeignLanguageNames = (boolean) $showForeignLanguageNames;
	}

	/**
	 * Sets whether all language names will be shown with a leading uppercase character.
	 * This requires the mbstring extension {@link http://php.net/manual/book.mbstring.php} to be loaded.
	 * @param boolean $showFirstUppercase
	 */
	public function setShowFirstUppercase($showFirstUppercase) {
		$this->showFirstUppercase = (boolean) $showFirstUppercase;
	}

	/**
	 * @param string $languageNameAlias Alias for the languageName function.
	 * @param string $cleanRouteParametersAlias Alias for the cleanRouteParameters filter.
	 * @param string $availableLocalesAlias Alias for the availableLocales global variable.
	 */
	public function setAliases($languageNameAlias = null, $cleanRouteParametersAlias = null,
			$availableLocalesAlias = null) {
		if (!empty($languageNameAlias)) {
			$this->languageNameAlias = $languageNameAlias;
		}
		if (!empty($cleanRouteParametersAlias)) {
			$this->cleanRouteParametersAlias = $cleanRouteParametersAlias;
		}
		if (!empty($availableLocalesAlias)) {
			$this->availableLocalesAlias = $availableLocalesAlias;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFunctions() {
		$functions = array();

		$getLanguageNameMethod = new \Twig_Function_Method($this, 'getLanguageName');
		$functions['craue_languageName'] = $getLanguageNameMethod;
		if (!empty($this->languageNameAlias)) {
			$functions[$this->languageNameAlias] = $getLanguageNameMethod;
		}

		return $functions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFilters() {
		$filters = array();

		$cleanRouteParametersMethod = new \Twig_Filter_Method($this, 'cleanRouteParameters');
		$filters['craue_cleanRouteParameters'] = $cleanRouteParametersMethod;
		if (!empty($this->cleanRouteParametersAlias)) {
			$filters[$this->cleanRouteParametersAlias] = $cleanRouteParametersMethod;
		}

		return $filters;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getGlobals() {
		$globals = array();

		$globals['craue_availableLocales'] = $this->availableLocales;
		if (!empty($this->availableLocalesAlias)) {
			$globals[$this->availableLocalesAlias] = $this->availableLocales;
		}

		return $globals;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'craue_changeLanguage';
	}

	/**
	 * Get the language name.
	 * @param string $locale Locale to be used with {@link http://php.net/manual/locale.getdisplaylanguage.php}.
	 * @return string
	 */
	public function getLanguageName($locale) {
		$localeToUse = $this->showForeignLanguageNames === true ? $locale : $this->getLocale();

		$languageName = \Locale::getDisplayLanguage($locale, $localeToUse);

		if ($this->showFirstUppercase === true) {
			if (!extension_loaded('mbstring')) {
				throw new \RuntimeException('PHP extension "mbstring" is not loaded. Either load it or disable the "showFirstUppercase" option.');
			}
			$encoding = mb_detect_encoding($languageName);
			$languageName = mb_strtoupper(mb_substr($languageName, 0, 1, $encoding), $encoding)
					.mb_substr($languageName, 1, mb_strlen($languageName, $encoding), $encoding);
		}

		return $languageName;
	}

	/**
	 * Removes some special request attributes from the current route.
	 * @param array $parameters Current route parameters.
	 * @return array Filtered route parameters.
	 */
	public function cleanRouteParameters(array $parameters) {
		unset($parameters['_controller']);
		unset($parameters['_route']);
		unset($parameters['_route_params']);
		unset($parameters['_locale']);
		unset($parameters['_template']);
		unset($parameters['_template_vars']);
		unset($parameters['_template_default_vars']);
		return $parameters;
	}

}
