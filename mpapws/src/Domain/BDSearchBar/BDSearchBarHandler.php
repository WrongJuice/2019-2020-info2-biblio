<?php

namespace App\Domain\BDSearchBar;


use App\Repository\BandeDessineeRepository;

class BDSearchBarHandler
{

    private $repository;

    /**
     * BDSearchBarHandler constructor.
     */
    public function __construct(BandeDessineeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(BDSearchBarQuery $Query) : ?iterable
    {
        return $this->repository->getBDDepuisRecherche($Query->recherche, $Query->page, $Query->nbPage, $Query->tri);
    }
}