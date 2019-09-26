<?php
namespace App\Utils;

// fonction : transformer le titre ou tag en slug

class Slugger {

    private $toLower;

    public function __construct($toLower) 
    {
       //va me permettre de conditionner la creation de mon slugger avec sa casse d'origine ou en miniscule
       $this->toLower = $toLower;
    }


    public function slugify(string $strToConvert){
        //si true chaine convertie en minuscule
        if($this->toLower === true){
            $strToConvert = strtolower($strToConvert);
        }

       //Nettoie la chaine avant de la transformer en slugger, en respectant la casse indiquée
       $convertedString = preg_replace( '/[^a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*/', '-', trim(strip_tags($strToConvert)) ); 
       return $convertedString;
    }
}