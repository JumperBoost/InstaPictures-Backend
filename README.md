# Insta'Pictures — Côté Backend
Projet de visualisation de médias (photos/vidéos) de n'importe quel ville, via les données d'Instagram.

## Descriptif
Ce mini-projet est un site internet dans lequel il est possible de consulter les photos et vidéos d'une ville, prises par les habitants et/ou touristes.
Les médias sont basées sur les données d'Instagram, récoltés via une [API Apify](https://apify.com/apify/instagram-scraper).

Le projet est découpé en deux parties : le **frontend** et le **backend**.
Dans cette partie, il s'agit du backend, c'est-à-dire la logique côté serveur. Le frontend est disponible dans un autre [dépôt](https://github.com/JumperBoost/InstaPictures-Frontend/).

## Technologies utilisées
- **PHP** 8.3 et le framework **Symfony** 7.2 pour constituer une API interne
- **ElasticSearch** 8.17 pour le système de recherche des villes, un moteur de recherche puissant et ultra-rapide
- **Python** 3.11, [script](other/elasticsearch_import.py) pour la collecte des données [CSV](other/csv) et export vers ElasticSearch
- [**Apify**](https://apify.com/) pour l'utilisation simplifiée d'API externe, notamment pour le scrapping des données Instagram
- Serveur web [**nginx**](https://nginx.org/) avec module php et CDN [**Cloudflare**](https://www.cloudflare.com/) pour l'hébergement et la sécurité

## Prérequis d'utilisation
- **PHP** 8.3.x
- **Symfony CLI** 5.10.x
- **Composer** _(avec projet compilé)_
- Cluster **ElasticSearch** 8.17 configuré, avec données importées
- Fichier environnement [**.env**](.env) configuré, à partir du fichier [.env.example](.env.example)
- **Timeout** du serveur web configuré au minimum à **100** secondes. Exemple de configuration avec _nginx_ :
```nginx
server {
    ...
    # Timeout
    fastcgi_read_timeout 100;
    proxy_read_timeout 100;
    ...
}
```

## API interne
### Routes
- `/autocomplete/city/{champ}` : Autocomplétion de la recherche des villes, par le biais de _ElasticSearch_
- `/search/city/{champ}` : Recherche / Récupération des posts, par le biais de l'API de _Instagram_, via l'intermédiaire _Apify_
- `/download/{url}` : Téléchargement des médias Instagram, faisant office de "pont" entre Instagram et le frontend _(nécessaire dû à la restriction CORS des CDN d'Instagram)_

_**Remarque**: Seuls les liens officiels d'Instagram peuvent être téléchargés par l'intermédiaire de l'API, pour des questions de sécurité. Il est possible à l'avenir de devoir ajouter de nouveaux CDN parmi les hôtes autorisés, afin de pouvoir autoriser le téléchargement._

### Lien hébergé
https://instapictures-api.jumperboost.fr/ _(CORS accessible uniquement par le [frontend](https://instapictures.jumperboost.fr/))_
