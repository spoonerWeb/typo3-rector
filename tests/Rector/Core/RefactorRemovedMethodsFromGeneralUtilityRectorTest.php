<?php

declare(strict_types=1);

namespace Ssch\TYPO3Rector\Tests\Rector\Core;

use Iterator;
use Ssch\TYPO3Rector\Rector\Core\Utility\RefactorRemovedMethodsFromGeneralUtilityRector;
use Ssch\TYPO3Rector\Tests\AbstractRectorWithConfigTestCase;

final class RefactorRemovedMethodsFromGeneralUtilityRectorTest extends AbstractRectorWithConfigTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFile($file);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/remove_generalutility_methods.php.inc'];
    }

    protected function getRectorClass(): string
    {
        return RefactorRemovedMethodsFromGeneralUtilityRector::class;
    }
}
