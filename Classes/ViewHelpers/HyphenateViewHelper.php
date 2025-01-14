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

/**
 * This ViewHelper can be used to apply hyphenation to text.
 *
 * Examples
 * ========
 *
 * Usage with simple StringContent
 * --------------------------------
 *
 * The ViewHelper can be used as tag as well as with inline syntax, here are some Examples
 * used with a Variable set like this: 
 * 
 * ..  code-block:: html
 *     <f:variable name="stringContent">Some textcontent to apply hyphention to</f:variable>
 * 
 * ViewHelper usage:
 * 
 * ..  code-block:: fluid
 *     {stringContent -> lih:hyphenate()}
 * 
 * or
 * 
 * ..  code-block:: fluid
 *     {lih:hyphenate(value: stringContent)}
 * 
 * or
 * 
 * ..  code-block:: html
 *     <lih:hyphenate value="{stringContent}"></lih:hyphenate>
 * 
 * or
 * 
 * ..  code-block:: html
 *     <lih:hyphenate>{stringContent}</lih:hyphenate>
 *
 * **The output** is the same for all examples:
 * 
 * .. code-block:: plaintext
 *
 *     Some text&shy;con&shy;tent to apply hy&shy;phen&shy;ti&shy;on to
 *
 * Example with arguments
 * ----------------------
 *
 * ..  code-block:: html
 *     <lih:hyphenate value="{stringContent}" leftMin="2" rightMin="3" wordMin="4" defaultLocale="de_CH"></lih:hyphenate>
 * 
 * This will use a german hyphenation dictionary with the settings for leftMin, rightMin and wordMin set as specified.
 * 
 * Inline example:
 * 
 * ..  code-block:: fluid
 *     {stringContent -> lih:hyphenate(leftMin: 2, rightMin: 3, wordMin: 5)}
 * 
 * This will set the values for the arguments leftMin, rightMin and wordMin.
 *
 */
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
