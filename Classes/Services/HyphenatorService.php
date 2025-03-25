<?php

/*
* This file is part of the "lia_hyphenator" Extension for TYPO3 CMS.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*/

namespace LIA\LiaHyphenator\Services;

use Org\Heigl\Hyphenator\Hyphenator;
use Org\Heigl\Hyphenator\Options;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;

class HyphenatorService implements \TYPO3\CMS\Core\SingletonInterface
{
    private Hyphenator $hyphenator;
    private Options $options;

    /**
     * Constructs a new HyphenatorService.
     *
     * @param ExtensionConfiguration $extensionConfiguration The ExtensionConfiguration instance.
     */
    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration
    ) {
        $this->hyphenator = new Hyphenator();
        $this->options = new Options();
    }

    /**
     * Get an instance of the Hyphenator with the given options.
     *
     * You can pass the following options:
     *
     * - defaultLocale: The default locale for the hyphenation rules. Defaults to "de_DE".
     * - rightMin: The minimum number of characters that must be left unhyphenated on the right of the word. Defaults to 2.
     * - leftMin: The minimum number of characters that must be left unhyphenated on the left of the word. Defaults to 2.
     * - wordMin: The minimum length of words that can be hyphenated. Defaults to 6.
     * - quality: The quality of the hyphenation. Can be an integer from 0 (no hyphenation) to 9 (best quality). Defaults to 9.
     * - hyphen: The string to use (e.g. '&shy;', "\u{00AD}", '-')
     *
     * @param array $options The options for the hyphenator.
     * @return Hyphenator The hyphenator instance.
     */
    public function getHyphenator(array $options): Hyphenator
    {
        $this->options
            ->setHyphen($this->getHyphenCharacter($options))
            ->setDefaultLocale($this->getLocale() ?? $this->getOptionValue('defaultLocale', $options, 'de-DE'))
            ->setRightMin($this->getOptionValue('rightMin', $options, 2))
            ->setLeftMin($this->getOptionValue('leftMin', $options, 2))
            ->setWordMin($this->getOptionValue('wordMin', $options, 6))
            ->setQuality($this->getOptionValue('quality', [], 9))
            ->setFilters(['Simple', 'CustomMarkup'])
            ->setTokenizers(['Whitespace', 'Punctuation']);

        // @extensionScannerIgnoreLine
        $this->hyphenator->setOptions($this->options);

        return $this->hyphenator;
    }

    protected function getHyphenCharacter(array $options): string
    {
        $char = $this->getOptionValue('hyphen', $options, '-');

        // The problem is that values from the settings (constant-editor) may not be correctly escaped 
        // as a result, we receive the string here and not the charcode. We try to correct this
        if (preg_match('@\\\\x([0-9a-fA-F]{2})@', $char, $matches)) {
            $char = html_entity_decode('&#x' . $matches[1] . ';', ENT_QUOTES, 'UTF-8');
        }
        if (preg_match('@\\\\u\{?([0-9a-fA-F]{4})\}?@', $char, $matches)) {
            $char = html_entity_decode('&#x' . $matches[1] . ';', ENT_QUOTES, 'UTF-8');
        }

        return $char;
    }

    /**
     * Retrieves the value of a specified option key from provided options or extension configuration.
     *
     * The function first checks the provided options array for the specified key.
     * If the key does not exist in the options array, it then checks the extension
     * configuration with the key under the 'lia_hyphenator' namespace. If the key
     * is not found in either, it returns the provided default value.
     *
     * @param string $key The key for the option to retrieve.
     * @param array $options An array of options to search for the key.
     * @param mixed $default The default value to return if the key is not found.
     *
     * @return mixed The value of the option if found, or the default value if not.
     */
    protected function getOptionValue(string $key, array $options, $default = null): mixed
    {
        return $options[$key] ?? $this->extensionConfiguration->get('lia_hyphenator', $key) ?? $default;
    }

    /**
     * Tries to determine the locale of the current request.
     *
     * It will first check the "language" attribute of the current request.
     * If that does not exist, it will try to get the default language of
     * the current site.
     *
     * @return string|null The locale if found, otherwise null.
     */
    private function getLocale(): ?string
    {
        $language =
            $this->getRequest()?->getAttribute('language')
            ?? $this->getRequest()?->getAttribute('site')?->getDefaultLanguage()
            ?? null;
        if ($language === null) {
            return null;
        }

        return $language->getLocale()->getName();
    }

    /**
     * Gets the current request. If none is available, a request is constructed.
     *
     * @return ServerRequest The current request.
     */
    private function getRequest(): ServerRequest
    {
        if (!empty($GLOBALS['TYPO3_REQUEST'])) {
            return $GLOBALS['TYPO3_REQUEST'];
        }

        return (new ServerRequest())->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE);
    }
}
