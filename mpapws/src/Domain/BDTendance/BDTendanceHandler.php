<?php

namespace App\Domain\BDTendance;


use App\Repository\BandeDessineeRepository;

class BDTendanceHandler
{

    private $repository;

    /**
     * BDTendanceHandler constructor.
     */
    public function __construct(BandeDessineeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(BDTendanceQuery $Query) : ?iterable
    {
        return $this->repository->getBDTendancesPagination($Query->page, $Query->nbPage, $Query->genre, $Query->tri);
    }
}