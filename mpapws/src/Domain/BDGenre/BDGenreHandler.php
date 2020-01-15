<?php

namespace App\Domain\BDGenre;


use App\Repository\BandeDessineeRepository;

class BDGenreHandler
{

    private $repository;

    /**
     * BDGenreHandler constructor.
     */
    public function __construct(BandeDessineeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(BDGenreQuery $Query) : ?iterable
    {
        return $this->repository->getBDGenrePagination($Query->page, $Query->nbPage, $Query->genre, $Query->tri);
    }
}