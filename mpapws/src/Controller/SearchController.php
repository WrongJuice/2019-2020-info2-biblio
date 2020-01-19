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
        $bandeDessinees = $repository->getBDDepuisRecherche($query, $page, $nbArticlesParPage, $tri);

        $pagination = array(
            'page' => $page,
            'nbPages' => ceil(count($bandeDessinees) / $nbArticlesParPage),
            'nomRoute' => 'handleSearch',
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
        $genreToString = "résultats de la recherche pour : ";
        $genreToString .= $query;

        // Si il n'y à pas de BD : Retourne sur la page no_result
        if (count($bandeDessinees) == 0)
        {
            return $this->render('pages/no_result.html.twig', [
                'typesGenre' => $typesGenre,
                'typesSousGenre' => $typesSousGenre,
                'formSearch' => $formSearch->createView()
            ]);
        }

        // Calcul du nombre de résultat
        $nbResultats = count($bandeDessinees);

        return $this->render('pages/liste_bd.html.twig', [
            'BandeDessinees' => $bandeDessinees,
            'pagination' => $pagination,
            'GenreToString' => $genreToString,
            'query' => $query,
            'typesGenre' => $typesGenre,
            'typesSousGenre' => $typesSousGenre,
            'formSearch' => $formSearch->createView(),
            'tri' => $tri,
            'nbResultats' => $nbResultats
        ]);
    }
}
