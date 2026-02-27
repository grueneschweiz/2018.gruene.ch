# Pre-Prompt für Chatbots

## Ziel
Dieser Pre-Prompt dient als Kontext für die Weiterentwicklung der drei Integrations-Tools: **Website**, **Webling-Service** und **Mailchimp-Service**. Er soll Windsurf bei jeder Änderung an einem dieser Tools die Architektur, die API-Endpunkte und die Historie bereitstellen.

---

## Systemübersicht
- **Webling-Service**: Erweiterte Schnittstelle zum CRM **Webling**. Stellt abstrahierte API-Funktionen bereit, führt Datenintegritätsprüfungen durch und ermöglicht Upsert-Operationen.
- **Mailchimp-Service**: Synchronisiert Daten zwischen Webling und Mailchimp, inklusive Feld-Mapping und Konfigurationslogik.
- **Website**: Handhabt Formulare, speichert neue Kontakte und interagiert mit beiden Services. Enthält eine Synchronisierungs-Queue.

### Datenwege
1. **Webling → Webling-Service → Mailchimp-Service**: Nächtlicher Cronjob synchronisiert bestehende Kontakte von Webling zu Mailchimp.
2. **Website → Webling-Service → Webling**: Via Cronjob und Queue-Verarbeitung werden bestehende Kontakte aus Formularen ins Webling übernommen.
3. **Neu: Website → Mailchimp-Service → Mailchimp**: Neue Kontakte werden direkt nach Mailchimp synchronisiert; bestehende weiterhin via Webling.
4. **Mailchimp-Webhook → Mailchimp-Service → Webling-Service → Webling**: Bei Unsubscribe oder Datenänderung in Mailchimp werden die Änderungen zurück nach Webling synchronisiert.

---

## API-Endpunkte

### Webling-Service
1. `GET /api/v1/member/{member_id}` – Liefert vollständigen Datensatz oder Mailchimp-relevante Teile.
2. `PUT /api/v1/member` – Aktualisiert Member-Datensatz (mit Append/Replace-Logik).
3. `POST /api/v1/member/match/{group_ids}` – Sucht nach möglichen Dubletten und liefert Matchstatus.

### Mailchimp-Service
1. `GET /api/v1/member/{member_id}` – Liefert Mailchimp-Informationen eines Members.
2. `PUT /api/v1/member/{member_id}` – Erstellt oder synchronisiert Mailchimp-Daten für einen Member.

### Website
- **Formular-Handling**: Sendet Daten an System (Upsert in Webling oder Mailchimp).
- **Query Data**: Fragt Mitgliedsstatus über Webling-Service an, um personalisierte Inhalte anzuzeigen.

---

## Historie & aktuelle Logik
- **Webling** ist das zentrale CRM und bleibt Master für Personendaten.
- Performance-Probleme führten zur Anpassung:
    - Neue Kontakte → direkt Mailchimp → später Webling (bei gutem Rating).
    - Bestehende Kontakte → Website → Webling → nächtlich Mailchimp.
- Ziel: Entlastung von Webling, flexible Synchronisation, konsistente Datenhaltung.

---

## Regeln für Weiterentwicklung
- Webling bleibt Master für bestätigte Personendaten.
- Mailchimp dient als schneller Einstiegspunkt für neue Kontakte.
- Synchronisation muss Dubletten vermeiden und Datenintegrität sicherstellen.
- API-Kommunikation erfolgt über HTTPS und JSON.

---

## Technische Implementierung

### Datei-Struktur (WordPress Theme)

