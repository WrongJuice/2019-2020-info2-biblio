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
            'public/data_test/livre.pdf',
            'livre.pdf',
            'application/pdf',
            null
        );

        $crawler = $client->submitForm('Envoyer',
            ['form[titre]' => 'BD_de_test',
            'form[auteur]' => 'Alfred',
            'form[description]' => 'Ceci est une BD de test',
            'form[genre]' => 'BD',
            'form[sousGenre]' => 'Aventure',
            'form[LivrePDF]' => $book]
        );

    }

}
