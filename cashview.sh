#!/bin/bash
################################################################################
# CashView - Laufende Kosten Cronjob
#
# Beschreibung:
#   Bucht automatisch laufende Kosten am korrekten Tag des Monats.
#   Läuft täglich um 0:00 Uhr und prüft, ob heute Buchungen fällig sind.
#
# Verwendung:
#   1. Datei nach /usr/local/bin/cashview-recurring.sh kopieren
#   2. Ausführbar machen: chmod +x /usr/local/bin/cashview-recurring.sh
#   3. Cronjob einrichten: 0 0 * * * /usr/local/bin/cashview-recurring.sh
#
# Logik:
#   - Prüft für jede laufende Kostenstelle, ob sie heute fällig ist
#   - Berücksichtigt first_run Datum als Startpunkt
#   - Berechnet auf Basis von modulo, ob genug Monate vergangen sind
#   - Verhindert Doppelbuchungen am gleichen Tag
#   - Behandelt Monate mit weniger Tagen korrekt (z.B. 31. in Monaten mit 30 Tagen)
#
# Beispiele:
#   first_run = 2024-01-15, modulo = 1 → Buchung jeden 15. des Monats
#   first_run = 2024-01-31, modulo = 3 → Buchung alle 3 Monate am 31. (oder letzter Tag)
#   first_run = 2024-02-29, modulo = 1 → In Feb am 29., sonst am 28.
#
# Version: 2.0
# Datum: 2026-01-28
################################################################################

# Konfiguration
DB_HOST="192.168.5.103"
DB_USER="cashview"
DB_PASS="cash123"
DB_NAME="cashview"
LOG_FILE="/var/log/cashview_recurring.log"

# Logging-Funktionen
log_info() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] INFO: $1" | tee -a "$LOG_FILE"
}

log_error() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] ERROR: $1" | tee -a "$LOG_FILE"
}

log_success() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] SUCCESS: $1" | tee -a "$LOG_FILE"
}

# Separator für bessere Lesbarkeit
echo "================================================================================" >> "$LOG_FILE"
log_info "CashView Recurring Costs Cronjob gestartet"

# Prüfen ob mysql-client installiert ist
if ! command -v mysql &> /dev/null; then
    log_error "MySQL Client ist nicht installiert!"
    exit 1
fi

# Datenbankverbindung testen
if ! mysql -h "$DB_HOST" -u "$DB_USER" --password="$DB_PASS" \
           --connect-timeout=5 -e "SELECT 1;" "$DB_NAME" &>/dev/null; then
    log_error "Datenbankverbindung fehlgeschlagen"
    exit 1
fi

log_info "Datenbankverbindung erfolgreich"

# Heute's Datum
CURRENT_DATE=$(date '+%Y-%m-%d')
CURRENT_DAY=$(date '+%d')
CURRENT_MONTH=$(date '+%m')
CURRENT_YEAR=$(date '+%Y')

# Maximaler Tag im aktuellen Monat
LAST_DAY_OF_MONTH=$(date -d "$CURRENT_YEAR-$CURRENT_MONTH-01 +1 month -1 day" '+%d')

log_info "Aktuelles Datum: $CURRENT_DATE (Tag $CURRENT_DAY von $LAST_DAY_OF_MONTH)"

# SQL-Query um fällige laufende Kosten zu finden
# Diese komplexe Query berücksichtigt:
# 1. Anzahl Monate seit first_run muss durch modulo teilbar sein
# 2. Tag des Monats muss übereinstimmen (oder heute ist letzter Tag und first_run-Tag existiert nicht in diesem Monat)
# 3. Keine Buchung darf heute schon existieren
READ_SQL="
SELECT
    l.id,
    l.Wert,
    l.ktoID,
    l.katID,
    l.manId,
    l.Beschreibung,
    l.first_run,
    l.modulo,
    DAY(l.first_run) as target_day,
    TIMESTAMPDIFF(MONTH, l.first_run, CURDATE()) as months_passed
