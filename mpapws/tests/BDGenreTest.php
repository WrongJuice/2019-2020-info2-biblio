<?php

namespace App\Tests;

use App\Domain\BDGenre\BDGenreHandler;
use App\Domain\BDGenre\BDGenreQuery;
use PHPUnit\Framework\TestCase;

class BDGenreTest extends TestCase
{

    public function testDemanderBDGenreAppelRepository()
    {
        $page = 1;
        $nbPage = 5;
        $genre = 'BD';
        $tri = 'def';

        $Repository = $this->createMock(\App\Repository\BandeDessineeRepository::class);
        $Repository->expects($this->once())->method('getBDGenrePagination');
        $Query = new BDGenreQuery($page, $nbPage, $genre, $tri);
        $Handler = new BDGenreHandler($Repository);

        $Handler->handle($Query);

    }

}
