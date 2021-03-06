# Information

TwigExtensionsBundle is just a collection of Twig extensions i find useful.

This bundle should be used in conjunction with Symfony2.

## DecorateEmptyValueExtension

Provides an enhanced `default` filter to decorate empty values with a placeholder which can even be HTML.

Usually, if you want to use HTML, e.g. the HTML entity "&mdash;", as value for the default filter in an HTML Twig
template you have to do cumbersome

	{{ somevalue | e | default('&mdash;') | raw }}

to make it render properly. With this extension you can write

	{{ somevalue | craue_default }}

instead.

## ArrayHelperExtension

Provides a `without` filter wrapping PHP's `array_diff` function and a `translateArray` filter which translates all
entries in an array.

## FormExtension

Provides a mechanism to render a form several times on one page. This is done by cloning the form prior to rendering
using the `cloneForm` function.

## StringHelperExtension

Provides a `substr` function wrapping PHP's `substr` function.

Also provides a `trailingDot` filter for ensuring that a text ends with a dot. This comes in handy when using error messages
(e.g. for validation) of vendor bundles (which are written like sentences but are missing the trailing dots) together
with your own ones (which should include the trailing dot).

## FormatDateTimeExtension

Provides filters for locale-aware formatting of date, time, and date/time values.

## FormatNumberExtension

Provides filters for locale-aware formatting of numbers and currencies.

## ChangeLanguageExtension

Providing helpers for implementing a language change mechanism and handling localized routes.

# Installation

## Add TwigExtensionsBundle to your vendor directory

Either by using a Git submodule:

	git submodule add https://github.com/craue/TwigExtensionsBundle.git vendor/bundles/Craue/TwigExtensionsBundle

Or by using the `deps` file:

	[CraueTwigExtensionsBundle]
	git=https://github.com/craue/TwigExtensionsBundle.git
	target=bundles/Craue/TwigExtensionsBundle

## Add TwigExtensionsBundle to your application kernel

	// app/AppKernel.php
	public function registerBundles() {
		$bundles = array(
			// ...
			new Craue\TwigExtensionsBundle\CraueTwigExtensionsBundle(),
		);
		// ...
	}

## Register the Craue namespace

	// app/autoload.php
	$loader->registerNamespaces(array(
		// ...
		'Craue' => __DIR__.'/../vendor/bundles',
	));

## Make the Twig extensions available by updating your configuration

	// app/config/config.yml
	craue_twig_extensions: ~

# Examples to use the extensions in your Twig template

## DecorateEmptyValueExtension

	{{ someValueWhichMayBeEmpty | craue_default }}<br />
	{{ someValueWhichMayBeEmpty | craue_default('no value') }}<br />
	{{ someValueWhichMayBeEmpty | craue_default('&ndash;') }}<br />
	{{ someValueWhichMayBeEmpty | craue_default(0) }}

## ArrayHelperExtension

	{{ anArray | craue_without(aValueOrAnArray) | join(', ') }}<br />
	{{ ['red', 'green', 'yellow', 'blue'] | craue_without('yellow') | join(', ') }} will print "red, green, blue"<br />
	{{ ['red', 'green', 'yellow', 'blue'] | craue_without(['yellow', 'black', 'red']) | join(', ') }} will print "green, blue"

	{{ anArray | craue_translateArray() | join(', ') }}<br />

## FormExtension

	{% for myEntity in myEntities %}
		{% set myFormInstance = craue_cloneForm(myForm) %}
		<form action={{ path('my_route', {'id': myEntity.getId()}) }} method="post" {{ form_enctype(myFormInstance) }}>
			{{ form_widget(myFormInstance) }}
			<input type="submit" />
		</form>
	{% endfor %}

## StringHelperExtension

	{{ craue_substr('bla', 2) }} will print "a"<br />
	{{ craue_substr('bla', 0, 1) }} will print "b"<br />
	{{ craue_substr('bla', 1, 1) }} will print "l"<br />
	{{ craue_substr('bla', 1, 2) }} will print "la"

	{{ aString | craue_trailingDot }}<br />
	{{ 'This text should end with a dot' | craue_trailingDot }}<br />
	{{ 'This text should end with exactly one dot.' | craue_trailingDot }}

## FormatDateTimeExtension

	<h2>with the current locale</h2>
	date: {{ someDateTimeValue | craue_date }}<br />
	time: {{ someDateTimeValue | craue_time }}<br />
	both: {{ someDateTimeValue | craue_datetime }}

	<h2>with a specific locale</h2>
	date: {{ someDateTimeValue | craue_date('de-DE') }}<br />
	time: {{ someDateTimeValue | craue_time('de') }}<br />
	both: {{ someDateTimeValue | craue_datetime('en-GB') }}

