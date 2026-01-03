#!/bin/bash
################################################################################
# CashView - Automatische Buchung laufender Kosten
#
# Beschreibung:
#   Dieses Script bucht alle laufenden Kosten automatisch in die Transaktionen-
#   Tabelle, wenn das Modulo-Intervall zum aktuellen Monat passt.
#
# Verwendung:
#   1. Datei nach /pfad/zu/cashview/cronjob_laufende_kosten.sh kopieren
#   2. Ausführbar machen: chmod +x cronjob_laufende_kosten.sh
#   3. Cronjob einrichten: crontab -e
#      5 0 1 * * /pfad/zu/cashview/cronjob_laufende_kosten.sh >> /var/log/cashview_cronjob.log 2>&1
#
# Intervall-Logik:
#   - modulo = 1  → Jeden Monat (monatlich)
#   - modulo = 3  → Alle 3 Monate (quartalsweise)
#   - modulo = 12 → Alle 12 Monate (jährlich)
#
# Beispiel:
#   Bei modulo = 3 wird gebucht, wenn MOD(MONTH(NOW()), 3) = 0
#   → März, Juni, September, Dezember
#
# Version: 1.0
# Datum: 2026-01-03
################################################################################

# Konfiguration
DB_HOST="192.168.5.103"
DB_USER="cashview"
DB_PASS="cash123"
DB_NAME="cashview"
LOG_FILE="/var/log/cashview_cronjob.log"

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

# Separator für bessere Lesbarkeit im Log
echo "================================================================================" >> "$LOG_FILE"
log_info "CashView Cronjob gestartet"
log_info "Aktueller Monat: $(date '+%B %Y')"

# Prüfen ob mysql-client installiert ist
if ! command -v mysql &> /dev/null; then
    log_error "MySQL Client ist nicht installiert!"
    exit 1
fi

# Zähle wie viele laufende Kosten fällig sind
log_info "Prüfe fällige laufende Kosten..."
FÄLLIGE_KOSTEN=$(mysql -h "$DB_HOST" -u "$DB_USER" --password="$DB_PASS" "$DB_NAME" -N -e \
    "SELECT COUNT(*) FROM laufendes WHERE MOD(MONTH(NOW()), modulo) = 0;")

if [ $? -ne 0 ]; then
    log_error "Fehler beim Abfragen der fälligen Kosten"
    exit 1
fi

log_info "Anzahl fälliger Kosten: $FÄLLIGE_KOSTEN"

if [ "$FÄLLIGE_KOSTEN" -eq 0 ]; then
    log_info "Keine laufenden Kosten fällig - Script beendet"
    echo "================================================================================" >> "$LOG_FILE"
    echo "" >> "$LOG_FILE"
    exit 0
fi

# Führe die Buchung aus
log_info "Beginne mit der Buchung..."

mysql -h "$DB_HOST" -u "$DB_USER" --password="$DB_PASS" "$DB_NAME" <<EOF
-- Laufende Kosten buchen
INSERT INTO transaktionen(Wert, KtoID, katID, Datum, manId)
SELECT Wert, KtoID, katID, NOW(), manId
FROM laufendes
WHERE MOD(MONTH(NOW()), modulo) = 0;

-- Änderungen committen
COMMIT;
EOF

# Prüfe Erfolg
if [ $? -eq 0 ]; then
    log_success "Erfolgreich $FÄLLIGE_KOSTEN laufende Kosten gebucht"

    # Zeige Details der gebuchten Transaktionen
    log_info "Details der gebuchten Transaktionen:"
    mysql -h "$DB_HOST" -u "$DB_USER" --password="$DB_PASS" "$DB_NAME" -t -e \
        "SELECT t.Wert, k.Bez as Konto, kat.bez as Kategorie, t.Datum, t.manId
         FROM transaktionen t
         LEFT JOIN Konten k ON t.KtoID = k.id
         LEFT JOIN kategorien kat ON t.katID = kat.ID
         WHERE DATE(t.Datum) = CURDATE()
         ORDER BY t.Datum DESC;" >> "$LOG_FILE" 2>&1

    exit 0
else
    log_error "Fehler beim Buchen der laufenden Kosten"
    exit 1
fi

echo "================================================================================" >> "$LOG_FILE"
echo "" >> "$LOG_FILE"