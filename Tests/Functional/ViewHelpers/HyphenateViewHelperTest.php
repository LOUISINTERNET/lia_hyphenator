<?php

/*
 * This file is part of the "lia_hyphenator" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace LIA\LiaHyphenator\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3Fluid\Fluid\View\TemplateView;

/**
 * Test for SimplePrevNextViewHelper
 */
final class HyphenateViewHelperTest extends FunctionalTestCase
{

    protected array $configurationToUseInTestInstance = [
        'EXTENSIONS' => [
            'lia_hyphenator' => [
                'defaultLocale' => 'de-DE',
                'leftMin' => '2',
                'quality' => '9',
                'rightMin' => '2',
                'wordMin' => '4',
            ],
        ],
    ];

    /**
     * Extension needed for this test
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/lia_hyphenator',
    ];

    /**
     * Data provider
     *
     * @return array
     */
    public static function defaultOptionsTestDataProvider()
    {
        return [
            'noHyphationBecauseToShortValuePerAttribute' => [
                'template' => '<lih:hyphenate value="In An Um Es"></lih:hyphenate>',
                'expectedResult' => 'In An Um Es',
            ],
            'OneWordHyphantionValuePerAttribute' => [
                'template' => '<lih:hyphenate value="Donaudampfschifffahrt"></lih:hyphenate>',
                'expectedResult' => 'Do&shy;nau&shy;dampf&shy;schiff&shy;fahrt',
            ],
            'noHyphationBecauseToShortValuePerTag' => [
                'template' => '<lih:hyphenate>In An Um Es</lih:hyphenate>',
                'expectedResult' => 'In An Um Es',
            ],
            'OneWordHyphantionValuePerTag' => [
                'template' => '<lih:hyphenate>Donaudampfschifffahrt</lih:hyphenate>',
                'expectedResult' => 'Do&shy;nau&shy;dampf&shy;schiff&shy;fahrt',
            ],

            'noHyphationBecauseToShortInline' => [
                'template' => '{lih:hyphenate(value:"In An Um Es")}',
                'expectedResult' => 'In An Um Es',
            ],
            'OneWordHyphantionInline' => [
                'template' => '{lih:hyphenate(value:"Donaudampfschifffahrt")}',
                'expectedResult' => 'Do&shy;nau&shy;dampf&shy;schiff&shy;fahrt',
            ],
            'noHyphationBecauseToShortPipe' => [
                'template' => '{f:variable(name:"s",value:"In An Um Es")}{s -> lih:hyphenate()}',
                'expectedResult' => 'In An Um Es',
            ],
            'OneWordHyphantionPipe' => [
                'template' => '{f:variable(name:\'s\',value:"Donaudampfschifffahrt")}{s -> lih:hyphenate()}',
                'expectedResult' => 'Do&shy;nau&shy;dampf&shy;schiff&shy;fahrt',
            ],
            'minLeftPerTemplateTo5shouldNotHyphen' => [
                'template' => '<lih:hyphenate value="Kommen wird nicht getrennt hier" leftMin="5"></lih:hyphenate>',
                'expectedResult' => 'Kommen wird nicht getrennt hier',
            ],
            'minLeftPerTemplateTo5shouldHyphen' => [
                'template' => '<lih:hyphenate value="Donaudampfschifffahrt" leftMin="5"></lih:hyphenate>',
                'expectedResult' => 'Donau&shy;dampf&shy;schiff&shy;fahrt',
            ],

        ];
    }

    /**
     * Data provider
     *
     * @return array
     */
    public static function noValueDataProvider()
    {
        return [
            'noValueByTag' => [
                'template' => '<lih:hyphenate></lih:hyphenate>',
            ],
            'noValueBySelfClosingTag' => [
                'template' => '<lih:hyphenate />',
            ],
            'noValuebyInline' => [
                'template' => '{lih:hyphenate()}',
            ],
            'noValuebyInlinePiped' => [
                'template' => '{f:variable(name:\'s\',value:null)} {s -> lih:hyphenate()}',
            ],
        ];
    }

    #[DataProvider('noValueDataProvider')]
    #[Test]
    public function callWitoNoOrWrongArgumentsTest($template): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/^HyphenViewHelper: Incompatible/');
        $this->expectExceptionCode(1733408400);

        $template = '<html xmlns:lih="http://typo3.org/ns/LIA/LiaHyphenator/ViewHelpers" data-namespace-typo3-fluid="true">' . $template . '</html>';
        $context = $this->get(RenderingContextFactory::class)->create();
        $context->getTemplatePaths()->setTemplateSource($template);
        (new TemplateView($context))->render();
    }

    /**
     * Test if default options work
     */
    #[DataProvider('defaultOptionsTestDataProvider')]
    #[Test]
    public function defaultOptionsTest($template, $expectedResult): void
    {
        $template = '<html xmlns:lih="http://typo3.org/ns/LIA/LiaHyphenator/ViewHelpers" data-namespace-typo3-fluid="true"><f:format.raw>' . $template . '</f:format.raw></html>';
        $context = $this->get(RenderingContextFactory::class)->create();

        $context->getTemplatePaths()->setTemplateSource($template);
        $result = (new TemplateView($context))->render();
        self::assertEquals($expectedResult, $result);
    }
}