## FormatNumberExtension

	<h2>with the current locale</h2>
	thousands separator: {{ someNumber | craue_number }}<br />
	default currency: {{ someNumber | craue_currency }}<br />
	specific currency: {{ someNumber | craue_currency('EUR') }}<br />
	spelled out number: {{ someNumber | craue_spellout }}

	<h2>with a specific locale</h2>
	thousands separator: {{ someNumber | craue_number('de-DE') }}<br />
	default currency: {{ someNumber | craue_currency(null, 'de-DE') }}<br />
	specific currency: {{ someNumber | craue_currency('EUR', 'de-DE') }}<br />
	spelled out number: {{ someNumber | craue_spellout('de-DE') }}

## ChangeLanguageExtension

There's a Twig template included which you can use to render a "change language" menu like this:

	{% include 'CraueTwigExtensionsBundle:ChangeLanguage:changeLanguage.html.twig' %}

This will render a list of links to the current route in all defined languages. Wrap it in a div to style it via CSS.
Take a look at the template if you want to customize it.

# Set/override default values

## DecorateEmptyValueExtension

	; app/config/parameters.ini
	craue_twig_extensions.decorateEmptyValue.placeholder="&ndash;"

## FormatDateTimeExtension

	; app/config/parameters.ini
	craue_twig_extensions.formatDateTime.datetype="full"
	craue_twig_extensions.formatDateTime.timetype="short"

## FormatNumberExtension

	; app/config/parameters.ini
	craue_twig_extensions.formatNumber.currency="EUR"

## ChangeLanguageExtension

	; app/config/parameters.ini
	craue_twig_extensions.changeLanguage.availableLocales[]="de"
	craue_twig_extensions.changeLanguage.availableLocales[]="en"
	craue_twig_extensions.changeLanguage.availableLocales[]="ru"
	craue_twig_extensions.changeLanguage.showForeignLanguageNames=true
	craue_twig_extensions.changeLanguage.showFirstUppercase=false

With XML for example you can also set the keys to be more specific about the locales:

	<parameter key="craue_twig_extensions.changeLanguage.availableLocales" type="collection">
		<parameter key="de_DE">de</parameter>
		<parameter key="en">en</parameter>
		<parameter key="ru">ru</parameter>
	</parameter>

# Advanced stuff

## DecorateEmptyValueExtension

### Alias

You can define an alias for the filter if you don't like to write

	{{ somevalue | craue_default }}

all the time. Setting this to `"d"` for example with

	; app/config/parameters.ini
	craue_twig_extensions.decorateEmptyValue.filterAlias="d"

allows you to write

	{{ somevalue | d }}

in your Twig Template. But pay attention to not accidentally override built-in filters, although you can do it
intentionally, e.g. by setting the alias to `"default"`.

## ArrayHelperExtension

### Alias

Similar to the DecorateEmptyValueExtension you can define an alias:

	; app/config/parameters.ini
	craue_twig_extensions.arrayHelper.withoutAlias="without"
	craue_twig_extensions.arrayHelper.translateArrayAlias="translateArray"

But, again, pay attention to not accidentally override built-in filters, although you can do it intentionally.

## FormExtension

### Alias

Similar to the DecorateEmptyValueExtension you can define an alias:

	; app/config/parameters.ini
	craue_twig_extensions.form.cloneFormAlias="cloneForm"

But, again, pay attention to not accidentally override built-in functions, although you can do it intentionally.

## StringHelperExtension

### Aliases

Similar to the DecorateEmptyValueExtension you can define aliases:

	; app/config/parameters.ini
	craue_twig_extensions.stringHelper.substrAlias="substr"
	craue_twig_extensions.stringHelper.trailingDotAlias="trailingDot"

But, again, pay attention to not accidentally override built-in filters/functions, although you can do it
intentionally.

## FormatDateTimeExtension

### Aliases

Again, you can define an alias for each filter:

	; app/config/parameters.ini
	craue_twig_extensions.formatDateTime.dateFilterAlias="date"
	craue_twig_extensions.formatDateTime.timeFilterAlias="time"
	craue_twig_extensions.formatDateTime.dateTimeFilterAlias="datetime"

Don't accidentally override built-in filters, although you can do it intentionally, e.g. by setting the
dateFilterAlias to `"date"`.

## FormatNumberExtension

### Aliases

Again, you can define an alias for each filter:

	; app/config/parameters.ini
	craue_twig_extensions.formatNumber.numberFilterAlias="number"
	craue_twig_extensions.formatNumber.currencyFilterAlias="currency"
	craue_twig_extensions.formatNumber.spelloutFilterAlias="spellout"

Don't accidentally override built-in filters, although you can do it intentionally.

## ChangeLanguageExtension

### Aliases

Again, you can define aliases:

	; app/config/parameters.ini
	craue_twig_extensions.changeLanguage.languageNameAlias=
	craue_twig_extensions.changeLanguage.cleanRouteParametersAlias=
	craue_twig_extensions.changeLanguage.availableLocalesAlias=

Don't accidentally override built-in filters/functions/globals, although you can do it intentionally.
