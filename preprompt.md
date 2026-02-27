# Pre-Prompt für Les Verts Theme ("les-verts")

## Ziel
Dieser Pre-Prompt dient als umfassender Kontext für die Weiterentwicklung des WordPress-Themes **les-verts** (Die Grünen Schweiz). Er beschreibt die Architektur, Technologie-Stack, Hauptfunktionalitäten und Best Practices.

---

## Projekt-Übersicht

**Les Verts** ist ein modernes WordPress-Theme für die Grünen Schweiz mit integriertem Living Styleguide.

### Technologie-Stack

#### Frontend/Styleguide
- **Fractal.build** - Living Styleguide Framework
- **Handlebars** - Template Engine für Styleguide-Komponenten
- **Sass** - CSS-Präprozessor
- **Gulp** - Build-System und Task-Runner
- **Browserify + Babel** - JavaScript-Bundling und ES6+ Transpilierung
- **Swiper** - Slider-Komponente
- **Modernizr** - Feature-Detection

#### Backend/WordPress
- **PHP 7.4+** - Mindestversion
- **Timber/Twig** - Template Engine für WordPress
- **Advanced Custom Fields Pro** - Custom Fields Management
- **Polylang Pro** - Mehrsprachigkeit (DE, FR, IT)
- **SearchWP** - Erweiterte Suchfunktionalität
- **Yoast SEO** - SEO-Optimierung
- **The Events Calendar (Tribe Events)** - Event-Management

#### Entwicklungsumgebung
- **Docker + Docker Compose** - Lokale Entwicklung
- **Node.js >= 16** - Build-Tools
- **Yarn >= 1.22.0** - Package Manager
- **WP-CLI** - WordPress Command Line Interface
- **XDEBUG** - PHP Debugging (standardmäßig aktiviert)

---

## Architektur

### Theme-Struktur

```
wordpress/wp-content/themes/les-verts/
├── functions.php              # Theme-Einstiegspunkt
├── lib/                       # Haupt-Logik
│   ├── _loader.php           # Zentrale Loader-Datei
│   ├── acf/                  # ACF-Anpassungen
│   ├── admin/                # Admin-Interface-Anpassungen
│   ├── controllers/          # View-Controller für komplexe Komponenten
│   ├── customizer/           # Theme-Customizer
│   ├── form/                 # Formular-Bibliothek (siehe preprompt_interfaces.md)
│   ├── post-types/           # Custom Post Types
│   ├── timber/               # Erweiterte Timber-Klassen
│   ├── tweaks/               # Rendering-Tweaks und Hacks
│   ├── twig/                 # Twig-Extensions, Filter und Funktionen
│   └── widgets/              # Custom Widgets
├── templates/                # Twig-Templates
├── acf-json/                 # ACF-Feld-Definitionen (versioniert)
├── languages/                # Übersetzungsdateien (.pot, .po, .mo)
├── styleguide/               # Styleguide-Quellcode (siehe unten)
└── static/                   # Kompilierte Assets (symlink zu styleguide/dist/static)
```

### Styleguide-Struktur

```
styleguide/
├── src/
│   ├── components/           # Fractal-Komponenten (Handlebars)
│   ├── docs/                 # Styleguide-Dokumentation
│   ├── scss/                 # Sass-Dateien
│   ├── js/                   # JavaScript-Module
│   ├── icons/                # SVG-Icons (werden zu Sprite kompiliert)
│   ├── fonts/                # Webfonts
│   ├── img/                  # Bilder
│   ├── index.js              # JavaScript-Einstiegspunkt
│   └── style.scss            # Sass-Einstiegspunkt
└── dist/
    ├── static/               # Kompilierte Assets (CSS, JS, Fonts, Icons)
    └── build/                # Statische Styleguide-Build
```

---

## Styleguide-Integration

### Konzept
Der Styleguide und WordPress teilen sich **dieselben kompilierten Assets**. Dies gewährleistet absolute Konsistenz zwischen Styleguide und Live-Website.

### Workflow

1. **Entwicklung starten:**
   ```bash
   yarn start          # Startet Styleguide + Live-Kompilierung
   yarn wp:start       # Startet WordPress (Docker)
   ```

2. **Assets werden kompiliert nach:**
   - `styleguide/dist/static/` (Hauptordner)
   - `wordpress/wp-content/themes/les-verts/static/` (Symlink)

3. **Styleguide-Zugriff:**
   - Styleguide: http://localhost:4000
   - WordPress: http://localhost
   - WordPress Admin: http://localhost/wp-admin (admin/admin)

### Build-Prozess (Gulp)

