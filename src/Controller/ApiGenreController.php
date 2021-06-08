<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiGenreController extends AbstractController
{
    /**
     * @Route("/api/genres", name="api_genres", methods={"GET"})
     */
    public function list(GenreRepository $genreRepository, SerializerInterface $serializerInterface): Response
    {
        $genres = $genreRepository->findAll();

        $resultats = $serializerInterface->serialize($genres,'json', ['groups' =>['listGenreFull'] ]);

        return new JsonResponse($resultats,200,[],true);

        // $genresNormalises = $normalizerInterface->normalize($genres, null, ['groups' => 'listGenreSimple']);
        // $resultatsJson = json_encode($genresNormalises);

        // deuxième option pour serialize
        // $resultatsJson = $serializerInterface->serialize($genres,'json', ['groups' => 'listGenreFull' ]);
        // $response = new Response($resultatsJson,200, ["Content-Type" => "applicatino/json"]); 

        // return $response;
    }

    /**
     * @Route("/api/genres/{id}", name="api_genres_show", methods={"GET"})
     */
    public function show(Genre $genre, SerializerInterface $serializerInterface): Response
    {

        $resultat = $serializerInterface->serialize($genre,'json', ['groups' =>['listGenreSimple'] ]);

        return new JsonResponse($resultat,Response::HTTP_OK,[],true);
    }

    /**
     * @Route("/api/genres", name="api_genres_create", methods={"POST"})
     */
    public function create(ValidatorInterface $validatorInterface, SerializerInterface $serializerInterface,EntityManagerInterface $entityManagerInterface, Request $request): Response
    {
        $data = $request->getContent();
        // $genre = new Genre();
        // $serializerInterface->deserialize($data, Genre::class, 'json', ['object_to_populate'=>$genre]);
        $genre = $serializerInterface->deserialize($data, Genre::class, 'json'); 
        $errors = $validatorInterface->validate($genre); 
        if(count($errors)){
            $errorsJson = $serializerInterface->serialize($errors,'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        } 
        $entityManagerInterface->persist($genre);
        $entityManagerInterface->flush();   

        return new JsonResponse(
            "le genre a bien été créer",
            Response::HTTP_CREATED,[

                // "location"=>"api/genres/".$genre->getId(),
                "location"=>$this->generateUrl(
                    'api_genres_show', 
                    ["id"=>$genre->getId()] ,UrlGeneratorInterface::ABSOLUTE_URL
                )
            ],true
        ); 
    }

    /**
     * @Route("/api/genres/{id}", name="api_genres_update", methods={"PUT"})
     */
    public function edit(ValidatorInterface $validatorInterface, Genre $genre, Request $request, SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface): Response
    {
        $data= $request->getContent();
        $serializerInterface->deserialize($data, Genre::class, 'json', ['object_to_populate'=>$genre]);
        $errors = $validatorInterface->validate($genre); 
        if(count($errors)){
            $errorsJson = $serializerInterface->serialize($errors,'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        } 
        $entityManagerInterface->persist($genre);
        $entityManagerInterface->flush();  
        
        return new JsonResponse("le genre a bien été modifié",Response::HTTP_OK,[],true);
    }

    /**
     * @Route("/api/genres/{id}", name="api_genres_delete", methods={"DELETE"})
     */
    public function delete(Genre $genre, EntityManagerInterface $entityManagerInterface): Response
    {
        $entityManagerInterface->remove($genre);
        $entityManagerInterface->flush();  
        
        return new JsonResponse("le genre a bien été supprimé",Response::HTTP_OK,[]);
    }
}
