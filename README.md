
# Weather Monitor (Laravel)

Egy egyszerű Laravel webalkalmazás, amiben **városokat** tudsz kezelni (név + ország + koordináták), majd egy Artisan parancs segítségével **időjárás méréseket** (hőmérséklet) tudsz lekérni és eltárolni. A **Weather Dashboard** oldalon városonként látszik a legutóbbi hőmérséklet és egy **Chart.js** grafikon, ami az utolsó 5–10 (jelenleg: max 10) mérést ábrázolja.

Az alkalmazás külső API-kat használ az Open‑Meteo szolgáltatásból:

- Geokódolás (város → latitude/longitude): `geocoding-api.open-meteo.com`
- Aktuális időjárás (koordináták → current temperature): `api.open-meteo.com`

---

## Funkciók

### Városok (Cities)

- Város hozzáadása (City Name + Country)
- A város koordinátáit (latitude/longitude) automatikusan lekéri geokódolással
- Duplikáció védelem: ugyanaz a `name + country` kombináció nem vehető fel kétszer
- Város törlése
- Lista nézetben: városnév, ország, latitude, longitude

### Időjárás mérések (Weather measurements)

- Mérések tárolása városonként (hőmérséklet + timestamp)
- `weather:update` Artisan parancs: minden városhoz lekéri az aktuális hőmérsékletet és elmenti
	- Ha egy városhoz nincs elmentett koordináta, először geokódolással bepótolja

### Weather Dashboard

- Városonként megjeleníti a legutóbbi hőmérsékletet
- Bónusz: városonként egy Chart.js grafikon az utolsó max 10 mérésből
- A grafikonok CDN-ről töltik a Chart.js-t (nincs külön NPM dependency erre)

---

## Oldalak és navigáció

### Web route-ok

- `GET /` → Cities oldal (alapértelmezett kezdőlap)
- `GET /cities` → Cities oldal (városlista + felvétel + törlés)
- `POST /cities` → város felvétele
- `DELETE /cities/{city}` → város törlése
- `GET /dashboard` → Weather Dashboard (városlista + latest temp + chartok)

### API route-ok

- `GET /api/weather/{city_id}` → az adott város mérései JSON-ban (csökkenő `created_at` sorrendben)
	- Ha nincs mérés, 404-et ad vissza egy üzenettel.

---

## Adatmodell (röviden)

### `cities` tábla

- `name` (string)
- `country` (string)
- `latitude` (float/decimal)
- `longitude` (float/decimal)

### `weather_measurements` tábla

- `city_id` (FK → cities)
- `temperature` (decimal(5,2))
- `created_at`, `updated_at`

Kapcsolat:

- Egy `City`-hez több `WeatherMeasurement` tartozik (`hasMany`).

---

## Technológia stack

- PHP 8.2+
- Laravel 12
- SQLite (alapértelmezett `.env.example` szerint)
- Frontend: a jelenlegi oldalak Bootstrap 5 CDN-t használnak
- Chart: Chart.js CDN
- Vite + Tailwind be van készítve a projektben (a build pipeline része), de a fenti oldalak megjelenése jelenleg Bootstrap alapú.

---

## Telepítés és futtatás (Windows / általános)

### 1) Függőségek telepítése

```bash
composer install
```

### 2) `.env` létrehozása és app key generálás

Az env.example kapcsolódási paramétereit módosítottam, az env szerint, hogy ne sqlite legyen az alapértelmezett adatbázis. Ezt azért tettem, mivel tesztkörnyezetben nem tartalmaz szenzitív adatokat.

```bash
copy .env.example .env
php artisan key:generate
```

### 3) Adatbázis migrációk


```bash
php artisan migrate
```

Megjegyzés: a projektben a session/cache/queue driver is adatbázisra van állítva, ezért a migrációk szükségesek.

### 4) (Opcionális) seed

```bash
php artisan db:seed
```

Van egy `TestCitySeeder`, ami például Budapestet és Londont felveszi (koordináta nélkül) – a `weather:update` parancs ezt később bepótolja.


### 5) Szerver indítása

```bash
php artisan serve
```

### 6) Mérések parancs óránkénti futtatása, vagy azonnali futtatás

Mérés futtatása:

```bash
php artisan weather:update
```

Mérések automatikus(óránkénti)futtatása:

```bash
php artisan schedule:work
```


Ezután:

- Kezdőlap: `http://127.0.0.1:8000/` (Cities)
- Dashboard: `http://127.0.0.1:8000/dashboard`





---

## Használat (tipikus flow)

### Város felvétele

1. Menj a Cities oldalra (`/` vagy `/cities`).
2. Add meg a város nevét és az országot.
3. Mentéskor a rendszer geokódolással lekéri a koordinátákat.
4. Ha nem található a város, validációs hibát kapsz.

### Időjárás frissítése (mérések létrehozása)

Futtasd:

```bash
php artisan weather:update
```

Ez végigmegy az összes városon:

- ha hiányzik a latitude/longitude, megpróbálja bepótolni
- lekéri az aktuális hőmérsékletet
- létrehoz egy új sort a `weather_measurements` táblában

### Dashboard grafikonok

A Dashboard oldalon városonként:

- “Latest Temperature” → a legutóbbi mért érték
- Grafikon → az utolsó max 10 mérés (időbélyeg + hőmérséklet)

---

## Fontos fájlok / belső működés (gyors térkép)

- Város CRUD + geokódolás:
	- `app/Http/Controllers/web/CityController.php`
	- `app/Services/GeocodingService.php`
	- `resources/views/cities.blade.php`

- Dashboard + chart adatok:
	- `app/Http/Controllers/web/DashboardController.php`
	- `resources/views/dashboard.blade.php`

- Mérések API:
	- `app/Http/Controllers/api/WeatherMeasurementController.php`
	- `routes/api.php`

- Frissítő parancs:
	- `app/Console/Commands/WeatherUpdate.php`

- Route-ok:
	- `routes/web.php`

---

## Hibaelhárítás

### 500-as hiba / Blade parse error

Ha a dashboard view-ban komplex PHP kifejezéseket közvetlenül `@json(...)`-ba raksz, előfordulhat Blade parse hiba. Ilyenkor érdemes a JSON-hoz szükséges adatot a controllerben előkészíteni és csak egy egyszerű `@json($valami)`-t használni.

### Nincs adat a grafikonon

- Ellenőrizd, hogy van-e mérés: futtasd `php artisan weather:update`
- Nézd meg az adatbázist (SQLite esetén a `database/database.sqlite` fájlt)

### Geokódolás nem talál várost

- Próbálj meg pontosabb városnevet (angol/ismert névformát)
- Ellenőrizd az internetkapcsolatot (külső Open‑Meteo API hívás)

---




