<?php

namespace App\Controller;

use App\Entity\BandeDessinee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/rechercher/{query}/{page}/{tri}", name="handleSearch")
     */
    public function handleSearch($page, $query, Request $request, $typesGenre, $typesSousGenre, $tri) {

        $nbArticlesParPage = 5;
        $repository = $this->getDoctrine()->getManager()->getRepository('App\Entity\BandeDessinee');
        $bandeDessinees = $repository->getBDDepuisRecherche($query, $page, $nbArticlesParPage);

        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($bandeDessinees) / $nbArticlesParPage),
            'nomRoute' => 'listeBDGenre',
            'paramsRoute' => array()
        );

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
            $query = $formSearch->getData()['query'];

            return $this->redirectToRoute('handleSearch', ['page' => '1', 'query' => $query, 'tri' => 'def']);
        }
        $genreToString = "oeuvres avec ";
        $genreToString .= $query;
        $genreToString .= " dans son titre";

        return $this->render('pages/liste_bd.html.twig', [
            'BandeDessinees' => $bandeDessinees,
            'pagination' => $pagination,
            'GenreToString' => $genreToString,
            'query' => $query,
            'typesGenre' => $typesGenre,
            'typesSousGenre' => $typesSousGenre,
            'formSearch' => $formSearch->createView()
            // envoyer nb resultat
        ]);
    }
}
