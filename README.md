# Tarot API

Jeg har bygget en simpel PHP-baseret REST API til tarotkort og readings. Projektet kører bedst på MAMP (PHP 8 + MySQL) og svarer med JSON, så jeg kan bruge data i `index.html` eller andre klienter.

## Kom godt i gang
- Jeg starter MAMP og sikrer mig, at MySQL bruger port `8889` og socket `.../mysql.sock`, så det matcher `config.php`.
- Jeg opretter databasen `tarot` i phpMyAdmin (eller via terminal): `CREATE DATABASE tarot CHARACTER SET utf8mb4;`.
- Jeg opretter tabellen `cards`, så den passer til det API’et forventer:

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

- Jeg lægger mine kortdata ind i tabellen (CSV-import i phpMyAdmin fungerer fint).
- Jeg åbner `http://localhost:8888/tarot-api/index.php` for selve API’et eller `index.html` for demo-UI’et (enten via MAMP eller VS Codes Live Server).

> Tabellen `readings` bliver oprettet automatisk første gang jeg kalder `/readings`, så den laver jeg ikke manuelt.

## API-overblik
- Når jeg vil skifte API-nøgle, ændrer jeg værdien i `index.php:713` (funktionen `requireApiKey`) og bruger den nye nøgle i alle requests.
- `GET /cards` – alle kort. Filtre: `?arcana=Major|Minor`, `?element=Air|Fire|Water|Earth`, `?q=tekst`.
- `GET /cards?random=1` – tilfældigt kort.
- `GET /cards/{id}` – enkelt kort.
- `POST /cards`, `PUT /cards/{id}`, `DELETE /cards/{id}` – kræver header `X-API-Key: HEMMELIGT_TOKEN`.
- `GET /readings` og `GET /readings/{id}` – liste eller enkelt reading.
- `POST /readings`, `PUT /readings/{id}`, `DELETE /readings/{id}` – også beskyttet af `X-API-Key`.

Alle succes-svar er JSON. Fejl-returneringer har `"error"` og `"message"` på dansk, så jeg nemt ser hvad der mangler.

## Lokal test
- Jeg bruger `curl` eller et REST-værktøj (Insomnia/Postman). Eksempel:

  ```bash
  curl http://localhost:8888/tarot-api/index.php/cards?arcana=Major
  ```

- `localhost:8888` er MAMPs standard for Apache; ændrer jeg porten i MAMP, justerer jeg URL’en tilsvarende (fx `localhost:8890`).
- Til skrivende requests husker jeg JSON-body (`-H "Content-Type: application/json" -d '{...}'`) og API-nøglen (`-H "X-API-Key: HEMMELIGT_TOKEN"`).

## Frontend-demo
`index.html` kalder API’et og viser kortene i et grid med filtrering. Hvis jeg flytter API’et til en anden host eller port, opdaterer jeg `apiEndpoint` i bunden af HTML-filen.

## HATEOAS – hvorfor jeg bruger det
API’et sender ikke kun data tilbage, men også links med forslag til næste skridt. Når jeg fx henter et kort, får jeg links til:
- `GET` for at hente kortet igen
- `PUT` for at opdatere det
- `DELETE` for at slette det

Det gør API’et selvforklarende og løfter det fra en ren CRUD-implementering (Richardson Level 2) til Level 3 med HATEOAS.

## GitHub-workflow
- Mit repo ligger på `https://github.com/Katrinerosa/tarot-api`.
- Jeg pusher nye ændringer med `git push origin main`, og jeg kan altid justere README eller kode direkte her i projektet.

Jeg har lært at bygge et REST API fra bunden i PHP, bruge HTTP-metoder/statuskoder bevidst og tænke HATEOAS ind, så API’et guider klienten automatisk.
