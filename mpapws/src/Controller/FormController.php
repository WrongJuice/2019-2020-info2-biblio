<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Notes;
use App\Entity\BandeDessinee;

use App\Repository\BandeDessineeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Imagick;
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
use Symfony\Component\Validator\Constraints\File;


class FormController extends AbstractController{

    public function __construct(Environment $twig)
    {

        $this->twig = $twig;

    }

    /**
     * @Route("/formulaire", name="formulaire")
     */

    public function formulaire(Request $request, EntityManagerInterface $entityManager, $typesGenre, $typesSousGenre){

        // On créé notre BD
        $BD = new BandeDessinee();
        $BD->setDateDeParution(new \DateTime('now'));


        $typesGenreTab = [];
        $typesSousGenreTab= [];

            foreach( $typesGenre as $typeGenre){
                array_push($typesGenreTab, [$typeGenre => $typeGenre]);
        }
        foreach( $typesSousGenre as $typeSousGenre){
            array_push($typesSousGenreTab, [$typeSousGenre => $typeSousGenre]);
        }

        // On créé notre FormBuilder et on lui ajoute directement les champs
        $form = $this->createFormBuilder($BD)
            ->add('titre', TextType::class,['label'  => 'Titre du livre',])
            ->add('auteur', TextType::class, ['label'  => 'Auteur',])
            ->add('description', TextareaType::class,['label'  => 'Description' , 'attr' => [
                'size' => "100"]
            ])
            ->add('genre', ChoiceType::class, [
                'choices' => $typesGenreTab,]
                , ['label'  => 'Genre',])
            ->add('sousGenre', ChoiceType::class, [
                'choices' => $typesSousGenreTab,
                ], ['label'  => 'Sous-genre',])
            ->add('LivrePDF', FileType::class, ['label'  => 'Livre au format PDF (Taille maximum autorisée : 100 mo)', 'mapped' => false, 'required' => false, 'constraints' => [
                new File([
                    'maxSize' => '100M',
                    'mimeTypes' => [ 'application/pdf', 'application/x-pdf'],
                    'mimeTypesMessage' => 'Nan mais sérieux quoi, veuillez uploader un fichier pdf valide !',
                    'uploadIniSizeErrorMessage' => 'Votre BD dépasse la taille maximum autorisée, veuillez faire un tome 2 et uploader un fichier plus léger !',
                    'uploadFormSizeErrorMessage' => 'Votre BD dépasse la taille maximum autorisée, veuillez faire un tome 2 et uploader un fichier plus léger !'])
                ],
                'attr' => ['accept' => "application/pdf"]
            ])
            ->add('save', SubmitType::class, [
                'label'  => 'Envoyer'
            ])
            ->getForm();

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

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $form['LivrePDF']->getData() != null) {

            $BD = $form->getData();

            dump($BD);

            $entityManager->persist($BD);
            $entityManager->flush();

            $uploadedPDF = ($form['LivrePDF']->getData());
            $destination = $this->getParameter('kernel.project_dir').'/public/data/' . $BD->getId();

            $filename = pathinfo($uploadedPDF->getClientOriginalName() . '.pdf' , PATHINFO_FILENAME);
            $uploadedPDF->move($destination , $filename);
            rename('./data/' . $BD->getId() . '/' . $uploadedPDF->getClientOriginalName() , './data/' . $BD->getId() .'/livre.pdf');

            //Décomposition du pdf
            $imagick = new Imagick();
            $bookPath = './data/' . $BD->getId() .'/';
            $imagick->readImage($bookPath . 'livre.pdf[0]');
            $imagick->writeImage($bookPath . 'affiche.jpg');
            $imagick->readImage($bookPath . 'livre.pdf');
            $numberOfPage = $imagick->getNumberImages();
            if($numberOfPage>=2){
                for ($i=1; $i!=6; $i++) {
                    if (($i+1)<$numberOfPage){
                        $imagick->readImage($bookPath . 'livre.pdf[' . $i . ']');
                        $imagick->writeImage($bookPath . 'p' . (string)$i . '.jpg');
                    }
                }
            }

            return $this->render('pages/task_success.html.twig', [
                'BandeDessinee'=> $BD,
                'typesGenre' => $typesGenre,
                'typesSousGenre' => $typesSousGenre,
                'formSearch' => $formSearch->createView(),
            ]);
        }
        return $this->render('pages/formulaire.html.twig', [
            'form'=> $form->createView(),
            'formSearch' => $formSearch->createView(),
            'typesGenre' => $typesGenre,
            'typesSousGenre' => $typesSousGenre
        ]);

    }

}

