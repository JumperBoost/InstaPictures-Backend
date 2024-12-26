<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController {

    #[Route('/search/city')]
    public function search_city_index(): Response {
        return new Response("Merci d'indiquer un champ pour rechercher une ville.", 400);
    }

    #[Route('/search/city/{champ}')]
    public function search_city(mixed $champ): Response {
        if(strlen($champ) < 3)
            return new Response("Le champ de recherche d'une ville doit être minimum de 3 caractères.", 400);
        if(str_contains($champ, '*'))
            return new Response("Le champ contient des caractères spéciaux interdit.", 400);
        try {
            $res = CityRepository::getInstance()->search($champ);
            $cities = [];
            foreach($res['hits']['hits'] as $hit)
                $cities[] = $hit['_source'];
            return new Response(json_encode($cities));
        } catch (\Exception $e) {
            return new Response("Une erreur est survenue lors de la recherche de la ville: " . $e, 500);
        }
    }
    #[Route('/search')]
    #[Route('/search/{request}')]
    public function index(mixed $request): Response {
        if(is_null($request))
            return new Response("Merci d'indiquer un type de recherche valide.", 400);
        else return new Response($request . " est un type de recherche invalide. Merci d'indiquer un type de recherche valide.", 400);
    }
}
