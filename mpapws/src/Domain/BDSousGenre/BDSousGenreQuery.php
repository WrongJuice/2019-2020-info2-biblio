<?php

namespace App\Domain\BDSousGenre;

class BDSousGenreQuery
{
    public $page;
    public $nbPage;
    public $genre;
    public $sousGenre;
    public $tri;

    public function __construct($page, $nbPage, $genre, $sousGenre, $tri)
    {
        $this->page = $page;
        $this->nbPage = $nbPage;
        $this->genre = $genre;
        $this->sousGenre = $sousGenre;
        $this->tri = $tri;
    }
}