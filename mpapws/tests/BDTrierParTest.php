<?php

namespace App\Tests;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class BDTrierParTest extends WebTestCase
{
    public function testOrdreBDApresTriOld() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/liste/BD/1/old');

        $text = $crawler->filter('.font-weight-bold')->text();
        var_dump($text);
        $this->assertEquals('05/05/2019 •

                                                    4,56 / 5', $text);

    }
}