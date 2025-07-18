<?php

namespace App\Tests\Unit;

use DateTime;
use App\Entity\User;
use App\Entity\Plateform;
use App\Entity\Tournament;
use PHPUnit\Framework\TestCase;

class TournamentTest extends TestCase
{
    public function testCreateNewObject(): void
    {
        $strName = "Tournois de test";
        $strDescription="Contenu de ma description";
        $strImgCard= "1.webp";
        $intLimitPlayer= 25;
        $boolRegistrationOpen= true;
        $strDateStart= new DateTime("2025-03-31 16:59:00");
        $strDateEnd= new DateTime("2025-05-31 16:59:00");
        $objPlateforms= New Plateform();
        $objCreatedBy= New User();


        $objTournaments = new Tournament();
        $objTournaments ->setName($strName)
                        ->setDescription($strDescription)
                        ->setImgCard($strImgCard)
                        ->setLimitPlayer($intLimitPlayer)
                        ->setRegistrationOpen($boolRegistrationOpen)
                        ->setDateStart($strDateStart)
                        ->setDateEnd($strDateEnd)
                        ->setPlateforms($objPlateforms)
                        ->setCreatedBy($objCreatedBy);
        self::assertSame($strName, $objTournaments->getName());
        self::assertSame($strDescription, $objTournaments->getDescription());
        self::assertSame($strImgCard, $objTournaments->getImgCard());
        self::assertSame($intLimitPlayer, $objTournaments->getLimitPlayer());
        self::assertSame($boolRegistrationOpen, $objTournaments->isRegistrationOpen());
        self::assertSame($strDateStart, $objTournaments->getDateStart());
        self::assertSame($strDateEnd, $objTournaments->getDateEnd());
        self::assertSame($objPlateforms, $objTournaments->getPlateforms());
        self::assertSame($objCreatedBy, $objTournaments->getCreatedBy());
    }
}