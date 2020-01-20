<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Notes;
use App\Entity\BandeDessinee;

use App\Domain\BDDetail\BDDetailHandler;
use App\Domain\BDDetail\BDDetailQuery;

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


class DetailController extends AbstractController{

    public function __construct(Environment $twig)
    {

        $this->twig = $twig;

    }

    /**
     * @Route("/{genre}/{id}/", name="BDDetaillee")
     */

    public function BDDetaillee($id, Request $request, EntityManagerInterface $entityManager, BDDetailHandler $BDDetailHandler, $typesGenre, $typesSousGenre)
    {
        /*Récupère les infos de la BD */
        $bandeDessinee = $BDDetailHandler->handle(new BDDetailQuery($id));

        /* Ajoute les planches seulement si elles existent */

        $planches = [];

        $bookPath = './data/' . $bandeDessinee->getId() .'/';

        if (file_exists($bookPath . 'p1.jpg')) {
            array_push($planches, $bandeDessinee->getPlanche1());
        }

        if (file_exists($bookPath . 'p2.jpg')) {
            array_push($planches, $bandeDessinee->getPlanche2());
        }

        if (file_exists($bookPath . 'p3.jpg')) {
            array_push($planches, $bandeDessinee->getPlanche3());
        }

        if (file_exists($bookPath . 'p4.jpg')) {
            array_push($planches, $bandeDessinee->getPlanche4());
        }

        if (file_exists($bookPath . 'p5.jpg')) {
            array_push($planches, $bandeDessinee->getPlanche5());
        }

        /* Inverse les commentaire pour avoir les plus récents en premiers */
        $commentaires = $bandeDessinee->getSesCommentaires();
        $commentairesReverse = [];
        foreach($commentaires as $commentaire){
            array_unshift($commentairesReverse, $commentaire);
        }

        /* Récupère le nombre de commentaires */
        $nbCommentaires = $bandeDessinee->getNbCommentaires();

        /* On s'occupe du formulaire d'envoi de commentaires */

        $commentaire = new Commentaire();
        $commentaire->setSabandeDessinee($bandeDessinee);
        $commentaire->setDate(new \DateTime('now'));

        $formComment = $this->createFormBuilder($commentaire)
            ->add('Auteur', TextType::class,['attr' => ['placeholder' => 'Pseudonyme',]])
            ->add('Contenu', TextareaType::class, ['attr' => ['placeholder' => 'Ajouter un commentaire public...',]])
            ->add('save', SubmitType::class, ['label'  => 'Valider',])
            ->getForm();

        $formComment->handleRequest($request);

        /* Si le commentaire est envoyé, recharge la page */
        if ($formComment->isSubmitted() && $formComment->isValid()) {


            $commentaire = $formComment->getData();
            dump($commentaire);

            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('BDDetaillee', ['id' => $bandeDessinee->getId(), 'genre' => $bandeDessinee->getGenre()]);
        }

        /* On s'occupe du formulaire d'envoi de note */

        $note = new Notes();
        $note->setSabandeDessinee($bandeDessinee);

        $formNote = $this->createFormBuilder($note)
            ->add('Valeur', ChoiceType::class, [
                'choices' => [0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4',
                    5  =>'5'],],['choice-label' => 'Ajouter une note',], ['placeholder' => 'Salut'])
            ->add('save', SubmitType::class, ['label'  => 'Valider',])
            ->getForm();

        $formNote->handleRequest($request);

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

        /* Si la note envoyé, recharge la page */
        if ($formNote->isSubmitted() && $formNote->isValid()) {


            $note = $formNote->getData();
            dump($note);

            $entityManager->persist($note);
            $entityManager->flush();
            $bandeDessinee->setNoteMoyenne();

            return $this->redirectToRoute('BDDetaillee', ['id' => $bandeDessinee->getId(), 'genre' => $bandeDessinee->getGenre() ]);
        }

        return $this->render('pages/bd_detaillee.html.twig', [
            'formComment'=> $formComment->createView(),
            'formNote'=> $formNote->createView(),
            'BandeDessinee' => $bandeDessinee,
            'Commentaires' => $commentairesReverse,
            'nbCommentaires' => $nbCommentaires,
            'Planches' => $planches,
            'typesGenre' => $typesGenre,
            'typesSousGenre' => $typesSousGenre,
            'formSearch' => $formSearch->createView()
        ]);
    }


}

