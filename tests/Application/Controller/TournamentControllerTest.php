<?php

namespace App\Tests\Application\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TournamentControllerTest extends WebTestCase
{
    public function testTournamentPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tournament');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Consultez la liste de tout nos tournois en cours');
    }
    public function testCreateEvent(): void
    {  
        $client = static::createClient();
        $container = static::getContainer();
        $crawler = $client->request('GET', '/tournament');

        // Voir comment faire cela plus simplement solution provisoire.. nécessite de tester le FileUploader
        $uploadedFile = new UploadedFile(
            'assets/images/covers-square/1.webp',
            '1.webp',
            'image/webp',
            null,
            true 
        );

        $formData = [
       'name' => 'Tournois de test',
        'description' => 'Contenu de ma description',
        'limit_player' => 25,
        'registration_open' => true,
        'date_start' => '2025-03-31T16:59',
        'date_end' => '2025-05-31T16:59',
        'plateforms' => 1,
        'img_card' => $uploadedFile,
        ];
      

           // Récupère un user depuis la base (ou crée un User en base si besoin)
        $user = $container->get('doctrine')->getRepository(User::class)->findOneBy(['email' => 'me@example.com']);
        // Connecte l'utilisateur
        $client->loginUser($user);
        $client->request('GET', '/tournament');
        $client->request('GET', '/administration-tournament');
        $client->request('GET', '/administration-tournament/list');
        $client->request('GET', '/administration-tournament/create');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('app_tournament_create');
        $this->assertSelectorTextContains('h2', 'Création d\'un tournois');
         // Récupération et remplissage du formulaire
        $crawler = $client->request('GET', '/administration-tournament/create');
        $submitButton = $crawler->selectButton('Enregistrer');
        $form = $submitButton->form();
         // Remplissage des champs (attention aux noms exacts du formulaire HTML Symfony)
        $form['tournament[name]'] = $formData['name'];
        $form['tournament[description]'] = $formData['description'];
        $form['tournament[limit_player]'] = $formData['limit_player'];
        $form['tournament[registration_open]'] = $formData['registration_open'];
        $form['tournament[date_start]'] = $formData['date_start'];
        $form['tournament[date_end]'] = $formData['date_end'];
        $form['tournament[plateforms]'] = $formData['plateforms'];
        $form['tournament[img_card]'] = $uploadedFile; 
        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();
        
    } 
}