FROM laufendes l
WHERE
    -- Genug Zeit seit first_run vergangen
    l.first_run <= CURDATE()
    -- Anzahl Monate ist durch modulo teilbar
    AND MOD(TIMESTAMPDIFF(MONTH, l.first_run, CURDATE()), l.modulo) = 0
    -- Entweder der Tag stimmt überein, oder es ist der letzte Tag des Monats
    -- und der Ziel-Tag existiert in diesem Monat nicht
    AND (
        DAY(CURDATE()) = DAY(l.first_run)
        OR (
            DAY(CURDATE()) = DAY(LAST_DAY(CURDATE()))
            AND DAY(l.first_run) > DAY(LAST_DAY(CURDATE()))
        )
    )
    -- Noch keine Buchung heute für diese laufende Kosten
    AND NOT EXISTS (
        SELECT 1
        FROM transaktionen t
        WHERE t.KtoID = l.ktoID
          AND t.katID = l.katID
          AND t.manId = l.manId
          AND t.Wert = l.Wert
          AND DATE(t.Datum) = CURDATE()
    )
ORDER BY l.id;
"

# Finde fällige Kosten
log_info "Suche nach fälligen laufenden Kosten..."

# Führe Query aus und speichere Ergebnis
RESULT=$(mysql -h "$DB_HOST" -u "$DB_USER" --password="$DB_PASS" "$DB_NAME" \
    -N -e "$READ_SQL" 2>&1)

if [ $? -ne 0 ]; then
    log_error "Fehler beim Abfragen der fälligen Kosten: $RESULT"
    exit 1
fi

# Zähle Anzahl Zeilen
COUNT=$(echo "$RESULT" | grep -c "^")

if [ -z "$RESULT" ] || [ "$COUNT" -eq 0 ]; then
    log_info "Keine laufenden Kosten heute fällig"
    echo "================================================================================" >> "$LOG_FILE"
    echo "" >> "$LOG_FILE"
    exit 0
fi

log_info "Anzahl fälliger Kosten: $COUNT"

# Verarbeite jede Zeile
BOOKED_COUNT=0
ERROR_COUNT=0

while IFS=$'\t' read -r id wert ktoID katID manId beschreibung first_run modulo target_day months_passed; do
    log_info "Verarbeite: ID=$id, Beschreibung='$beschreibung', Betrag=$wert, Monate seit Start=$months_passed"

    # Erstelle INSERT Statement
    INSERT_SQL="
    INSERT INTO transaktionen (Wert, Datum, KtoID, katID, manId)
    VALUES ($wert, NOW(), $ktoID, $katID, $manId);
    "

    # Führe Buchung aus
    INSERT_RESULT=$(mysql -h "$DB_HOST" -u "$DB_USER" --password="$DB_PASS" "$DB_NAME" \
        -e "$INSERT_SQL" 2>&1)

    if [ $? -eq 0 ]; then
        log_success "Gebucht: $beschreibung ($wert €) auf Konto $ktoID, Kategorie $katID, Mandant $manId"
        ((BOOKED_COUNT++))
    else
        log_error "Fehler beim Buchen von ID $id: $INSERT_RESULT"
        ((ERROR_COUNT++))
    fi

done <<< "$RESULT"

# Zusammenfassung
log_info "=== Zusammenfassung ==="
log_info "Erfolgreich gebucht: $BOOKED_COUNT"
log_info "Fehler: $ERROR_COUNT"

# Zeige Details der gebuchten Transaktionen
if [ $BOOKED_COUNT -gt 0 ]; then
    log_info "Details der heutigen Buchungen:"

    DETAIL_SQL="
    SELECT
        t.Wert,
        k.Bez as Konto,
        kat.bez as Kategorie,
        TIME(t.Datum) as Uhrzeit,
        t.manId
    FROM transaktionen t
    LEFT JOIN Konten k ON t.KtoID = k.id
    LEFT JOIN kategorien kat ON t.katID = kat.ID
    WHERE DATE(t.Datum) = CURDATE()
    ORDER BY t.Datum DESC;
    "

    mysql -h "$DB_HOST" -u "$DB_USER" --password="$DB_PASS" "$DB_NAME" \
        -t -e "$DETAIL_SQL" >> "$LOG_FILE" 2>&1
fi

echo "================================================================================" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"

# Exit-Code: 0 wenn alles OK, 1 wenn Fehler aufgetreten sind
if [ $ERROR_COUNT -gt 0 ]; then
    exit 1
else
    exit 0
fi