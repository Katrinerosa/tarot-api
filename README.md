# Tarot API

En simpel PHP-baseret REST API til tarotkort og readings. Projektet kører på MAMP (PHP 8 + MySQL) og leverer JSON, som både kan bruges af mit eget frontend (fx `index.html`) eller andre apps.

## Kom godt i gang
- Start MAMP og tjek at MySQL bruger port `8889` og socket `.../mysql.sock` – det matcher `config.php`.
- Opret databasen `tarot` i phpMyAdmin (eller via terminal): `CREATE DATABASE tarot CHARACTER SET utf8mb4;`.
- Opret tabellen `cards` med de felter API’et forventer. Eksempel:

  ```sql
  CREATE TABLE cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    arcana VARCHAR(10) NOT NULL,
    suit VARCHAR(50) NULL,
    number INT NULL,
    description TEXT NOT NULL,
    upright_meaning TEXT NOT NULL,
    reversed_meaning TEXT NOT NULL,
    love_meaning TEXT NOT NULL,
    career_meaning TEXT NOT NULL,
    spiritual_meaning TEXT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    keywords VARCHAR(255) NOT NULL,
    element VARCHAR(10) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ```

- Indsæt kortdata i tabellen (brug evt. CSV → SQL import i phpMyAdmin).
- Åbn `http://localhost:8888/tarot-api/index.php` for selve API’et eller `index.html` for demo UI’et (VSCodes Live Server eller MAMP kan levere HTML’en).

> Tabellen `readings` bliver automatisk oprettet første gang man kalder `/readings`, så den skal ikke laves manuelt.

## API-overblik
- `GET /cards` – alle kort. Filtre: `?arcana=Major|Minor`, `?element=Air|Fire|Water|Earth`, `?q=tekst`.
- `GET /cards?random=1` – tilfældigt kort.
- `GET /cards/{id}` – enkelt kort.
- `POST /cards`, `PUT /cards/{id}`, `DELETE /cards/{id}` – kræver header `X-API-Key: HEMMELIGT_TOKEN`.
- `GET /readings` og `GET /readings/{id}` – liste eller enkelt reading.
- `POST /readings`, `PUT /readings/{id}`, `DELETE /readings/{id}` – også beskyttet af `X-API-Key`.

Alle succes-svar er JSON. Fejl-returneringer indeholder `"error"` og `"message"` på dansk, så man kan se hvad der mangler.

## Lokal test
- Brug `curl` eller et REST-værktøj (Insomnia/Postman). Eksempel:

  ```bash
  curl http://localhost:8888/tarot-api/index.php/cards?arcana=Major
  ```

- Til requests HUSK! JSON-body (`-H "Content-Type: application/json" -d '{...}'`) og API-nøglen (`-H "X-API-Key: HEMMELIGT_TOKEN"`).

## Frontend-demo
`index.html` kalder API’et og viser kortene i et grid med filtrering. Filen forventer, at API’et ligger på samme host/port; hvis man flytter API’et, så opdater `apiEndpoint` i bunden af HTML-filen.

## GitHub-workflow
- Commits dukker nu op i `https://github.com/Katrinerosa/tarot-api`.
- Push ændringer med `git push origin main`, og opret evt. README-opdateringer direkte her, hvis du justerer API’et fremover.

##HATEOAS – Hypermedia as the Engine of Application State

HATEOAS betyder, at API’et ikke kun returnerer data, men også vejviser-links til, hvad klienten kan gøre som det næste.
Det gør API’et selvforklarende og mere robust, fordi man ikke behøver kende alle URL’er på forhånd — de følger med i svaret.
	
  •	GET for at hente kortet igen
	•	PUT for at opdatere det
	•	DELETE for at slette det

På den måde følger klienten API’et trin for trin — lidt som at navigere i et websted, hvor hvert svar indeholder links til næste side.
Det er det, der løfter API’et fra Richardson Level 2 (ren CRUD) til Level 3 (selvbeskrivende API med HATEOAS).

Jeg har i projektet lært, hvordan man opbygger et REST API fra bunden i PHP, og hvordan HTTP-metoder og statuskoder bruges rigtigt. Jeg har også fået en bedre forståelse for HATEOAS og hvordan et API kan være selvforklarende ved at inkludere links i sine svar.