```
wordpress/wp-content/themes/les-verts/lib/form/
├── _loader.php                    # Formular-Bibliothek Einstiegspunkt
├── submission.php                 # FormSubmission-Klasse (Haupteinstieg)
├── public-api.php                 # Öffentliche API-Funktionen
├── rest.php                       # REST-API-Endpunkte (Nonce)
├── settings-page.php              # Admin-Einstellungsseite
└── include/
    ├── FormType.php               # Custom Post Type für Formulare
    ├── FormModel.php              # Formular-Datenmodell
    ├── FormField.php              # Formularfeld-Definitionen
    ├── SubmissionModel.php        # Submission-Datenmodell
    ├── SyncEnqueuer.php           # Queue-Verwaltung (Daten vorbereiten)
    ├── SyncProcessor.php          # Queue-Verarbeitung (Cron-Job)
    ├── CrmQueueItem.php           # Queue-Item-Datenstruktur
    ├── QueueDao.php               # Queue-Datenbank-Zugriff
    ├── CrmFieldData.php           # CRM-Feld-Mapping-Objekt
    ├── CrmDao.php                 # Webling-API-Kommunikation
    ├── CrmSaver.php               # CRM-Speicher-Logik
    ├── MailchimpSaver.php         # Mailchimp-Speicher-Logik
    ├── Mailer.php                 # E-Mail-Versand
    ├── Mail.php                   # E-Mail-Template-Rendering
    ├── Nonce.php                  # Nonce-Verwaltung
    ├── GDPRHelper.php             # GDPR-Compliance-Helfer
    ├── Limiter.php                # Rate-Limiting
    ├── ProgressHelper.php         # Fortschrittsbalken-Daten
    ├── SubmissionExport.php       # CSV-Export
    ├── SubmissionsTable.php       # Admin-Tabellen-Ansicht
    └── Util.php                   # Hilfsfunktionen
```

### Datenfluss im Detail

#### 1. Formular-Submission
```
Benutzer → Frontend-Formular
         → AJAX POST /wp-json/supt/v1/form/submit
         → FormSubmission::handle_submission()
         → Validierung (Nonce, Rate-Limiting, GDPR)
         → SubmissionModel::create() (speichert in DB)
         → E-Mail-Versand (Mailer)
         → SyncEnqueuer::add_to_queue()
         → Response an Frontend
```

#### 2. Queue-Verarbeitung (Cron)
```
WP-Cron (alle 10 Minuten)
         → SyncProcessor::process_queue()
         → Lock-Mechanismus (verhindert parallele Ausführung)
         → Batch-Verarbeitung (max. 30 Items)
         → Für jedes Item:
            ├─→ CrmDao::match() (Dubletten-Check)
            ├─→ Wenn neu:
            │   └─→ MailchimpSaver::save() (direkt zu Mailchimp)
            └─→ Wenn existiert:
                └─→ CrmSaver::save() → CrmDao::save() (zu Webling)
         → Item aus Queue entfernen oder Retry-Counter erhöhen
```

#### 3. CRM-Feld-Mapping
```
Formularfeld (ACF)
         → FormField-Objekt
         → CrmFieldData-Objekt (enthält CRM-Feld-Key + Wert)
         → SyncEnqueuer speichert CrmFieldData in Queue
         → SyncProcessor extrahiert CRM-Feld-Keys
         → CrmDao sendet mit korrekten Feld-Namen an API
```

**Beispiel:**
```php
// Formular-Feld: "vorname"
// CRM-Feld-Mapping: "firstName"
// In Queue gespeichert als CrmFieldData-Objekt
$fieldData = new CrmFieldData('vorname', 'Max', 'firstName');

// Bei Verarbeitung:
$crmData = [
    'firstName' => ['value' => 'Max', 'mode' => 'replace']
];
```

### API-Endpunkte im Detail

#### Webling-Service API

**Match-Endpunkt:**
```
POST /api/v1/member/match/{group_id}
Content-Type: application/json

Request Body (flache Struktur):
{
  "firstName": "Max",
  "lastName": "Mustermann",
  "email1": "max@example.com"
}

Response:
{
  "status": "match|no_match|ambiguous",
  "member_id": 123,  // wenn match
  "matches": [...]   // wenn ambiguous
}
```

**Save-Endpunkt:**
```
PUT /api/v1/member
Content-Type: application/json

Request Body (mit mode):
{
  "firstName": {"value": "Max", "mode": "replace"},
  "lastName": {"value": "Mustermann", "mode": "replace"},
  "email1": {"value": "max@example.com", "mode": "replace"},
  "groups": {"value": [123], "mode": "append"}
}

Response:
{
  "member_id": 123,
  "status": "created|updated"
}
```

#### Mailchimp-Service API

```
PUT /api/v1/member/{member_id}
Content-Type: application/json

Request Body (flache Struktur):
{
  "firstName": "Max",
  "lastName": "Mustermann",
  "email": "max@example.com",
  "language": "de"
}

Response: HTTP 204 No Content (Erfolg)
```

