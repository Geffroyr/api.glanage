<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Glaneur;
use App\Entity\Actualite;
use App\Entity\Agriculteur;
use App\Entity\Evenement;
use App\Entity\Utilisateur;
use App\Form\EvenementType;
use App\Form\GlaneurZoneType;
use App\Entity\EvenementGlaneur;
use App\Repository\LieuRepository;
use App\Repository\EvenementRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EvenementGlaneurRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Goutte\Client;
/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/utilisateur/get", name="utilisateur_get")
     */
    public function utilisateur_get(SerializerInterface $serializer)
    {
        $user = $this->getUser();
        if ($user->getType() == 'glaneur') {
            $data = $serializer->serialize(
                $user,
                'json',
                [AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'username',
                    'type',
                    'email',
                    'lastname',
                    'firstname',
                    'phone',
                    'perimetre',
                    'lieu',
                    'evenementGlaneurs' => ['effectif', 'evenement' => ['id']]
                ]]
            );
        } else if ($user->getType() == 'agriculteur') {
            $data = $serializer->serialize(
                $user,
                'json',
                [AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'username',
                    'type',
                    'email',
                    'lastname',
                    'firstname',
                    'phone',
                    'perimetre',
                    'lieu',
                    'evenements' => ['id']
                ]]
            );
        } else {
            $data = $serializer->serialize(
                $user,
                'json',
                [AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'username',
                    'type',
                    'email',
                    'lastname',
                    'firstname',
                    'phone',
                    'perimetre',
                    'lieu'
                ]]
            );
        }
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/lieu/list", name="lieu_list")
     */
    public function lieu_list(SerializerInterface $serializer, LieuRepository $lieuRepository)
    {
        $lieux = $lieuRepository->findBrittany();
        $data = $serializer->serialize($lieux, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/evenement/list", name="evenement_list")
     */
    public function evenement_list(SerializerInterface $serializer, EvenementRepository $evenementRepository)
    {
        if($this->getUser()->getLieu()!=NULL){
            $evenements = $evenementRepository->findByDistance($this->getUser());
        
        //$evenements = $evenementRepository->findAll();
        $data = $serializer->serialize(
            $evenements,
            'json',
            [AbstractNormalizer::ATTRIBUTES => [
                'id',
                'date',
                'lieu' => ['id', 'commune', 'latitude', 'longitude', 'codePostal'],
                'evenementLegumes' => ['volume', 'legume' => ['name']],
                'agriculteur' => ['id', 'username'],
                'deroulements' => ['heure', 'description'],
                'rendezvouses' => ['heure', 'description']
            ]]
        );
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
    } else {
        $response = new Response('',400);
    }
        return $response;
    }

    /**
     * @Route("/evenement/get/{id<\d+>}", name="evenement_get")
     */
    public function evenement_get(int $id, SerializerInterface $serializer)
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
        $data = $serializer->serialize(
            $evenement,
            'json',
            [AbstractNormalizer::ATTRIBUTES => [
                'id',
                'date',
                'lieu' => ['id', 'commune', 'latitude', 'longitude', 'codePostal'],
                'evenementLegumes' => ['volume', 'legume' => ['name']],
                'agriculteur' => ['id', 'username'],
                'deroulements' => ['heure', 'description'],
                'rendezvouses' => ['heure', 'description']
            ]]
        );
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/agriculteur/list", name="agriculteur_list")
     */
    public function agriculteur_list(SerializerInterface $serializer)
    {
        $agriculteurs = $this->getDoctrine()->getRepository(Agriculteur::class)->findAll();
        $data = $serializer->serialize(
            $agriculteurs,
            'json',
            [AbstractNormalizer::ATTRIBUTES => [
                'id',
                'username'
            ]]
        );
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/actualite/list", name="actualite_list")
     */
    public function actualite_list(SerializerInterface $serializer)
    {
        $actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        $data = $serializer->serialize($actualites, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Route("/actualite/get/{id<\d+>}", name="actualite_get")
     */
    public function actualite_get(int $id, SerializerInterface $serializer)
    {
        $actualite = $this->getDoctrine()->getRepository(Actualite::class)->find($id);
        $data = $serializer->serialize($actualite, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/utilisateur/update/zone", name="utilisateur_update_zone")
     */
    public function utilisateurUpdateZone(Request $request): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $lieu = $this->getDoctrine()->getRepository(Lieu::class)->find($data['lieu']['id']);
        $user->setLieu($lieu);
        $user->setPerimetre($data['perimetre']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/evenementGlaneur/new", name="evenementGlaneur_new")
     */
    public function evenementGlaneur_new(Request $request, EvenementRepository $evenementRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $evenementGlaneur = new EvenementGlaneur();
        $evenementGlaneur->setGlaneur($this->getUser())
            ->setEvenement($evenementRepository->find($data['evenement']['id']))
            ->setEffectif($data['effectif']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenementGlaneur);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/evenementGlaneur/edit", name="evenementGlaneur_edit")
     */
    public function evenementGlaneur_edit(Request $request, EvenementRepository $evenementRepository, EvenementGlaneurRepository $evenementGlaneurRepository): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $evenement = $evenementRepository->find($data['evenement']['id']);
        $evenementGlaneur = $evenementGlaneurRepository->findByEvenementGlaneur($evenement, $user);
        $evenementGlaneur->setEffectif($data['effectif']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenementGlaneur);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/evenementGlaneur/delete", name="evenementGlaneur_delete")
     */
    public function evenementGlaneur_delete(Request $request, EvenementRepository $evenementRepository, EvenementGlaneurRepository $evenementGlaneurRepository): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $evenement = $evenementRepository->find($data['evenement']['id']);
        $evenementGlaneur = $evenementGlaneurRepository->findByEvenementGlaneur($evenement, $user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($evenementGlaneur);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/utilisateur/edit/infos", name="utilisateur_edit_infos")
     */
    public function utilisateur_edit_infos(Request $request): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $user->setLastname($data['lastname'])
            ->setFirstname($data['firstname'])
            ->setPhone($data['phone']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/utilisateur/edit/pass", name="utilisateur_edit_pass")
     */
    public function utilisateur_edit_pass(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        $password = $passwordEncoder->encodePassword($user, $data['password']);
        $user->setPassword($password);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/evenement/new", name="evenement_new")
     */
    public function evenement_new(Request $request,  SerializerInterface $serializer)
    {
        $data = json_decode($request->getContent(), true);
        $evenement = new Evenement();

        $evenement = $serializer->deserialize(
            json_encode($data),
            Evenement::class,
            'json',
            [AbstractNormalizer::ATTRIBUTES => [
                'date',
                'evenementLegumes' => ['volume', 'legume' => ['name']],
                'deroulements' => ['heure', 'description'],
                'rendezvouses' => ['heure', 'description']
            ]]
        );

        $evenement->setLieu($this->getDoctrine()->getRepository(Lieu::class)->find($data['lieu']['id']))
            ->setEnabled(true);

        if ($this->isGranted('ROLE_AGRICULTEUR')) {
            $evenement->setAgriculteur($this->getUser());
        } else if ('ROLE_ADMIN') {
            $evenement->setAgriculteur($this->getDoctrine()->getRepository(Agriculteur::class)->find($data['agriculteur']));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenement);
        $entityManager->flush();


        $response = new Response('', 204);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/evenement/edit/{id<\d+>}", name="evenement_edit")
     */
    public function evenement_edit(Request $request,  SerializerInterface $serializer, int $id)
    {
        $data = json_decode($request->getContent(), true);
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($id);

        $temp_evenement = $serializer->deserialize(
            json_encode($data),
            Evenement::class,
            'json',
            [AbstractNormalizer::ATTRIBUTES => [
                'evenementLegumes' => ['volume', 'legume' => ['name']],
                'deroulements' => ['heure', 'description'],
                'rendezvouses' => ['heure', 'description']
            ]]
        );

        $evenement->setEvenementLegumes($temp_evenement->getEvenementLegumes())
            ->setDeroulements($temp_evenement->getDeroulements())
            ->setRendezvouses($temp_evenement->getRendezvouses());

        $evenement->setDate(new \DateTime($data['date']))
            ->setLieu($this->getDoctrine()->getRepository(Lieu::class)->find($data['lieu']['id']))
            ->setEnabled(true);

        if ($this->isGranted('ROLE_AGRICULTEUR')) {
            $evenement->setAgriculteur($this->getUser());
        } else if ('ROLE_ADMIN') {
            $evenement->setAgriculteur($this->getDoctrine()->getRepository(Agriculteur::class)->find($data['agriculteur']));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenement);
        $entityManager->flush();
        $response = new Response('', 204);

        return $response;
    }
    /**
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
