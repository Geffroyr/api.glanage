<?php

namespace App\Controller;

use Goutte\Client;
use App\Entity\Actualite;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Constraints\Length;

//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;

class ScraperController extends AbstractController
{

    /*
     * @Route("/scraper", name="scraper")
     */
    public function index(SerializerInterface $serializer)
    {
        $actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($actualites as $actualite) {
            $entityManager->remove($actualite);
        }
        $entityManager->flush();
        //$client = PantherClient::createFirefoxClient();
        $client = new Client();
        //$client = HttpClient::create();
        $crawler = $client->request('GET', 'http://glanage-solidaire.fr');
        //dump($response->getContent());
        $crawler->filter('.actualites li')->each(function ($node) {
            $actualite = new Actualite();
            $actualite->setTitre($node->filter('.title')->text())
                ->setContenu($node->filter('.contenu')->text())
                ->addImage(preg_match('/(http).+(jpg|png)/', $node->filter('.image-actus')->last()->attr('style'), $out) ? $out[0] : 'no match')
                ->setDate(date_create_from_format('d/m/y', $node->filter('.date')->text()))
                ->setLien($node->filter('a')->last()->attr('href'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actualite);
            $entityManager->flush();
        });
        $actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        $data = $serializer->serialize($actualites, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /*
     * @Route("/scraper", name="scraper")
     */
    public function index2(SerializerInterface $serializer)
    {
        $actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        $entityManager = $this->getDoctrine()->getManager();
        foreach ($actualites as $actualite) {
            $entityManager->remove($actualite);
        }
        $entityManager->flush();
        $client = new Client();
        $crawler = $client->request('GET', 'http://glanage-solidaire.fr');
        $crawler->filter('.actualites li')->each(function ($node) {
            $lien = $node->filter('a')->last()->attr('href');
            $client2 = new Client();
            $crawler2 = $client2->request('GET', $lien);
            $actualite = new Actualite();
            $actualite->setTitre($crawler2->filter('.titre')->text())
                ->setContenu($crawler2->filter('.contenu')->text())
                ->setDate(date_create_from_format('d/m/y', $crawler2->filter('.date')->text()))
                ->setLien($lien);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actualite);
            $entityManager->flush();
            $crawler2->filter('.galerie img')->each(function ($node2) {
                $actualite = $this->getDoctrine()->getRepository(Actualite::class)->findOneBy(array(), array('id' => 'DESC'), 1, 0);
                $actualite->addImage($node2->attr('src'));
            });
        });
        //$actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        //$data = $serializer->serialize($actualites, 'json');
        //$response = new Response($data);
        $response = new Response('',204);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
