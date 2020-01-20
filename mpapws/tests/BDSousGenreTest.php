<?php

namespace App\Tests;

use App\Domain\BDSousGenre\BDSousGenreHandler;
use App\Domain\BDSousGenre\BDSousGenreQuery;
use PHPUnit\Framework\TestCase;

class BDSousGenreTest extends TestCase
{

    public function testDemanderBDSousGenreAppelRepository()
    {
        $page = 1;
        $nbPage = 5;
        $genre = 'BD';
        $sousGenre = 'Humour';
        $tri = 'def';

        $Repository = $this->createMock(\App\Repository\BandeDessineeRepository::class);
        $Repository->expects($this->once())->method('getBDSousGenrePagination');
        $Query = new BDSousGenreQuery($page, $nbPage, $genre, $sousGenre, $tri);
        $Handler = new BDSousGenreHandler($Repository);

        $Handler->handle($Query);

    }

}
