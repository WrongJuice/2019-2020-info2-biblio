<?php

namespace App\Domain\BDGenre;

class BDGenreQuery
{
    public $page;
    public $nbPage;
    public $genre;
    public $tri;

    public function __construct($page, $nbPage, $genre, $tri)
    {
        $this->page = $page;
        $this->nbPage = $nbPage;
        $this->genre = $genre;
        $this->tri = $tri;
    }
}