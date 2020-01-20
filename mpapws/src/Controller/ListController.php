<?php

namespace App\Controller;

use App\Domain\BDGenre\BDGenreHandler;
use App\Domain\BDGenre\BDGenreQuery;
use App\Domain\BDSousGenre\BDSousGenreHandler;
use App\Domain\BDSousGenre\BDSousGenreQuery;
use App\Domain\BDTendance\BDTendanceHandler;
use App\Domain\BDTendance\BDTendanceQuery;

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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\Tools\Pagination\Paginator;


class ListController extends AbstractController{

    public function __construct(Environment $twig)
    {

        $this->twig = $twig;

    }


    /**
     * @Route("/liste/{genre}/{page}/{tri}", requirements={"page" = "\d+"}, name="listeBDGenre")
     */

    public function listeBDGenre($genre, $page, BDGenreHandler $BDGenreHandler, $nbArticlesParPage, $typesGenre, $typesSousGenre, $tri, Request $request)
    {
        /* Récupère la liste des BD selon un genre */

        /* Crée un système de pagination avec 5 BD par page */

        $bandeDessinees = $BDGenreHandler->handle(new BDGenreQuery($page, $nbArticlesParPage, $genre, $tri)); // Récupère les BD



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

        // Si mauvais paramètre de route : 404
        if(!in_array($genre, $typesGenre, $tri))
        {
            throw new NotFoundHttpException();
        }

        // Calcul du nombre de résultat
        $nbResultats = count($bandeDessinees);

        // Si il n'y à pas de BD : Retourne sur la page no_result
        if ($nbResultats == 0)
        {
            return $this->render('pages/no_result.html.twig', [
                'typesGenre' => $typesGenre,
                'typesSousGenre' => $typesSousGenre,
                'formSearch' => $formSearch->createView()
            ]);
        }


        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($bandeDessinees) / $nbArticlesParPage),
            'nomRoute' => 'listeBDGenre',
            'paramsRoute' => array( 'BandeDessinees' => $bandeDessinees,
                'genre' => $genre,
                'tri' => $tri,)
        );

        return $this->render('pages/liste_bd.html.twig', [
            'BandeDessinees' => $bandeDessinees,
            'GenreToString' => $genre,
            'genre' => $genre,
            'pagination' => $pagination,
            'typesGenre' => $typesGenre,
            'typesSousGenre' => $typesSousGenre,
            'tri' => $tri,
            'formSearch' => $formSearch->createView(),
            'nbResultats' => $nbResultats
        ]);
    }

    /**
     * @Route("/liste/{genre}/Tendances/{page}/{tri}", name="listeBDTendances")
     */

    public function listeBDTendances($genre, $page, BDTendanceHandler $BDTendanceHandler, $nbArticlesParPage, $typesGenre, $typesSousGenre, Request $request, $tri)
    {
        /* Récupère la liste des BD Recentes selon un genre */

        /* Crée un système de pagination avec 5 BD par page */

        $BDTendances = $BDTendanceHandler->handle(new BDTendanceQuery($page, $nbArticlesParPage, $genre, $tri)); // Récupère les BD Récentes



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

        // Permet d'afficher le genre consulté
        $genreToString = $genre;
        $genreToString .= ' Tendances';

        // Si mauvais paramètre de route : 404
        if(!in_array($genre, $typesGenre))
        {
            throw new NotFoundHttpException();
        }

        // Calcul du nombre de résultat
        $nbResultats = count($BDTendances);

        // Si il n'y à pas de BD : Retourne sur la page no_result
        if ($nbResultats == 0)
        {
            return $this->render('pages/no_result.html.twig', [
                'typesGenre' => $typesGenre,
                'typesSousGenre' => $typesSousGenre,
                'formSearch' => $formSearch->createView()
            ]);
        }



        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($BDTendances) / $nbArticlesParPage),
            'nomRoute' => 'listeBDTendances',
            'paramsRoute' => array('genre' => $genre, 'tri' => $tri)
        );

        return $this->render('pages/liste_bd.html.twig', [
            'BandeDessinees' => $BDTendances,
            'GenreToString' => $genreToString,
            'genre' => $genre,
            'pagination' => $pagination,
            'typesGenre' => $typesGenre,
            'typesSousGenre' => $typesSousGenre,
            'formSearch' => $formSearch->createView(),
            'nbResultats' => $nbResultats
        ]);
    }

    /**
     * @Route("/liste/{genre}/{sousGenre}/{page}/{tri}", name="listeBDSousGenre")
     */

    public function listeBDSousGenre($genre, $sousGenre, $page, BDSousGenreHandler $BDSousGenreHandler, $nbArticlesParPage, $typesGenre, $typesSousGenre, $tri, Request $request)
    {
        /* Récupère la liste des BD selon un genre et un sous genre */

        /* Crée un système de pagination avec 5 BD par page */

        $bandeDessinees = $BDSousGenreHandler->handle(new BDSousGenreQuery($page, $nbArticlesParPage, $genre, $sousGenre, $tri)); // Récupère les BD



        // Permet d'afficher le genre consulté
        $genreToString = $genre;
        $genreToString .= ' ';
        $genreToString .= $sousGenre;

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

        // Si mauvais paramètre de route : 404
        if(!in_array($genre, $typesGenre, $tri) or !in_array($sousGenre, $typesSousGenre, $tri))
        {
            throw new NotFoundHttpException();
        }

        // Calcul du nombre de résultat
        $nbResultats = count($bandeDessinees);

        // Si il n'y à pas de BD : Retourne sur la page no_result
        if ($nbResultats == 0)
        {
            return $this->render('pages/no_result.html.twig', [
                'typesGenre' => $typesGenre,
                'typesSousGenre' => $typesSousGenre,
                'formSearch' => $formSearch->createView()
            ]);
        }



        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($bandeDessinees) / $nbArticlesParPage),
            'nomRoute' => 'listeBDSousGenre',
            'paramsRoute' => array('genre' => $genre,
                'sousGenre' => $sousGenre,
                'tri' => $tri,)
        );

        // Si il y une BD : Retourne sur la page liste_bd
        return $this->render('pages/liste_bd.html.twig', [
            'BandeDessinees' => $bandeDessinees,
            'GenreToString' => $genreToString,
            'genre' => $genre,
            'sousGenre' => $sousGenre,
            'typesGenre' => $typesGenre,
            'typesSousGenre' => $typesSousGenre,
            'tri' => $tri,
            'formSearch' => $formSearch->createView(),
            'nbResultats' => $nbResultats,
            'pagination' => $pagination
        ]);
    }

}

