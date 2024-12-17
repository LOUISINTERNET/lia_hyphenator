<?php 

/*
* This file is part of the "lia_hyphenator" Extension for TYPO3 CMS.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*/

namespace LIA\LiaHyphenator\ViewHelpers;

use LIA\LiaHyphenator\Services\HyphenatorService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class HyphenateViewHelper extends AbstractViewHelper
{
    /**
     * Initialize all the needed arguments.
     */
    public function initializeArguments(): void
    {
        $this->registerArgument('value', 'string', 'Text to apply hyphenation to.', false);
        $this->registerArgument('leftMin', 'int', 'How many characters have to be left unhyphenated to the left of the word. This has to be an integer value.');
        $this->registerArgument('rightMin', 'int', 'How many characters have to be left unhyphenated to the right of the word. This has to be an integer value');
        $this->registerArgument('wordMin', 'int', 'Words under the given length will not be hyphenated altogether. It makes sense to set option to a higher value than the sum of rightMin and leftMin.');
        $this->registerArgument('quality', 'int', 'How good shal the hyphenation be. The higher the number the better. THis can be any integer from 0 (no Hyphenation at all) through 9 (berst hyphernation). This defaults to 9. .. warning:: Change this only if you know what you do!');
        $this->registerArgument('defaultLocale', 'string', 'This parameter defines what dictionary to use by default for hyphenation. Defaults to the current locale.');
    }

    /**
     * Renders the hyphenated content.
     *
     * @return string Hyphenated content.
     *
     * @throws \Exception
     */
    public function render(): string
    {
        $value = $this->renderChildren();

        if ($value === null || !is_string($value)) {
            throw new \Exception(
                'HyphenViewHelper: Incompatible or missing content.',
                1733408400,
            );
        }

        $hyphenatorService = GeneralUtility::makeInstance(HyphenatorService::class);
        $hyphenator = $hyphenatorService->getHyphenator($this->arguments);
        $value = $hyphenator->hyphenate($value);

        if (is_array($value)) {
            return reset($value);
        }

        return $value;
    }

    /**
     * Gets the name of the content argument
     *
     * @return string the name of the content argument
     */
    public function getContentArgumentName(): string
    {
        return 'value';
    }
}
