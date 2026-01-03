# 💰 CashView - Persönliches Haushaltsbuch

Ein modernes, mandantenbasiertes Haushaltsbuch zur Verwaltung persönlicher Finanzen mit umfangreichen Statistiken und Trendanalysen.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-4.4.1-7952B3?style=flat-square&logo=bootstrap)
![Mobile First](https://img.shields.io/badge/Mobile-First-00C7B7?style=flat-square)
![Dark Mode](https://img.shields.io/badge/Dark-Mode-000000?style=flat-square)

## 📋 Inhaltsverzeichnis

- [Features](#-features)
- [Screenshots](#-screenshots)
- [Voraussetzungen](#-voraussetzungen)
- [Installation](#-installation)
- [Konfiguration](#-konfiguration)
- [Verwendung](#-verwendung)
- [Responsive Design](#-responsive-design)
- [Technologie-Stack](#-technologie-stack)
- [Projekt-Struktur](#-projekt-struktur)
- [Datenbank-Schema](#-datenbank-schema)
- [Roadmap](#-roadmap)
- [Lizenz](#-lizenz)

## ✨ Features

### 📊 Finanzübersicht
- **Echtzeit-Kontostand**: Aktueller Überblick über alle Konten mit verfügbarem Betrag und Dispo
- **Tagesbudget**: Automatische Berechnung des verfügbaren Betrags pro Tag
- **Multi-Konto-Verwaltung**: Verwaltung mehrerer Konten pro Mandant
- **Schnelle Buchungen**: Einfaches Erfassen von Transaktionen mit Kategorie und Konto

### 📈 Statistiken & Analysen
- **30-Tage-Perioden-Vergleich**: Rollierender Vergleich der letzten 3x 30 Tage
- **Trend-Analyse**: Automatische Erkennung von Ausgaben-Trends mit visuellen Indikatoren
- **Kreisdiagramme**: Ausgabenverteilung nach Kategorien (30 Tage & Gesamt)
- **Verlaufsdiagramm**: Grafische Darstellung des Guthabenverlaufs über 30 Tage
- **Kategorien-Übersicht**: Detaillierte Aufschlüsselung der Ausgaben nach Kategorien

### ⚙️ Konfiguration
- **Mandantenfähig**: Mehrere Benutzer mit eigenen Kategorien und Konten
- **Individuelle Kategorien**: Erstellen und verwalten eigener Ausgaben-Kategorien
- **Farbverwaltung**: Visual Color Picker für Kategorien
- **Globale Kategorien**: Zentral verwaltete Kategorien für alle Mandanten

### 📱 Mobile-First Design
- **iPhone 13 optimiert**: (390x844px)
- **iPad 10 optimiert**: (820x1180px)
- **Desktop optimiert**: (1920x1080+)
- **Touch-freundlich**: Große Buttons und optimierte Formularfelder
- **Dark Mode**: Automatische Anpassung an System-Theme

### 🎨 Weitere Features
- **Responsive Tabellen**: Horizontales Scrolling auf kleinen Bildschirmen
- **Benzinpreis-Integration**: Aktueller Benzinpreis-Vergleich per Modal
- **Euro-Formatierung**: Korrekte Darstellung von Währungsbeträgen
- **Visuelle Feedback**: Farbcodierte Trend-Indikatoren

## 🖼️ Screenshots

### Desktop Ansicht
```
┌─────────────────────────────────────────┐
│  CashView - Finanzübersicht             │
│  ┌───────────────┬───────────────┐      │
│  │ Kontostand    │ Tagesbudget   │      │
│  │ 2.450,00 €    │ 81,67 €       │      │
│  └───────────────┴───────────────┘      │
│                                          │
│  📈 Ausgaben-Trend (3x 30-Tage)         │
│  ██████████████████████ Letzte 30 Tage  │
│  ████████████████ Vor 30-60 Tagen       │
│  ███████████████████ Vor 60-90 Tagen    │
└─────────────────────────────────────────┘
```

## 🔧 Voraussetzungen

- **PHP**: 7.4 oder höher
- **MySQL/MariaDB**: 5.7+ / 10.3+
- **Webserver**: Apache oder Nginx
- **PHP Extensions**:
    - mysqli
    - gd (für Diagramme)
- **Browser**: Modern mit HTML5 & CSS3 Support

## 📦 Installation

### 1. Repository klonen
```bash
git clone https://github.com/username/cashview.git
cd cashview
```

### 2. Datenbank erstellen
```bash
mysql -u root -p
```

```sql
CREATE DATABASE cashview CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cashview'@'localhost' IDENTIFIED BY 'dein_sicheres_passwort';
GRANT ALL PRIVILEGES ON cashview.* TO 'cashview'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Datenbank-Schema importieren
```bash
mysql -u cashview -p cashview < db.sql
```

### 4. Datenbank-Update für mandantenspezifische Kategorien
```bash
mysql -u cashview -p cashview < db_update.sql
```

### 5. Konfiguration anpassen

Öffne die PHP-Dateien und passe die Datenbankverbindung an:

```php
// In index.php, stats.php, config.php
mysqli_connect("localhost", "cashview", "dein_passwort", "cashview")
```

### 6. Font-Dateien bereitstellen

Lade NotoSans-Regular.ttf herunter und platziere sie:
```
cashview/
└── media/
    └── NotoSans-Regular.ttf
```

Download: [Google Fonts - Noto Sans](https://fonts.google.com/noto/specimen/Noto+Sans)

### 7. Webserver konfigurieren

#### Apache (.htaccess)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /cashview/
    
    # Zugriff auf Datenbank-Dateien verhindern
    <Files "db.sql">
        Require all denied
    </Files>
    <Files "db_update.sql">
        Require all denied
    </Files>
</IfModule>
```

#### Nginx
```nginx
location /cashview/ {
    index index.php;
    
    # Zugriff auf Datenbank-Dateien verhindern
    location ~* \.(sql)$ {
        deny all;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

## ⚙️ Konfiguration

### Initialisierung

1. **Mandanten anlegen**: Füge in der Tabelle `Konten` einen Eintrag mit `manId` hinzu
2. **Initialwerte setzen**: Trage Anfangsbestände in `Initialwerte` ein
3. **Kategorien erstellen**: Nutze die Konfigurationsseite oder lege globale Kategorien an

### Beispiel SQL-Befehle

```sql
-- Mandant 1 mit Giro-Konto anlegen
INSERT INTO Konten (id, Bez, Grenze, manId) VALUES (1, 'Girokonto', 0, 1);

-- Anfangsbestand setzen
INSERT INTO Initialwerte (initId, Betrag, KtoId) VALUES (1, 3000, 1);

-- Globale Kategorie anlegen
INSERT INTO kategorien (ID, bez, sortorder, statscolor, manId) 
VALUES (1, 'Lebensmittel', 10, '255,99,71', 0);
```

## 🚀 Verwendung

### Zugriff auf die Anwendung

```
http://localhost/cashview/index.php?manId=1
```

**Wichtig**: Die `manId` muss als GET-Parameter übergeben werden!

### Navigation

- **Hauptseite** (`index.php`): Kontoübersicht und Buchungsformular
- **Statistik** (`stats.php`): Detaillierte Analysen und Diagramme
- **Konfiguration** (`config.php`): Kategorien-, Konten- und Laufende-Kosten-Verwaltung
- **Setup** (`setup.php`): Initiales Anlegen neuer Mandanten

### Buchung erstellen

1. Betrag eingeben (Komma oder Punkt als Dezimaltrennzeichen)
2. Kategorie auswählen
3. Konto auswählen
4. "Speichern" klicken

### Kategorie erstellen

1. Zur Konfigurationsseite navigieren
2. Tab "Kategorien" wählen
3. Formular "Neue Kategorie hinzufügen" ausfüllen
4. Farbe mit Color Picker auswählen
5. "Kategorie hinzufügen" klicken

### Konto erstellen

1. Zur Konfigurationsseite navigieren
2. Tab "Konten" wählen
3. Kontobezeichnung, Anfangsbestand und Dispogrenze eingeben
4. "Konto hinzufügen" klicken

### Laufende Kosten einrichten

1. Zur Konfigurationsseite navigieren
2. Tab "Laufende Kosten" wählen
3. Beschreibung, Betrag, Intervall, Konto und Kategorie festlegen
4. "Laufende Kosten hinzufügen" klicken

**Wichtig**: Laufende Kosten werden automatisch am Monatsersten durch einen Cronjob gebucht!

## 🔄 Automatische Buchungen (Cronjob)

### Cronjob einrichten

Laufende Kosten werden automatisch jeden Monatsersten gebucht. Dazu muss ein Cronjob eingerichtet werden:

```bash
# Cronjob bearbeiten
crontab -e

# Folgende Zeile hinzufügen (läuft am 1. jeden Monats um 00:05 Uhr)
5 0 1 * * /pfad/zu/cashview/cronjob_laufende_kosten.sh >> /var/log/cashview_cronjob.log 2>&1
```

### Cronjob-Script erstellen

Erstelle die Datei `cronjob_laufende_kosten.sh`:

```bash
#!/bin/bash
# CashView - Automatische Buchung laufender Kosten
# Wird jeden Monatsersten ausgeführt

mysql -h 192.168.5.103 -u cashview --password=cash123 cashview <<EOF
INSERT INTO transaktionen(Wert, KtoID, katID, Datum, manId) 
SELECT Wert, KtoID, katID, NOW(), manId 
FROM laufendes 
WHERE MOD(MONTH(NOW()), modulo) = 0;
COMMIT;
EXIT
EOF
```

Mache das Script ausführbar:
```bash
chmod +x cronjob_laufende_kosten.sh
```

### Funktionsweise

- **Modulo-Berechnung**: Das Intervall wird über den Modulo-Wert gesteuert
    - `modulo = 1`: Jeden Monat (30 Tage → monatlich)
    - `modulo = 3`: Alle 3 Monate (90 Tage → quartalsweise)
    - `modulo = 12`: Alle 12 Monate (365 Tage → jährlich)

- **Beispiel**: Bei `modulo = 3` wird gebucht, wenn `MOD(MONTH(NOW()), 3) = 0`
    - Januar (1): MOD(1, 3) = 1 → **Keine Buchung**
    - Februar (2): MOD(2, 3) = 2 → **Keine Buchung**
    - März (3): MOD(3, 3) = 0 → **✅ Buchung**
    - April (4): MOD(4, 3) = 1 → **Keine Buchung**
    - ...

### Logging aktivieren

Für Fehlersuche empfiehlt sich Logging:

```bash
# Log-Datei erstellen
sudo touch /var/log/cashview_cronjob.log
sudo chown www-data:www-data /var/log/cashview_cronjob.log

# Erweitere das Script für besseres Logging:
#!/bin/bash
echo "=== CashView Cronjob Start: $(date) ===" >> /var/log/cashview_cronjob.log

mysql -h 192.168.5.103 -u cashview --password=cash123 cashview <<EOF
INSERT INTO transaktionen(Wert, KtoID, katID, Datum, manId) 
SELECT Wert, KtoID, katID, NOW(), manId 
FROM laufendes 
WHERE MOD(MONTH(NOW()), modulo) = 0;
COMMIT;
EXIT
EOF

if [ $? -eq 0 ]; then
    echo "✅ Erfolgreich abgeschlossen" >> /var/log/cashview_cronjob.log
else
    echo "❌ Fehler beim Ausführen" >> /var/log/cashview_cronjob.log
fi

echo "=== CashView Cronjob Ende: $(date) ===" >> /var/log/cashview_cronjob.log
echo "" >> /var/log/cashview_cronjob.log
```

### Manuelles Testen

Vor der Einrichtung des Cronjobs sollte das Script manuell getestet werden:

```bash
# Script ausführen
./cronjob_laufende_kosten.sh

# Log überprüfen
tail -f /var/log/cashview_cronjob.log

# Datenbank prüfen
mysql -h 192.168.5.103 -u cashview --password=cash123 -e "SELECT * FROM transaktionen ORDER BY Datum DESC LIMIT 10;" cashview
```

## 📱 Responsive Design

### Breakpoints

| Device | Breakpoint | Optimierungen |
|--------|-----------|---------------|
| **Mobile** | < 428px | Kompakte Navigation, volle Breite, Touch-optimiert |
| **iPhone 13** | 390-428px | Optimierte Schriftgrößen, Touch-Targets |
| **iPad 10** | 768-1024px | 2-Spalten-Layout, größere Buttons |
| **Desktop** | > 1025px | Grid-Layout, maximale Breite 1140px |

### Dark Mode

Automatische Erkennung via `prefers-color-scheme`:
- Dunkle Hintergründe (#121212, #1e1e1e)
- Optimierte Textfarben für Lesbarkeit
- Angepasste Formularelemente
- Farbcodierte Alerts

## 🛠️ Technologie-Stack

### Frontend
- **HTML5**: Semantisches Markup
- **CSS3**: Custom Properties, Flexbox, Grid
- **Bootstrap 4.4.1**: UI-Framework
- **JavaScript (Vanilla)**: Color Picker, Form Handling
- **jQuery 3.5.1**: DataTables Integration

### Backend
- **PHP 7.4+**: Server-seitige Logik
- **MySQLi**: Datenbankverbindung
- **GD Library**: Diagramm-Generierung

### Externe Dienste
- **Benzinpreis.de API**: Tankpreise
- **DataTables**: Sortierbare Tabellen

## 📁 Projekt-Struktur

```
cashview/
├── index.php           # Hauptseite (Übersicht & Buchung)
├── stats.php           # Statistik-Seite
├── config.php          # Konfiguration (Kategorien, Konten, Laufende Kosten)
├── setup.php           # Setup für neue Mandanten
├── db.sql              # Datenbank-Schema
├── db_update.sql       # Update für mandantenspezifische Kategorien
├── cronjob_laufende_kosten.sh  # Cronjob-Script für automatische Buchungen
├── favicon.ico         # Favicon
├── media/
│   └── NotoSans-Regular.ttf  # Font für Diagramme
└── README.md           # Diese Datei
```

## 🗄️ Datenbank-Schema

### Tabellen

#### `Konten`
```sql
id INT PRIMARY KEY
Bez VARCHAR(50)           -- Kontobezeichnung
Grenze VARCHAR(45)        -- Dispogrenze
manId INT                 -- Mandanten-ID
```

#### `Initialwerte`
```sql
initId INT PRIMARY KEY
Betrag INT                -- Anfangsbestand
KtoId INT                 -- FK zu Konten
```

#### `kategorien`
```sql
ID INT PRIMARY KEY
bez VARCHAR(45)           -- Kategoriebezeichnung
sortorder INT             -- Sortierreihenfolge
statscolor VARCHAR(45)    -- RGB-Farbe (z.B. "255,0,0")
manId INT                 -- Mandanten-ID (0 = global)
```

#### `transaktionen`
```sql
wert INT                  -- Betrag in Euro
KtoID INT                 -- FK zu Konten
katID INT                 -- FK zu kategorien
Datum DATETIME            -- Buchungsdatum
manId INT                 -- Mandanten-ID
```

### ER-Diagramm

```
┌─────────────┐         ┌──────────────┐
│   Konten    │────┬────│Initialwerte  │
│   manId     │    │    │   Betrag     │
└─────────────┘    │    └──────────────┘
       │           │
       │           │    ┌──────────────┐
       │           └────│transaktionen │
       │                │   wert       │
       │                │   Datum      │
       │                └──────────────┘
       │                       │
       │                       │
┌──────▼──────┐               │
│ kategorien  │◄──────────────┘
│   manId     │
│ statscolor  │
└─────────────┘
```

## 🎯 Roadmap

### Version 2.0 (geplant)
- [ ] Multi-User-Login mit Authentifizierung
- [ ] Import/Export von Transaktionen (CSV)
- [ ] Wiederkehrende Buchungen
- [ ] Budget-Limits pro Kategorie
- [ ] E-Mail-Benachrichtigungen
- [ ] PDF-Export von Statistiken
- [ ] REST API
- [ ] Progressive Web App (PWA)

### Version 2.1 (geplant)
- [ ] Mehrwährungsunterstützung
- [ ] Gemeinsame Haushalte (Mehrere Personen pro Mandant)
- [ ] Belege als Anhang zu Buchungen
- [ ] Erweiterte Filter-Optionen
- [ ] Dashboard mit Widgets

## 🤝 Contributing

Beiträge sind willkommen! Bitte beachte:

1. Fork das Repository
2. Erstelle einen Feature-Branch (`git checkout -b feature/AmazingFeature`)
3. Committe deine Änderungen (`git commit -m 'Add some AmazingFeature'`)
4. Push zum Branch (`git push origin feature/AmazingFeature`)
5. Öffne einen Pull Request

### Coding Standards
- PSR-12 für PHP Code
- Semantisches HTML5
- Mobile-First CSS
- Kommentare in Deutsch

## 🐛 Bug Reports

Gefundene Bugs bitte als GitHub Issue melden mit:
- Beschreibung des Problems
- Schritte zur Reproduktion
- Erwartetes vs. tatsächliches Verhalten
- Screenshots (falls relevant)
- Browser/Device-Information


## 🙏 Danksagungen

- Bootstrap Team für das exzellente UI-Framework
- Chart.js Community
- Benzinpreis.de für die API
- Alle Mitwirkenden und Tester

---

**⭐ Wenn dir dieses Projekt gefällt, gib ihm einen Stern auf GitHub!**