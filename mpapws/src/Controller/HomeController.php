<?php

namespace App\Controller;

use App\Domain\BDTendanceHome\BDTendanceHomeHandler;
use App\Domain\BDTendanceHome\BDTendanceHomeQuery;

use App\Entity\Commentaire;
use App\Entity\Notes;
use App\Entity\BandeDessinee;

use App\Repository\BandeDessineeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class HomeController extends AbstractController{

    public function __construct(Environment $twig)
    {

        $this->twig = $twig;

    }


    public function index($typesGenre, $typesSousGenre, BDTendanceHomeHandler $BDTendanceHomeHandler, Request $request):Response{

        /* Récupère les BD Recentes grâce au Handler */

        $BDTendances = $BDTendanceHomeHandler->handle(new BDTendanceHomeQuery('BD')); // Récupère les BD Tendances

        /* Récupère les Mangas Tendances grâce au Handler */

        $mangasTendances = $BDTendanceHomeHandler->handle(new BDTendanceHomeQuery('Mangas')); // Récupère les Mangas Tendances

        /* Récupère les Comics Tendances grâce au Handler */

        $comicsTendances = $BDTendanceHomeHandler->handle(new BDTendanceHomeQuery('Comics')); // Récupère les Comics Tendances

        $formSearch = $this->createFormBuilder()
            ->add('query', TextType::class)
            ->add('rechercher', SubmitType::class, [
                'attr' => [
                    'class' => "btn btn-outline-light"
                ]
            ])
            ->getForm();

        $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $motCle = $formSearch->getData()['query'];

            return $this->redirectToRoute('handleSearch', ['page' => '1', 'query' => $motCle, 'tri' => 'def']);
        }

        return $this->render('pages/home.html.twig', [
            'BDTendances' => $BDTendances,
            'MangasTendances' => $mangasTendances,
            'ComicsTendances' => $comicsTendances,
            'typesGenre' => $typesGenre,
            'typesSousGenre' => $typesSousGenre,
            'formSearch' => $formSearch->createView()
        ]);
    }


}