**Entwicklung:**
```bash
yarn start  # → gulp fractal:start
```
- Kompiliert Sass zu CSS
- Bundelt JavaScript mit Browserify
- Generiert SVG-Sprite
- Kopiert Fonts und Bilder
- Startet Fractal Dev-Server mit BrowserSync
- Watched Änderungen und kompiliert live

**Produktion:**
```bash
yarn build  # → gulp build:production
```
- Minifiziert CSS und JavaScript
- Optimiert Assets
- Generiert Modernizr-Build

**Statischer Styleguide-Build:**
```bash
yarn fractal:build
```
- Erstellt statische HTML-Version des Styleguides in `styleguide/dist/build/`

### Komponenten-Entwicklung

**Neue Komponente erstellen:**
```bash
yarn component:add [type] [component_name]
```

**Komponenten-Typen:**
- `atoms` - Kleinste UI-Elemente
- `molecules` - Kombinationen von Atoms
- `organisms` - Komplexe UI-Komponenten
- `templates` - Seitenlayouts

**Handlebars → Twig:**
- Styleguide nutzt Handlebars
- WordPress nutzt Twig/Timber
- Komponenten müssen in beiden Systemen implementiert werden
- Assets (CSS/JS) sind identisch

---

## Hauptfunktionalitäten

### 1. Formular-System

**Siehe `preprompt_interfaces.md` für Details zu:**
- Formular-Verarbeitung
- CRM-Integration (Webling)
- Mailchimp-Integration
- Queue-System für asynchrone Verarbeitung

**Wichtige Dateien:**
- `lib/form/_loader.php` - Formular-Bibliothek Einstiegspunkt
- `lib/form/submission.php` - Formular-Submission-Handler
- `lib/form/include/SyncEnqueuer.php` - Queue-Verwaltung
- `lib/form/include/SyncProcessor.php` - Queue-Verarbeitung
- `lib/form/include/CrmDao.php` - CRM-API-Kommunikation
- `lib/form/include/MailchimpSaver.php` - Mailchimp-Integration

**Debug-Konstanten (wp-config.php):**
```php
define('SUPT_FORM_ASYNC', false);      // Synchrone Verarbeitung für Debugging
define('LES_VERTS_FORM_DEBUG_LOG', true); // Logging in ./form.log
```

**WP-CLI Befehle:**
```bash
wp form list              # Zeigt Queue-Status
wp form process           # Verarbeitet Queue manuell
```

### 2. Responsive Image Handling

**Konzept:**
- Ersetzt Timmy-Plugin durch WordPress Core Image Handling
- Definiert standardisierte Bildgrößen
- Generiert responsive srcset automatisch
- PNG-Uploads haben spezielle Beschränkungen

**Bildgrößen:**
```php
'thumbnail'    => 150px   # WordPress Core
'small'        => 400px   # Custom
'medium'       => 790px   # WordPress Core (Haupt-Responsive-Größe)
'large'        => 1024px  # WordPress Core
'extra-large'  => 1200px  # Custom
'huge'         => 1580px  # Custom
'full'         => 2560px  # WordPress Core (big_image_size_threshold)
```

**PNG-Beschränkungen:**
- Max. Dateigröße: 3MB
- Max. Breite: 2560px
- Keine automatischen Zwischengrößen (Performance)
- Pixel-perfekte Qualität erhalten

**Wichtige Dateien:**
- `lib/twig/functions/image-handling.php` - Bildverarbeitungs-Setup
- `lib/twig/functions/image-filters.php` - Twig-Filter für responsive Bilder
- `lib/tweaks/limit-image-upload-dims.php` - Upload-Beschränkungen

**Twig-Nutzung:**
```twig
{{ image_filters.getTimberImageResponsive(image, 'medium') }}
```

### 3. Single Sign-On (SSO)

**Integration:**
- OpenID Connect Generic Plugin
- Keycloak als Identity Provider
- Auto-Login (SSO-Modus)
- Deaktiviert WordPress Standard-Login

**Wichtige Dateien:**
- `lib/tweaks/openid-connect-generic.php` - SSO-Anpassungen
- `docs/sso.md` - Setup-Dokumentation

**Anpassungen:**
1. Deaktiviert automatische Account-Erstellung
2. Deaktiviert WordPress Standard-Authentifizierung
3. Angepasster Login-Button-Text

### 4. Custom Post Types

**PeopleType** - Personen/Team-Mitglieder
- Custom Taxonomie für Kategorisierung
- ACF-Felder für Kontaktdaten, Bilder, etc.

**BlockType** - Wiederverwendbare Content-Blöcke
- Flexible Content-Blöcke
- Verwendbar in verschiedenen Kontexten

**Registrierung:**
- Alle Post Types erweitern `\SUPT\Model`
- Registrierung in `lib/_loader.php`