### Queue-System

**Datenbank-Tabelle:** `wp_options`
- Option-Name: `supt_queue_sync_save`
- Wert: Serialisiertes Array von CrmQueueItem-Objekten

**CrmQueueItem-Struktur:**
```php
class CrmQueueItem {
    private array $data;              // CrmFieldData-Objekte
    private int $submission_id;       // Referenz zur Submission
    private int $attempts = 0;        // Anzahl Verarbeitungsversuche
    private int $last_attempt = 0;    // Timestamp letzter Versuch
}
```

**Retry-Logik:**
- Max. 5 Versuche pro Item
- Min. 39 Minuten zwischen Versuchen
- Nach 5 Fehlversuchen: Item entfernen + Admin-Benachrichtigung

**Lock-Mechanismus:**
- Transient: `sync_processor_lock`
- Timeout: 9 Minuten
- Verhindert parallele Cron-Ausführungen

### Debugging

**Konstanten (wp-config.php):**
```php
// Synchrone Verarbeitung (kein Cron)
define('SUPT_FORM_ASYNC', false);

// Detailliertes Logging
define('LES_VERTS_FORM_DEBUG_LOG', true);
// Loggt in: ./form.log
```

**WP-CLI Befehle:**
```bash
# Queue-Status anzeigen
wp form list

# Queue manuell verarbeiten
wp form process

# Einzelne Submission anzeigen
wp form show <submission_id>
```

**Log-Ausgaben:**
```
submissionId=123 msg=Processing item
submissionId=123 msg=Matched existing member, member_id=456
submissionId=123 msg=Saved to CRM successfully
```

### Docker-Netzwerk

**Mailchimp-Service Verbindung:**
- WordPress-Container: `wp_docker_les_verts`
- Zugriff auf Host-Services: `host.docker.internal:9001`
- Umgebungsvariable: `MAILCHIMP_SERVICE_ENDPOINT`

**Beispiel:**
```php
$endpoint = getenv('MAILCHIMP_SERVICE_ENDPOINT') ?: 'http://host.docker.internal:9001';
```

### Fehlerbehandlung

**Permanente Fehler:**
- API-Konfigurationsfehler
- Authentifizierungsfehler
- → Admin-E-Mail-Benachrichtigung
- → Queue-Verarbeitung gestoppt

**Temporäre Fehler:**
- Netzwerk-Timeouts
- Rate-Limiting
- → Retry mit Backoff
- → Max. 5 Versuche

**Fehler-Logging:**
```php
Util::report_form_error($context, $item, $exception, $form);
Util::debug_log("msg=Error details");
```

### Performance-Optimierungen

**Batch-Verarbeitung:**
- Max. 30 Items pro Cron-Lauf
- Max. 10 CRM-Syncs pro Lauf (Rate-Limiting)
- Lock-Timeout: 9 Minuten

**Asynchrone Verarbeitung:**
- Formular-Response sofort (< 1 Sekunde)
- CRM/Mailchimp-Sync im Hintergrund
- Cron-Intervall: 10 Minuten

**Caching:**
- Nonce-Caching (12 Stunden)
- Form-Konfiguration gecacht
- Queue in wp_options (schneller als Custom Table)

---

## Wichtige Änderungen & Historie

### CRM-Feld-Mapping
**Problem:** Formular-Feldnamen (vorname, nachname) wurden direkt an CRM gesendet.
**Lösung:** CrmFieldData-Objekte speichern sowohl Formular-Feld als auch CRM-Feld-Key.

### Match vs. Save Datenformat
**Problem:** Beide Endpunkte erwarteten unterschiedliche Formate.
**Lösung:**
- Match: Flache Struktur `{"firstName": "Max"}`
- Save: Wrapped Struktur `{"firstName": {"value": "Max", "mode": "replace"}}`

### Docker-Netzwerk
**Problem:** WordPress konnte Mailchimp-Service nicht erreichen.
**Lösung:** `host.docker.internal` für Host-Services nutzen.

### Queue-Dubletten
**Problem:** Gleiche Submission mehrfach in Queue.
**Lösung:** `QueueDao::push_if_not_in_queue()` prüft auf Dubletten.
