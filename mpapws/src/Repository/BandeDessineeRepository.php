<?php

namespace App\Repository;

use App\Domain\BandeDessineeInterface;
use App\Entity\BandeDessinee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @method BandeDessinee|null find($id, $lockMode = null, $lockVersion = null)
 * @method BandeDessinee|null findOneBy(array $criteria, array $orderBy = null)
 * @method BandeDessinee[]    findAll()
 * @method BandeDessinee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BandeDessineeRepository extends ServiceEntityRepository implements  BandeDessineeInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BandeDessinee::class);
    }

    /* Repository Lié a la page d'accueil */
    public function getBDTendancesForHome($genre)
    {
        /* Récupère les BD sorties il y a moins de deux mois et selon un genre*/

        $twoMonths = new \DateTime(date("Y-m-d H:i:s"));
        $twoMonths->modify('-2 months');

        $QueryBuilder = $this->createQueryBuilder('BD')
            ->Where('BD.DateDeParution > :twoMonths')
            ->andWhere('BD.Genre = :genre')
            ->andWhere('BD.estPopulaire = 1')
            ->orderBy('BD.NoteMoyenne', 'DESC')
            ->setParameter('twoMonths', $twoMonths)
            ->setParameter('genre', $genre);

        $BandeDessinees = $QueryBuilder->getQuery()->setMaxResults(5)->getResult();

        return $BandeDessinees;
    }

    /* Repository lié à la page toutes les BD Tendances */
    public function getBDTendancesPagination($page, $nbMaxParPage, $genre, $tri)
    {
        /* Récupère les BD sorties il y a moins de deux mois et selon un genre*/

        $twoMonths = new \DateTime(date("Y-m-d H:i:s"));
        $twoMonths->modify('-2 months');

        if ($tri == "pos") {
            $QueryBuilder = $this->createQueryBuilder('BD')
                ->Where('BD.DateDeParution > :twoMonths')
                ->andWhere('BD.Genre = :genre')
                ->andWhere('BD.estPopulaire = 1')
                ->orderBy('BD.NoteMoyenne', 'DESC')
                ->setParameter('twoMonths', $twoMonths)
                ->setParameter('genre', $genre);

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if (($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }

        if ($tri == "new" || $tri == "def") {
            $QueryBuilder = $this->createQueryBuilder('BD')
                ->Where('BD.DateDeParution > :twoMonths')
                ->andWhere('BD.Genre = :genre')
                ->andWhere('BD.estPopulaire = 1')
                ->orderBy('BD.DateDeParution', 'DESC')
                ->setParameter('twoMonths', $twoMonths)
                ->setParameter('genre', $genre);

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if (($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }

        if ($tri == "old") {
            $QueryBuilder = $this->createQueryBuilder('BD')
                ->Where('BD.DateDeParution > :twoMonths')
                ->andWhere('BD.Genre = :genre')
                ->andWhere('BD.estPopulaire = 1')
                ->orderBy('BD.DateDeParution', 'ASC')
                ->setParameter('twoMonths', $twoMonths)
                ->setParameter('genre', $genre);

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if (($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }
    }

    /*Repository lié à la page toutes les BD d'un genre */
    /*Repository lié à la page toutes les BD d'un genre */
    public function getBDGenrePagination($page, $nbMaxParPage, $genre, $tri)
    {

        if ($tri == "pos") {
            $QueryBuilder = $this->createQueryBuilder('BD')
                ->where('BD.Genre = :genre')
                ->orderBy('BD.NoteMoyenne', 'DESC')
                ->setParameter('genre', $genre);

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if (($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }
        if ($tri == "old" ) {
            $QueryBuilder = $this->createQueryBuilder('BD')
                ->where('BD.Genre = :genre')
                ->orderBy('BD.DateDeParution', 'ASC')
                ->setParameter('genre', $genre);

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if (($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }
        if ($tri == "new" || $tri == "def") {
            $QueryBuilder = $this->createQueryBuilder('BD')
                ->where('BD.Genre = :genre')
                ->orderBy('BD.DateDeParution', 'DESC')
                ->setParameter('genre', $genre);

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if (($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }

    }

    /* Repository lié à la page toutes les BD d'un sous genre */
    public function getBDSousGenrePagination($page, $nbMaxParPage, $genre, $sousGenre, $tri)
    {
        if ($tri == "pos"){
            $QueryBuilder = $this->createQueryBuilder('BD')
                ->where('BD.Genre = :genre')
                ->andWhere('BD.SousGenre = :sousGenre')
                ->orderBy('BD.NoteMoyenne', 'DESC')
                ->setParameter('genre', $genre)
                ->setParameter('sousGenre', $sousGenre);

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if ( ($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }

        if ($tri == "new" || $tri == "def"){
            $QueryBuilder = $this->createQueryBuilder('BD')
                ->where('BD.Genre = :genre')
                ->andWhere('BD.SousGenre = :sousGenre')
                ->orderBy('BD.DateDeParution', 'DESC')
                ->setParameter('genre', $genre)
                ->setParameter('sousGenre', $sousGenre);

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if ( ($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }

        if ($tri == "old"){
            $QueryBuilder = $this->createQueryBuilder('BD')
                ->where('BD.Genre = :genre')
                ->andWhere('BD.SousGenre = :sousGenre')
                ->orderBy('BD.DateDeParution', 'ASC')
                ->setParameter('genre', $genre)
                ->setParameter('sousGenre', $sousGenre);

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if ( ($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }

        $QueryBuilder = $this->createQueryBuilder('BD')
            ->where('BD.Genre = :genre')
            ->andWhere('BD.SousGenre = :sousGenre')
            ->orderBy('BD.DateDeParution', 'DESC')
            ->setParameter('genre', $genre)
            ->setParameter('sousGenre', $sousGenre);

        $BandeDessinees = $QueryBuilder->getQuery();

        $premierResultat = ($page - 1) * $nbMaxParPage;
        $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
        $paginator = new Paginator($BandeDessinees);

        if ( ($paginator->count() <= $premierResultat) && $page != 1) {
            throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
        }

        return $paginator;
    }

    public function getBDDepuisRecherche($recherche, $page, $nbMaxParPage, $tri)
    {
        /* Récupère les BD selon une recherche*/
        if ($tri == "new" || $tri == "def") {
            $QueryBuilder = $this->createQueryBuilder("BD")
                ->where('LOWER(BD.Titre) LIKE LOWER(:recherche)')
                ->orWhere('LOWER(BD.Auteur) LIKE LOWER(:recherche)')
                ->orWhere('LOWER(BD.Description) LIKE LOWER(:recherche)')
                ->orderBy('BD.DateDeParution', 'DESC')
                ->setParameter('recherche', '%'.$recherche.'%');

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if (($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }

        if ($tri == "old") {
            $QueryBuilder = $this->createQueryBuilder("BD")
                ->where('LOWER(BD.Titre) LIKE LOWER(:recherche)')
                ->orWhere('LOWER(BD.Auteur) LIKE LOWER(:recherche)')
                ->orWhere('LOWER(BD.Description) LIKE LOWER(:recherche)')
                ->orderBy('BD.DateDeParution', 'ASC')
                ->setParameter('recherche', '%'.$recherche.'%');

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if (($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }

        if ($tri == "pos") {
            $QueryBuilder = $this->createQueryBuilder("BD")
                ->where('LOWER(BD.Titre) LIKE LOWER(:recherche)')
                ->orWhere('LOWER(BD.Auteur) LIKE LOWER(:recherche)')
                ->orWhere('LOWER(BD.Description) LIKE LOWER(:recherche)')
                ->orderBy('BD.NoteMoyenne', 'DESC')
                ->setParameter('recherche', '%'.$recherche.'%');

            $BandeDessinees = $QueryBuilder->getQuery();

            $premierResultat = ($page - 1) * $nbMaxParPage;
            $BandeDessinees->setFirstResult($premierResultat)->setMaxResults($nbMaxParPage);
            $paginator = new Paginator($BandeDessinees);

            if (($paginator->count() <= $premierResultat) && $page != 1) {
                throw new NotFoundHttpException('La page demandée n\'existe pas.'); // page 404, sauf pour la première page
            }

            return $paginator;
        }
    }

}
