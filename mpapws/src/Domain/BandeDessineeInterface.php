<?php

namespace App\Domain;


interface BandeDessineeInterface
{

    public function getBDTendancesForHome($genre);
    public function getBDTendancesPagination($page, $nbMaxParPage, $genre, $tri);
    public function getBDGenrePagination($page, $nbMaxParPage, $genre, $tri);
    public function getBDSousGenrePagination($page, $nbMaxParPage, $genre, $sousGenre, $tri);
    public function getBDDepuisRecherche($recherche, $page, $nbMaxParPage, $tri);
    
}