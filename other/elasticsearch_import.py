from elasticsearch import Elasticsearch
from elasticsearch.helpers import bulk
from csv_import import get_data, get_ignored_lines
from dotenv import dotenv_values

env_config = dotenv_values(".env")
ELASTIC_URL = env_config["ELASTIC_URL"]
ELASTIC_USER = env_config["ELASTIC_USER"]
ELASTIC_PASSWORD = env_config["ELASTIC_PASSWORD"]
ELASTIC_INDEX = "instapictures_cities"

def generate_data():
    print("Génération des données...")
    for data in get_data('geonames-postal-code.csv', 'countries-codes.csv'):
        yield { "_index": ELASTIC_INDEX, "_source": data }
    print("Fin de génération des données.", get_ignored_lines(), "lignes n'ont pas pu être importées.")

def delete(instance: Elasticsearch):
    print("Suppression des données existantes...")
    instance.options(ignore_status=[400, 404]).indices.delete(index=ELASTIC_INDEX)

def create(instance: Elasticsearch):
    print("Création de l'index...")
    mapping = {
        "settings": {
            "analysis": {
                "analyzer": {
                    "autocomplete_analyzer": {
                        "tokenizer": "autocomplete_tokenizer",
                        "filter": ["lowercase"]
                    }
                },
                "tokenizer": {
                    "autocomplete_tokenizer": {
                        "type": "edge_ngram",
                        "min_gram": 2,
                        "max_gram": 20,
                        "token_chars": ["letter", "digit"]
                    }
                }
            }
        },
        "mappings": {
            "properties": {
                "name": {
                    "type": "text",
                    "analyzer": "autocomplete_analyzer",
                    "search_analyzer": "standard"
                },
                "postal_code": {
                    "type": "text",
                    "analyzer": "standard"
                },
                "department": {
                    "type": "text",
                    "analyzer": "standard"
                },
                "region": {
                    "type": "text",
                    "analyzer": "standard"
                },
                "country": {
                    "type": "text",
                    "analyzer": "standard"
                }
            }
        }
    }
    instance.indices.create(index=ELASTIC_INDEX, body=mapping)

def post(instance: Elasticsearch):
    print("Envoi des données.")
    try:
        response = bulk(instance, generate_data())
        print("Envoi réussi:", response)
    except Exception as e:
        print("Erreur lors de l'envoi des données:", e)
    print("Fin d'envoi des données.")

if __name__ == '__main__':
    print("Création de l'instance ElasticSearch")
    es = Elasticsearch(ELASTIC_URL, basic_auth=(ELASTIC_USER, ELASTIC_PASSWORD))
    delete(es)
    create(es)
    post(es)
    print("Fin de l'instance ElasticSearch")
