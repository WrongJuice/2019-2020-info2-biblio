<?php

namespace App\Tests;

use App\Domain\BDDetail\BDDetailHandler;
use App\Domain\BDDetail\BDDetailQuery;
use App\Entity\BandeDessinee;
use PHPUnit\Framework\TestCase;

class BDDetailTest extends TestCase
{

    public function testDemanderBDDetailAppelRepository()
    {

        $BandeDessinee= new BandeDessinee();
        $BandeDessinee->setTitre('La marche sur la Lune');
        $BandeDessinee->setDescription("Vivez l'aventure de Tom qui part essayer de marcher sur la lune avec ses amis ! Je m'appelle Jean-Jacques Mulusson sur mon Tipee");
        $BandeDessinee->setAuteur('Jean-Jacques Molusson');
        $BandeDessinee->setDateDeParution(new \DateTime(date("Y-m-d H:i:s")));
        $BandeDessinee->setGenre('BD');
        $BandeDessinee->setSousGenre('Aventure');

        $Repository = $this->createMock(\App\Repository\BandeDessineeRepository::class);
        $Repository->expects($this->once())->method('__construct')->willReturn($BandeDessinee);
        $Query = new BDDetailQuery($BandeDessinee->getId());
        $Handler = new BDDetailHandler($Repository);

        $Handler->handle($Query);

        $repository = $this->getDoctrine()->getRepository('App\Entity\BandeDessinee');

    }

}
