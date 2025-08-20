#!/bin/bash

# Script de restauration de la base de données DogSchoolResa
# Usage: ./restore_database.sh [nom_du_backup]

BACKUP_NAME=${1:-""}
BACKUP_DIR="./database/backups"
DB_PATH="./database/database.sqlite"

# Vérifier si un nom de backup a été fourni
if [ -z "$BACKUP_NAME" ]; then
    echo "❌ Veuillez spécifier le nom du backup à restaurer"
    echo ""
    echo "📋 Sauvegardes disponibles:"
    ls -la "$BACKUP_DIR"/*.sqlite 2>/dev/null || echo "Aucune sauvegarde trouvée"
    echo ""
    echo "Usage: ./restore_database.sh [nom_du_backup]"
    exit 1
fi

# Construire le chemin complet du fichier de backup
BACKUP_FILE="$BACKUP_DIR/${BACKUP_NAME}.sqlite"

# Vérifier que le fichier de backup existe
if [ ! -f "$BACKUP_FILE" ]; then
    echo "❌ Backup non trouvé: $BACKUP_FILE"
    echo ""
    echo "📋 Sauvegardes disponibles:"
    ls -la "$BACKUP_DIR"/*.sqlite 2>/dev/null || echo "Aucune sauvegarde trouvée"
    exit 1
fi

# Demander confirmation
echo "⚠️  ATTENTION: Cette opération va remplacer la base de données actuelle !"
echo "📁 Backup à restaurer: $BACKUP_FILE"
echo "📊 Taille: $(du -h "$BACKUP_FILE" | cut -f1)"
echo ""
read -p "Êtes-vous sûr de vouloir continuer ? (y/N): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Restauration annulée"
    exit 1
fi

# Créer une sauvegarde de la base actuelle avant restauration
CURRENT_BACKUP="$BACKUP_DIR/pre_restore_$(date +%Y%m%d_%H%M%S).sqlite"
if [ -f "$DB_PATH" ]; then
    cp "$DB_PATH" "$CURRENT_BACKUP"
    echo "💾 Sauvegarde de la base actuelle: $CURRENT_BACKUP"
fi

# Restaurer la base de données
cp "$BACKUP_FILE" "$DB_PATH"

if [ $? -eq 0 ]; then
    echo "✅ Base de données restaurée avec succès !"
    echo "📊 Taille restaurée: $(du -h "$DB_PATH" | cut -f1)"
else
    echo "❌ Erreur lors de la restauration"
    exit 1
fi