### 5. Mehrsprachigkeit (L10n)

**Workflow:**
1. Code mit WordPress i18n-Funktionen schreiben
2. ACF-Felder als PHP exportieren → `acf-translate.php`
3. Poedit Pro: `languages/theme.pot` aktualisieren
4. Push zu GitHub → Automatischer Upload zu Crowdin
5. Übersetzung auf Crowdin
6. GitHub Workflow erstellt PR mit übersetzten Dateien

**Tools:**
- Polylang Pro - Content-Übersetzung
- Crowdin - Übersetzungsmanagement
- Poedit Pro - .pot/.po-Dateien (benötigt Pro wegen Twig)

**Dateien:**
- `languages/theme.pot` - Master-Übersetzungsdatei
- `languages/lesverts-de_CH.po/mo` - Deutsche Übersetzungen
- `languages/lesverts-fr_FR.po/mo` - Französische Übersetzungen
- `.github/workflows/l10n.yml` - Automatisierungs-Workflow

### 6. ACF (Advanced Custom Fields)

**Synchronized JSON:**
- Felder werden automatisch in `acf-json/` gespeichert
- Versionskontrolle der Feld-Definitionen
- Automatische Synchronisation zwischen Umgebungen

**Workflow:**
1. Felder im Backend bearbeiten
2. Automatische Speicherung in `acf-json/`
3. Dateien committen
4. Auf anderen Umgebungen: "Sync available" Button klicken

**ACF-Anpassungen:**
- `lib/acf/acf-init.php` - Options-Pages
- `lib/acf/acf-people-title.php` - Custom Titel-Generierung
- `lib/acf/cached-oembeds.php` - Performance-Optimierung

---

## Controllers (View-Models)

Controller bereiten Daten für komplexe/globale Komponenten vor und injizieren sie in den Timber-Kontext.

**Verfügbare Controller:**
- `Branding_controller` - Logo, Farben, Branding-Elemente
- `Navigation_controller` - Menüs und Navigation
- `Progress_controller` - Fortschrittsbalken-Daten
- `Frontpage_controller` - Startseiten-spezifische Daten
- `Landingpage_controller` - Landingpage-spezifische Daten

**Registrierung:**
```php
// In lib/_loader.php
Branding_controller::register();
```

---

## Twig-Extensions

### Wichtige Filter

**Bildverarbeitung:**
- `getTimberImageResponsive(image, size)` - Responsive Bilder

**Lokalisierung:**
- `pll` - Polylang-Übersetzungen
- `l10n_date` - Lokalisierte Datumsformatierung

**Formatierung:**
- `email` - E-Mail-Verschleierung
- `phone` - Telefonnummer-Formatierung
- `social_link` - Social-Media-Links
- `nice_link` - Schöne URLs
- `wptexturize` - WordPress Typografie
- `disableable_autop` - Kontrollierbares wpautop

**Sicherheit:**
- `esc_form_value` - Formular-Wert-Escaping
- `hexencode` - Hex-Kodierung

**Utilities:**
- `uniqueid` - Eindeutige IDs generieren
- `shortcode` - WordPress-Shortcodes ausführen
- `license` - Lizenz-Informationen

### Wichtige Funktionen

- `get_lang()` - Aktuelle Sprache
- `get_people()` - Personen abrufen
- `link_props()` - Link-Eigenschaften extrahieren

---

## Docker-Entwicklungsumgebung

### Services

**wordpress** (Port 80)
- Apache + PHP 7.4
- XDEBUG aktiviert
- Volumes für Theme-Entwicklung
- Zugriff auf host.docker.internal für externe Services

**db** (MariaDB 10.6)
- Persistente Datenbank
- Credentials: wordpress/wordpress

**phpmyadmin** (Port 8181)
- Datenbank-Management
- http://localhost:8181

**mailhog** (Port 8025)
- E-Mail-Testing
- Fängt alle ausgehenden E-Mails ab
- http://localhost:8025

### Umgebungsvariablen

```bash
MAILCHIMP_SERVICE_ENDPOINT  # Mailchimp-Service URL
INVERT_CTA_COLORS          # Feature-Flag für CTA-Farben
WORDPRESS_DEBUG            # Debug-Modus
```

### Nützliche Befehle

```bash
# WordPress initialisieren
yarn wp:init

# WordPress starten
yarn wp:start

# WP-CLI nutzen
docker exec wp_docker_les_verts wp [command]

# Plugins aktualisieren
docker exec wp_docker_les_verts wp plugin update --all

# Theme-Logs ansehen
docker logs wp_docker_les_verts -f
```

---

## Migrations

