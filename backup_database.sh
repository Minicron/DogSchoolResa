#!/bin/bash

# Script de sauvegarde de la base de données DogSchoolResa
# Usage: ./backup_database.sh [nom_du_backup]

BACKUP_NAME=${1:-"backup_$(date +%Y%m%d_%H%M%S)"}
BACKUP_DIR="./database/backups"
DB_PATH="./database/database.sqlite"

# Créer le répertoire de sauvegarde s'il n'existe pas
mkdir -p "$BACKUP_DIR"

# Vérifier que la base de données existe
if [ ! -f "$DB_PATH" ]; then
    echo "❌ Base de données non trouvée: $DB_PATH"
    exit 1
fi

# Créer la sauvegarde
BACKUP_FILE="$BACKUP_DIR/${BACKUP_NAME}.sqlite"
cp "$DB_PATH" "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo "✅ Sauvegarde créée avec succès: $BACKUP_FILE"
    echo "📊 Taille: $(du -h "$BACKUP_FILE" | cut -f1)"
else
    echo "❌ Erreur lors de la création de la sauvegarde"
    exit 1
fi

# Lister les sauvegardes existantes
echo ""
echo "📋 Sauvegardes disponibles:"
ls -la "$BACKUP_DIR"/*.sqlite 2>/dev/null | head -10
