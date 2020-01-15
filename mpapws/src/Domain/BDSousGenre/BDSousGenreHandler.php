<?php

namespace App\Domain\BDSousGenre;


use App\Repository\BandeDessineeRepository;

class BDSousGenreHandler
{

    private $repository;

    /**
     * BDSousGenreHandler constructor.
     */
    public function __construct(BandeDessineeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(BDSousGenreQuery $Query) : ?iterable
    {
        return $this->repository->getBDSousGenrePagination($Query->page, $Query->nbPage, $Query->genre, $Query->sousGenre, $Query->tri);
    }
}