**System:**
- Automatische Datenbank-Migrationen bei Theme-Updates
- Versionskontrolle über `Migrations\Migrator`
- Migrations in `lib/migrations/`

**Beispiele:**
- `get-active-button.php` - Migration für Action-Buttons (v0.26.2)
- `event-content.php` - Event-Content-Migration (v0.32.0)

---

## Performance-Optimierungen

### Timmy-Filter entfernt
Alle Timmy-Upload/Metadata-Filter wurden entfernt, um Performance-Probleme zu vermeiden:
- Keine automatische Bildgenerierung beim Upload
- On-Demand-Generierung stattdessen
- Deutlich schnellere Uploads und Löschungen

### Caching
- ACF oEmbed-Caching aktiviert
- SearchWP Email-Summaries deaktiviert

### Asset-Loading
- Minifizierte Assets in Produktion
- Block-Styles nur für eingeloggte User
- Conditional Loading von Plugin-Assets

---

## Widgets

**Custom Widgets:**
- `ContactWidget` - Kontaktinformationen
- `ButtonWidget` - Call-to-Action Buttons
- `LinkListWidget` - Link-Listen

**Widget-Zone:**
- `footer-widget-area` - Footer-Bereich

---

## Wichtige Konstanten

**Theme:**
```php
THEME_VERSION      # Theme-Version
THEME_DOMAIN       # 'lesverts' - Text-Domain
THEME_URI          # Theme-URL
```

**Feature-Flags:**
```php
INVERT_CTA_COLORS  # CTA-Farben invertieren
```

**Debug:**
```php
WP_DEBUG                    # WordPress Debug-Modus
SUPT_FORM_ASYNC            # Asynchrone Formular-Verarbeitung
LES_VERTS_FORM_DEBUG_LOG   # Formular-Logging
```

**Upload-Limits:**
```php
SUPT_UPLOAD_MAX_PX_PNG     # PNG max. Pixel-Fläche (default: 2560*2560)
SUPT_UPLOAD_MAX_PX_JPEG    # JPEG max. Pixel-Fläche (default: 4096*4096)
```

---

## Best Practices

### Code-Stil
1. **Namespace:** Alle Theme-Klassen in `SUPT\` Namespace
2. **PSR-4 Autoloading:** Via Composer
3. **WordPress Coding Standards:** Befolgen
4. **Keine Kommentare hinzufügen/löschen** ohne explizite Anfrage

### Entwicklung
1. **Timber/Twig nutzen** für Templates
2. **ACF für Custom Fields** statt Meta-Boxes
3. **Controller für komplexe Logik** statt in Templates
4. **Twig-Filter für Formatierung** statt PHP in Templates
5. **Migrations für DB-Änderungen** bei Theme-Updates

### Assets
1. **Styleguide-Komponenten zuerst** entwickeln
2. **Twig-Templates parallel** implementieren
3. **Shared Assets** zwischen Styleguide und WordPress
4. **Minifizierung in Produktion** aktivieren

### Testing
1. **Styleguide** für visuelle Komponenten-Tests
2. **XDEBUG** für PHP-Debugging
3. **Mailhog** für E-Mail-Tests
4. **Form.log** für Formular-Debugging

### Deployment
1. **Release-Script nutzen:** `yarn make:release`
2. **ACF-JSON committen**
3. **Übersetzungen via Crowdin** aktualisieren
4. **Migrations testen** vor Deployment

---

## Nützliche Links

- **Timber Dokumentation:** https://timber.github.io/docs/
- **Fractal Dokumentation:** https://fractal.build/
- **ACF Dokumentation:** https://www.advancedcustomfields.com/resources/
- **Polylang Dokumentation:** https://polylang.pro/doc/
- **GitHub Repository:** https://github.com/grueneschweiz/2018.gruene.ch

---

## Troubleshooting

### Styleguide startet nicht
```bash
yarn install  # Dependencies neu installieren
rm -rf node_modules && yarn install  # Kompletter Neuinstall
```

### WordPress-Probleme
```bash
# Container neu starten
docker compose down
docker compose up -d

# Datenbank zurücksetzen
docker compose down -v
yarn wp:init
```

### Asset-Kompilierung schlägt fehl
```bash
# Gulp-Cache leeren
rm -rf styleguide/dist
yarn build
```

### Formular-Queue hängt
```bash
# Queue manuell verarbeiten
docker exec wp_docker_les_verts wp form process

# Queue-Status prüfen
docker exec wp_docker_les_verts wp form list
```

---

## Lizenz

GPL-3.0-or-later

**Nutzung:**
- Grüne Parteimitglieder: Frei für persönliche Websites
- Parteien: Kontakt erforderlich (gruene@gruene.ch)
