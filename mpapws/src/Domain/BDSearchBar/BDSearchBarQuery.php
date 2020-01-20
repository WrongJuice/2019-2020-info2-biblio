<?php

namespace App\Domain\BDSearchBar;

class BDSearchBarQuery
{
    public $page;
    public $nbPage;
    public $recherche;
    public $tri;

    public function __construct($page, $nbPage, $recherche, $tri)
    {
        $this->page = $page;
        $this->nbPage = $nbPage;
        $this->recherche = $recherche;
        $this->tri = $tri;
    }
}