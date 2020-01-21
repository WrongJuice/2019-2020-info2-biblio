<?php

namespace App\Tests;

use App\Controller\FormController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FormControllerTest extends WebTestCase
{

    public function testPageIsSuccessful()
    {
        $client = static::createClient();

        $client->request('GET', '/formulaire');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testSubmitForm()
    {
        $client = static::createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/formulaire');



        $book = new UploadedFile(
            'public/data_test/TP - TS3.pdf',
            'TP - TS3.pdf',
            'application/pdf',
            null
        );

        //$form = $crawler->selectButton('Envoyer')->form();

        //$form = $crawler->filter('.formUp')->form();
        //$form['titre'] = 'BD_de_test';

        $crawler = $client->submitForm('Envoyer',
            ['form[titre]' => 'BD_de_test',
            'form[auteur]' => 'Alfred',
            'form[description]' => 'Ceci est une BD de test',
            'form[genre]' => 'BD',
            'form[sousGenre]' => 'Aventure',
            'form[LivrePDF]' => $book]
        );

        //$form = $crawler->selectButton('save')->form();

        // set some values
        //$form['titre'] = 'BD_Test';

    }

}
