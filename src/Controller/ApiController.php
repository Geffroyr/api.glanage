<?php

namespace App\Controller;

use Goutte\Client;
use App\Entity\Lieu;
use App\Entity\Legume;
use App\Entity\Glaneur;
use App\Entity\Actualite;
use App\Entity\Evenement;
use App\Entity\Agriculteur;
use App\Entity\Utilisateur;
use App\Form\EvenementType;
use App\Form\GlaneurZoneType;
use App\Entity\EvenementLegume;
use App\Entity\EvenementGlaneur;
use App\Repository\LieuRepository;
use App\Entity\EvenementRecuperateur;
use App\Repository\EvenementRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EvenementGlaneurRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/apii")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/utilisateur/get", name="utilisateur_get")
     */
    public function utilisateur_get(SerializerInterface $serializer)
    {
        $user = $this->getUser();
        switch ($user->getType()) {
            case 'glaneur':
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
                break;
            case 'agriculteur':
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
                break;
            case 'recuperateur':
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
                        'evenementRecuperateurs' => ['legume' => ['id', 'name'], 'evenement' => ['id'], 'volume']
                    ]]
                );
                break;
            default:
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
     * @Route("/legume/list", name="legume_list")
     */
    public function legume_list(SerializerInterface $serializer)
    {
        $legumes = $this->getDoctrine()->getRepository(Legume::class)->findAll();
        $data = $serializer->serialize($legumes, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/evenement/list", name="evenement_list")
     */
    public function evenement_list(SerializerInterface $serializer, EvenementRepository $evenementRepository)
    {
        if ($this->getUser()->getLieu() != NULL) {
            $evenements = $evenementRepository->findByDistance($this->getUser());

            //$evenements = $evenementRepository->findAll();
            $data = $serializer->serialize(
                $evenements,
                'json',
                [AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'date',
                    'lieu' => ['id', 'commune', 'latitude', 'longitude', 'codePostal'],
                    'evenementLegumes' => ['volume', 'legume' => ['id', 'name']],
                    'agriculteur' => ['id', 'username'],
                    'deroulements' => ['heure', 'description'],
                    'rendezvouses' => ['heure', 'description']
                ]]
            );
            $response = new Response($data);
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $response = new Response('', 400);
        }
        return $response;
    }

    /**
     * @Route("/evenement/get/{id<\d+>}", name="evenement_get")
     */
    public function evenement_get(int $id, SerializerInterface $serializer)
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->find($id);
        $user = $this->getUser();
        switch ($user->getType()) {
            case 'ambassadeur':
            case 'admin':
                $data = $serializer->serialize(
                    $evenement,
                    JsonEncoder::FORMAT,
                    [AbstractNormalizer::ATTRIBUTES => [
                        'id',
                        'date',
                        'lieu' => ['id', 'commune', 'latitude', 'longitude', 'codePostal'],
                        'evenementLegumes' => ['volume', 'legume' => ['id', 'name']],
                        'agriculteur' => ['id', 'username'],
                        'deroulements' => ['heure', 'description'],
                        'rendezvouses' => ['heure', 'description'],
                        'evenementGlaneurs' => ['glaneur' => ['id', 'username'], 'effectif'],
                        'evenementRecuperateurs' => ['recuperateur' => ['id', 'username'], 'legume' => ['id', 'name'], 'volume']
                    ]]
                );
                break;
            default:
                $data = $serializer->serialize(
                    $evenement,
                    'json',
                    [AbstractNormalizer::ATTRIBUTES => [
                        'id',
                        'date',
                        'lieu' => ['id', 'commune', 'latitude', 'longitude', 'codePostal'],
                        'evenementLegumes' => ['volume', 'legume' => ['id', 'name']],
                        'agriculteur' => ['id', 'username'],
                        'deroulements' => ['heure', 'description'],
                        'rendezvouses' => ['heure', 'description']
                    ]]
                );
        }

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AMBASSADEUR')")
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
     * @Security("is_granted('ROLE_GLANEUR')")
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
     * @Security("is_granted('ROLE_GLANEUR')")
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
     * @Security("is_granted('ROLE_GLANEUR')")
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
     * @Security("is_granted('ROLE_RECUPERATEUR')")
     * @Route("/evenementRecuperateur/new", name="evenementRecuperateur_new")
     */
    public function evenementRecuperateur_new(Request $request, EvenementRepository $evenementRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        for ($i = 0; $i < count($data); $i++) {
            $evenementRecuperateur = new EvenementRecuperateur();
            $evenementRecuperateur->setRecuperateur($this->getUser())
                ->setEvenement($evenementRepository->find($data[$i]['evenement']['id']))
                ->setLegume($this->getDoctrine()->getRepository(Legume::class)->find($data[$i]['legume']['id']))
                ->setVolume($data[$i]['volume']);


            $entityManager->persist($evenementRecuperateur);
        }
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    /**
     * @Security("is_granted('ROLE_RECUPERATEUR')")
     * @Route("/evenementRecuperateur/edit", name="evenementRecuperateur_edit")
     */
    public function evenementRecuperateur_edit(Request $request, EvenementRepository $evenementRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        for ($i = 0; $i < count($data); $i++) {
            $evenementRecuperateur = $this->getDoctrine()->getRepository(EvenementRecuperateur::class)
                ->findOneBy([
                    'evenement' => $evenementRepository->find($data[$i]['evenement']['id']),
                    'recuperateur' => $this->getUser(),
                    'legume' => $this->getDoctrine()->getRepository(Legume::class)->find($data[$i]['legume']['id'])
                ]);
            $evenementRecuperateur->setVolume($data[$i]['volume']);
            $entityManager->persist($evenementRecuperateur);
        }
        $entityManager->flush();
        $response = new Response('', 204);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    /**
     * @Security("is_granted('ROLE_RECUPERATEUR')")
     * @Route("/evenementRecuperateur/delete", name="evenementRecuperateur_delete")
     */
    public function evenementRecuperateur_delete(Request $request, EvenementRepository $evenementRepository): Response
    {
        $data = json_decode($request->getContent(), true);
        $entityManager = $this->getDoctrine()->getManager();

        for ($i = 0; $i < count($data); $i++) {
            $evenementRecuperateur = $this->getDoctrine()->getRepository(EvenementRecuperateur::class)
                ->findOneBy([
                    'evenement' => $evenementRepository->find($data[$i]['evenement']['id']),
                    'recuperateur' => $this->getUser(),
                    'legume' => $this->getDoctrine()->getRepository(Legume::class)->find($data[$i]['legume']['id'])
                ]);
            $entityManager->remove($evenementRecuperateur);
        }
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
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGRICULTEUR') or is_granted('ROLE_AMBASSADEUR')")
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
                //'evenementLegumes' => ['volume', 'legume' => ['name']],
                'deroulements' => ['heure', 'description'],
                'rendezvouses' => ['heure', 'description']
            ]]
        );

        $evenementLegumes = [];
        for ($i = 0; $i < count($data['evenementLegumes']); $i++) {
            $evenement_legume = new EvenementLegume();
            $evenement_legume->setVolume($data['evenementLegumes'][$i]['volume']);
            $evenement_legume->setLegume($this->getDoctrine()->getRepository(Legume::class)->find($data['evenementLegumes'][$i]['legume']['id']));
            //$evenement->addEvenementLegume($evenement_legume);
            $evenementLegumes[] = $evenement_legume;
        }
        $evenement->setEvenementLegumes($evenementLegumes);

        $evenement->setLieu($this->getDoctrine()->getRepository(Lieu::class)->find($data['lieu']['id']))
            ->setEnabled(true);

        if ($this->isGranted('ROLE_AGRICULTEUR')) {
            $evenement->setAgriculteur($this->getUser());
        } else if ($this->isGranted('ROLE_ADMIN')||$this->isGranted('ROLE_AMBASSADEUR')) {
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
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_AGRICULTEUR') or is_granted('ROLE_AMBASSADEUR')")
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
                //'evenementLegumes' => ['volume', 'legume' => ['name']],
                'deroulements' => ['heure', 'description'],
                'rendezvouses' => ['heure', 'description']
            ]]
        );

        $evenementLegumes = [];
        for ($i = 0; $i < count($data['evenementLegumes']); $i++) {
            $evenement_legume = new EvenementLegume();
            $evenement_legume->setVolume($data['evenementLegumes'][$i]['volume']);
            $evenement_legume->setLegume($this->getDoctrine()->getRepository(Legume::class)->find($data['evenementLegumes'][$i]['legume']['id']));
            //$evenement->addEvenementLegume($evenement_legume);
            $evenementLegumes[] = $evenement_legume;
        }
        $evenement->setEvenementLegumes($evenementLegumes);

        $evenement->setDeroulements($temp_evenement->getDeroulements())
            ->setRendezvouses($temp_evenement->getRendezvouses());

        $evenement->setDate(new \DateTime($data['date']))
            ->setLieu($this->getDoctrine()->getRepository(Lieu::class)->find($data['lieu']['id']))
            ->setEnabled(true);

        if ($this->isGranted('ROLE_AGRICULTEUR')) {
            $evenement->setAgriculteur($this->getUser());
        } else if ($this->isGranted('ROLE_ADMIN')||$this->isGranted('ROLE_AMBASSADEUR')) {
            $evenement->setAgriculteur($this->getDoctrine()->getRepository(Agriculteur::class)->find($data['agriculteur']));
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenement);
        $entityManager->flush();
        $response = new Response('', 204);

        return $response;
    }
    /**
     * @Security("is_granted('ROLE_ADMIN')")
     * @Route("/scraper", name="scraper")
     */
    public function scraper(SerializerInterface $serializer)
    {
        // $actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        $entityManager = $this->getDoctrine()->getManager();
        // foreach ($actualites as $actualite) {
        //     $entityManager->remove($actualite);
        // }
        // $entityManager->flush();
        $client = new Client();
        $crawler = $client->request('GET', 'http://glanage-solidaire.fr');
        $crawler->filter('.actualites li')->each(function ($node) {
            $lien = $node->filter('a')->last()->attr('href');
            $client2 = new Client();
            $crawler2 = $client2->request('GET', $lien);
            if (!$this->getDoctrine()->getRepository(Actualite::class)->findOneBy(array('titre' => $crawler2->filter('.titre')->text()))) {
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
                    $img = file_get_contents($node2->attr('src'));
                    $path = "image" . "/" . basename($node2->attr('src'));
                    file_exists($path) ?: file_put_contents($path, $img);
                    $actualite->addImage($path);
                });
            }
        });
        //$actualites = $this->getDoctrine()->getRepository(Actualite::class)->findAll();
        //$data = $serializer->serialize($actualites, 'json');
        //$response = new Response($data);
        $response = new Response('', 204);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
