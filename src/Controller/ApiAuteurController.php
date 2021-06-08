<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Nationalite;
use App\Repository\AuteurRepository;
use App\Repository\NationaliteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiAuteurController extends AbstractController
{
    /**
     * @Route("/api/auteurs", name="api_auteurs", methods={"GET"})
     */
    public function list(AuteurRepository $auteurRepository, SerializerInterface $serializerInterface): Response
    {
        $auteurs = $auteurRepository->findAll();

        $resultats = $serializerInterface->serialize($auteurs,'json', ['groups' =>['listAuteurFull'] ]);

        return new JsonResponse($resultats,200,[],true);

        // $auteursNormalises = $normalizerInterface->normalize($auteurs, null, ['groups' => 'listAuteurSimple']);
        // $resultatsJson = json_encode($auteursNormalises);

        // deuxième option pour serialize
        // $resultatsJson = $serializerInterface->serialize($auteurs,'json', ['groups' => 'listAuteurFull' ]);
        // $response = new Response($resultatsJson,200, ["Content-Type" => "applicatino/json"]); 

        // return $response;
    }

    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_show", methods={"GET"})
     */
    public function show(Auteur $auteur, SerializerInterface $serializerInterface): Response
    {

        $resultat = $serializerInterface->serialize($auteur,'json', ['groups' =>['listAuteurSimple'] ]);

        return new JsonResponse($resultat,Response::HTTP_OK,[],true);
    }

    /**
     * @Route("/api/auteurs", name="api_auteurs_create", methods={"POST"})
     */
    public function create(NationaliteRepository $nationaliteRepository, ValidatorInterface $validatorInterface, SerializerInterface $serializerInterface,EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $data = $request->getContent(); 
        $dataTab = $serializerInterface->decode($data, 'json');
        $auteur = new Auteur();
        $nationalite = $nationaliteRepository->find($dataTab['nationalite']['id']);
        $serializerInterface->deserialize($data, Auteur::class, 'json', ['object_to_populate'=>$auteur]);
        $auteur->setNationalite($nationalite);
       

        $errors = $validatorInterface->validate($auteur); 
        if(count($errors)){
            $errorsJson = $serializerInterface->serialize($errors,'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        } 
        $entityManagerInterface->persist($auteur);
        $entityManagerInterface->flush();   

        return new JsonResponse(
            "l'auteur a bien été créé",
            Response::HTTP_CREATED,[

                // "location"=>"api/auteurs/".$auteur->getId(),
                "location"=>$this->generateUrl(
                    'api_auteurs_show', 
                    ["id"=>$auteur->getId()] ,UrlGeneratorInterface::ABSOLUTE_URL
                )
            ],true
        ); 
    }

    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_update", methods={"PUT"})
     */
    public function edit(NationaliteRepository $nationaliteRepository, ValidatorInterface $validatorInterface, Auteur $auteur, Request $request, SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface): Response
    {
        $data= $request->getContent();
        $dataTab = $serializerInterface->decode($data, 'json');
        $nationalite = $nationaliteRepository->find($dataTab['nationalite']['id']);
        // solution 1
        $serializerInterface->deserialize($data, Auteur::class, 'json', ['object_to_populate'=>$auteur]);
        $auteur->setNationalite($nationalite);

        // gestion des erreurs de validation
        $errors = $validatorInterface->validate($auteur); 
        if(count($errors)){
            $errorsJson = $serializerInterface->serialize($errors,'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        } 
        $entityManagerInterface->persist($auteur);
        $entityManagerInterface->flush();  
        
        return new JsonResponse("l'auteur a bien été modifié",Response::HTTP_OK,[],true);
    }

    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_delete", methods={"DELETE"})
     */
    public function delete(Auteur $auteur, EntityManagerInterface $entityManagerInterface): Response
    {
        $entityManagerInterface->remove($auteur);
        $entityManagerInterface->flush();  
        
        return new JsonResponse("le auteur a bien été supprimé",Response::HTTP_OK,[]);
    }
}
