#!/bin/bash

# Script de déploiement - DogSchoolResa V1
# Usage: ./deploy.sh

set -e  # Arrêter le script en cas d'erreur

echo "🚀 Déploiement de DogSchoolResa V1..."

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    print_error "Ce script doit être exécuté depuis le répertoire racine de Laravel"
    exit 1
fi

# 1. Sauvegarder la base de données existante (si elle existe)
if [ -f ".env" ]; then
    print_status "Sauvegarde de la base de données existante..."
    if command -v mysqldump &> /dev/null; then
        DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
        DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
        DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)
        
        if [ ! -z "$DB_DATABASE" ] && [ ! -z "$DB_USERNAME" ]; then
            BACKUP_FILE="backup_$(date +%Y%m%d_%H%M%S).sql"
            mysqldump -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" > "$BACKUP_FILE" 2>/dev/null || print_warning "Impossible de sauvegarder la base de données"
            if [ -f "$BACKUP_FILE" ]; then
                print_success "Sauvegarde créée: $BACKUP_FILE"
            fi
        fi
    fi
fi

# 2. Installer/MAJ les dépendances
print_status "Installation des dépendances PHP..."
composer install --optimize-autoloader --no-dev

print_status "Installation des dépendances Node.js..."
npm install

# 3. Compiler les assets
print_status "Compilation des assets..."
npm run build

# 4. Configuration de l'environnement
if [ ! -f ".env" ]; then
    print_status "Création du fichier .env..."
    cp .env.example .env
    php artisan key:generate
    print_warning "N'oubliez pas de configurer votre base de données dans le fichier .env"
else
    print_status "Fichier .env déjà présent"
fi

# 5. Vider tous les caches
print_status "Vidage des caches..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 6. Exécuter les migrations
print_status "Exécution des migrations..."
php artisan migrate --force

# 7. Configuration des permissions
print_status "Configuration des permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || print_warning "Impossible de changer le propriétaire (nécessite sudo)"

# 8. Vérifier l'installation
print_status "Vérification de l'installation..."
if php artisan about > /dev/null 2>&1; then
    print_success "Laravel est correctement installé"
else
    print_error "Problème avec l'installation de Laravel"
    exit 1
fi

# 9. Afficher les informations importantes
echo ""
print_success "🎉 Déploiement terminé avec succès !"
echo ""
echo "📋 Informations importantes :"
echo "=============================="
echo ""
echo "🔐 Comptes par défaut :"
echo "   Super Admin: admin@ceccondat.fr / admin"
echo "   Admin Club: montuy.alexis@gmail.com / admin"
echo "   Test User: test@ceccondat.fr / test"
echo ""
echo "⚠️  SÉCURITÉ : Changez immédiatement les mots de passe par défaut !"
echo ""
echo "📧 Configuration email :"
echo "   Modifiez le fichier .env pour configurer l'envoi d'emails"
echo ""
echo "🔄 Cron job (optionnel) :"
echo "   * * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "📚 Documentation : docs/DEPLOYMENT_V1.md"
echo ""
print_success "🚀 DogSchoolResa V1 est prêt à être utilisé !"
