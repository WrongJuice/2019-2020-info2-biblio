<?php

namespace App\Tests;

use App\Domain\BDTendance\BDTendanceHandler;
use App\Domain\BDTendance\BDTendanceQuery;
use PHPUnit\Framework\TestCase;

class BDTendancesTest extends TestCase
{

    public function testDemanderBDTendancesAppelRepository()
    {
        $page = 1;
        $nbPage = 5;
        $genre = 'BD';
        $tri = 'def';

        $Repository = $this->createMock(\App\Repository\BandeDessineeRepository::class);
        $Repository->expects($this->once())->method('getBDTendancesPagination');
        $Query = new BDTendanceQuery($page, $nbPage, $genre, $tri);
        $Handler = new BDTendanceHandler($Repository);

        $Handler->handle($Query);

    }

}
