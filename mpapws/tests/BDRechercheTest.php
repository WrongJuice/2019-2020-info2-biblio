<?php

namespace App\Tests;

use App\Domain\BDSearchBar\BDSearchBarHandler;
use App\Domain\BDSearchBar\BDSearchBarQuery;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BDRechercheTest extends WebTestCase {
    public function testAfficherBDApresRechercheTitre() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/rechercher/toto/1/def');
        $text = $crawler->filter('.BDlist-link')->text();

        $this->assertEquals('Totoche', $text);
    }

    public function testAfficherBDApresRechercheAuteur() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/rechercher/lucien/1/def');
        $text = $crawler->filter('.BDlist-link')->text();

        $this->assertEquals('Totoche', $text);
    }

    public function testAfficherBDApresRechercheDescription() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/rechercher/foloches/1/def');
        $text = $crawler->filter('.BDlist-link')->text();

        $this->assertEquals('Totoche', $text);
    }
}