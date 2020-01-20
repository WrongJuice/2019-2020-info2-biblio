<?php

namespace App\Tests;

use App\Controller\FormController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FormControllerTest extends WebTestCase
{

    public function testShowPost()
    {
        $client = static::createClient();

        $client->request('GET', '/formulaire');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}
