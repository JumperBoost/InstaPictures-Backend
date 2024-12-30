<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AutoCompleteController extends AbstractController {

    #[Route('/autocomplete/city')]
    public function autocomplete_city_index(): Response {
        return new Response("Merci d'indiquer un champ pour autocompléter une ville.", 400);
    }

    #[Route('/autocomplete/city/{champ}')]
    public function autocomplete_city(mixed $champ): Response {
        if(strlen($champ) < 3)
            return new Response("Le champ d'autocomplétion d'une ville doit être minimum de 3 caractères.", 400);
        if(str_contains($champ, '*'))
            return new Response("Le champ contient des caractères spéciaux interdit.", 400);
        try {
            $res = CityRepository::getInstance()->autocomplete($champ);
            $cities = [];
            foreach($res['hits']['hits'] as $hit)
                $cities[] = $hit['_source'];
            return new Response(json_encode($cities));
        } catch (\Exception $e) {
            return new Response("Une erreur est survenue lors de l'autocomplétion de la ville: " . $e, 500);
        }
    }
    #[Route('/autocomplete')]
    #[Route('/autocomplete/{request}')]
    public function index(mixed $request): Response {
        if(is_null($request))
            return new Response("Merci d'indiquer un type d'autocomplétion valide.", 400);
        else return new Response($request . " est un type d'autocomplétion invalide. Merci d'indiquer un type d'autocomplétion valide.", 400);
    }
}
