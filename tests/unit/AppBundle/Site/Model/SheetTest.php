<?php

declare(strict_types=1);

namespace AppBundle\Tests\Site\Model;

use AppBundle\Site\Model\Sheet;
use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SheetTest extends TestCase
{
    #[DataProvider('dates')]
    public function testIsPublished(?DateTime $start, ?DateTime $end, bool $expected): void
    {
        $sheet = new Sheet();
        $sheet->setPublicationStart($start);
        $sheet->setPublicationEnd($end);

        self::assertEquals($expected, $sheet->isPublished(new DateTime('2026-01-15')));
    }

    public static function dates(): \Generator
    {
        yield 'aucune date' => [null, null, true];
        yield 'début null, fin dans le futur' => [null, new DateTime('2026-02-01'), true];
        yield 'début null, fin dans le passé' => [null, new DateTime('2026-01-01'), false];
        yield 'début dans le passé, fin null' => [new DateTime('2026-01-01'), null, true];
        yield 'début dans le futur, fin null' => [new DateTime('2026-02-01'), null, false];
        yield 'dans la plage' => [new DateTime('2026-01-01'), new DateTime('2026-02-01'), true];
        yield 'avant la plage' => [new DateTime('2026-01-20'), new DateTime('2026-02-01'), false];
        yield 'après la plage' => [new DateTime('2026-01-01'), new DateTime('2026-01-10'), false];
        yield 'exactement à la date de début' => [new DateTime('2026-01-15'), null, true];
        yield 'exactement à la date de fin' => [null, new DateTime('2026-01-15'), true];
    }
}
