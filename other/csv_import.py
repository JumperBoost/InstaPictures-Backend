from pandas import read_csv, isna

ignored_lines = 0

# Constantes, correspondant aux colonnes des fichiers CSV
COL_CODE_PAYS = "country code"
COL_CODE_POSTAL = "postal code"
COL_NOM_COMMUNE = "place name"
COL_DEPARTEMENT = "admin name2"
COL_REGION = "admin name1"

COL2_CODE_PAYS = "ISO2 CODE"
COL2_LABEL_PAYS = "LABEL FR"

def get_countries_name(country_codes_csv_path, separator=";") -> dict:
    csv = read_csv(country_codes_csv_path, sep=separator, low_memory=False)
    countries_list = {}
    for _, row in csv.iterrows():
        countries_list[row[COL2_CODE_PAYS]] = row[COL2_LABEL_PAYS]
    return countries_list

def get_data(communes_csv_path, country_codes_csv_path, separator=";"):
    global ignored_lines
    ignored_lines = 0
    countries_list = get_countries_name(country_codes_csv_path, separator)
    csv = read_csv(communes_csv_path, sep=separator, low_memory=False).sort_values(by=[COL_CODE_PAYS, COL_CODE_POSTAL, COL_NOM_COMMUNE])
    for _, row in csv.iterrows():
        try:
            nom_pays = countries_list[row[COL_CODE_PAYS]]
            nom_commune = row[COL_NOM_COMMUNE]
            code_postal = row[COL_CODE_POSTAL]
            departement = row[COL_DEPARTEMENT]
            region = row[COL_REGION]
            if not isna(nom_pays) and not isna(nom_commune) and not isna(code_postal) and not isna(departement) and not isna(region):
                yield { 'country': nom_pays, 'name': nom_commune, 'postal_code': code_postal, 'department': departement, 'region': region }
        except KeyError:
            # On ignore la ligne si le code postal est inexistant (dans le cas où la commune est une nouvelle fusion)
            ignored_lines += 1

def get_ignored_lines():
    return ignored_lines
