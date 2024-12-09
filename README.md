# PHP Examen 2 - 27/2/2024

## Opdracht

In www/index.php vind je een html formulier terug om een nieuwe user account aan te maken. Zorg ervoor dat dit formulier werkt volgens de regels van de kunst:

- Laat de gebruiker toe om een nieuwe user aan te maken. Deze moet in de _users_ tabel terechtkomen (zie /dump/dbexamen.sql)
- Voorzie de nodige validatie:
  - Voornaam is verplicht
  - Naam is verplicht
  - Username is
    - verplicht
    - is minstens 3 characters lang
    - dient uniek te zijn
    - wordt ALTIJD opgeslagen in lowercase
    - mag enkel bestaan uit letters, cijfers en underscores
    - mag niet beginnen of einddigen met een underscore
  - Email is verplicht
    - Voorzie een extra validatie check via https://eva.pingutil.com/
  - Country is verplicht
  - Term & conditions is verplicht
- Deze validatie dient volledig in PHP te werken, verwijder browser-validatie. JS validatie geeft geen punten.
- Toon foutmeldingen bij voorkeur _the bootstrap way_.
- Spendeer extra aandacht aan UX!
- Indien een gebruiker de optionele foto upload:
  - laat enkel jpg/jpeg en png toe
  - laat enkel afbeeldingen toe die mininaal 100px breed en hoog zijn
  - laat enkel afbeeldingen toe kleiner dan 2MB
  - upload de afbeeldingen in folder www/avatars en sla enkel de relatieve link op in de DB
- Toon, in geval dat alles ok verliep, een simpele bedankt-boodschap met _Welkom @username_!
- Schrijf duidelijke, gestructureerde code. Voeg commentaar toe waar nodig.

## Extra

- Voorzie de mogelijkheid om het formulier niet enkel in EN, maar ok in NL te gebruiken. Gebruik hiervoor een GET-parameter

## Local development

- Indien gewenst kan gebruik gemaakt worden van de bijgevoegde docker-compose file. Dit is geen vereiste en levert geen extra punten op.
