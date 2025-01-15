<?php 

/*
* This file is part of the "lia_hyphenator" Extension for TYPO3 CMS.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*/

namespace LIA\LiaHyphenator\Tests\Unit\ViewHelpers;

use LIA\LiaHyphenator\Services\HyphenatorService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test for SimplePrevNextViewHelper
 */
class HyphenatorServiceTest extends UnitTestCase
{
    /**
     * @var ExtensionConfiguration|MockObject
     */
    private $extensionConfigurationMock;

    /**
     * @var HyphenatorService
     */
    private $hyphenatorService;

    /**
     * Have Hyphanator extension loaded
     */
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/lia_hyphenator',
    ];

    /**
     * Creates a subclass TestFinder with some protected
     * functions made public.
     *
     * @return HyphenatorService an accessible proxy
     */
    protected function createAccessibleProxy($args)
    {
        $className = 'TestHyphenatorServiceProxy';
        if (!class_exists($className, false)) {
            eval ('use LIA\LiaHyphenator\Services\HyphenatorService;' .
                'class ' . $className . ' extends HyphenatorService {' .
                '  public function getOptionValue(string $key, array $options, $default = null): mixed {' .
                '    return parent::getOptionValue($key, $options, $default);' .
                '  }' .
                '}');
        }

        return new $className($args);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock of ExtensionConfiguration
        $this->extensionConfigurationMock = $this->createMock(ExtensionConfiguration::class);

        // Inject mock into the class under test
        $this->hyphenatorService = $this->createAccessibleProxy($this->extensionConfigurationMock);
    }

    #[Test]
    public function isHyphenatorServiceInstantiated(): void
    {
        self::assertInstanceOf(HyphenatorService::class, $this->hyphenatorService);
    }

    /**
     * Test if default options work
     */
    #[DataProvider('ensureCorrectConfigurationTestDataProvider')]
    #[Test]
    public function ensureCorrectConfigurationTest($key, $options, $extensionConfiguration, $default, $expectedResult): void
    {
        // Define expected return value for the get method
        $this->extensionConfigurationMock
            ->expects(self::atMost(1))
            ->method('get')
            ->with('lia_hyphenator', $key)
            ->willReturn($extensionConfiguration[$key] ?? null);

        $result = $this->hyphenatorService->getOptionValue($key, $options, $default);

        self::assertSame($expectedResult, $result);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public static function ensureCorrectConfigurationTestDataProvider()
    {
        return [
            // minLeft
            'leftMinValueFromExtensionSettings' => [
                'key' => 'leftMin',
                'options' => [],
                'extensionConfiguration' => ['leftMin' => '2'],
                'default' => '1',
                'expectedResult' => '2', // ExtensionSetting
            ],
            'leftMinValueFromOptions' => [
                'key' => 'leftMin',
                'options' => ['leftMin' => 4],
                'extensionConfiguration' => ['leftMin' => '2'],
                'default' => '1',
                'expectedResult' => 4, // options
            ],
            'leftMinValueFromDefault' => [
                'key' => 'leftMin',
                'options' => ['leftMin' => null],
                'extensionConfiguration' => [],
                'default' => '1',
                'expectedResult' => '1', // default
            ],
            // minRight
            'rightMinValueFromExtensionSettings' => [
                'key' => 'rightMin',
                'options' => [],
                'extensionConfiguration' => ['rightMin' => '2'],
                'default' => '1',
                'expectedResult' => '2', // ExtensionSetting
            ],
            'rightMinValueFromOptions' => [
                'key' => 'rightMin',
                'options' => ['rightMin' => 4],
                'extensionConfiguration' => ['rightMin' => '2'],
                'default' => '1',
                'expectedResult' => 4, // options
            ],
            'rightMinValueFromDefault' => [
                'key' => 'rightMin',
                'options' => ['rightMin' => null],
                'extensionConfiguration' => ['rightMin'],
                'default' => '1',
                'expectedResult' => '1', // default
            ],
            // wordMin
            'wordMinValueFromExtensionSettings' => [
                'key' => 'wordMin',
                'options' => [],
                'extensionConfiguration' => ['wordMin' => '6'],
                'default' => '1',
                'expectedResult' => '6', // ExtensionSetting
            ],
            'wordMinValueFromOptions' => [
                'key' => 'wordMin',
                'options' => ['wordMin' => 4],
                'extensionConfiguration' => ['wordMin' => '6'],
                'default' => '1',
                'expectedResult' => 4, // options
            ],
            'wordMinValueFromDefault' => [
                'key' => 'wordMin',
                'options' => ['wordMin' => null],
                'extensionConfiguration' => ['wordMin'],
                'default' => '1',
                'expectedResult' => '1', // default
            ],
        ];
    }
}
