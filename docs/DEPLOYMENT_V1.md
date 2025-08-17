# Guide de Déploiement - DogSchoolResa V1

## Vue d'ensemble

Ce guide décrit le processus de mise en production de la version 1.0 de DogSchoolResa, une application de gestion de réservations pour clubs d'éducation canine.

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- Base de données MySQL/PostgreSQL/SQLite
- Serveur web (Apache/Nginx)
- Node.js et npm (pour la compilation des assets)

## Installation

### 1. Cloner le projet
```bash
git clone <repository-url>
cd DogSchoolResa
```

### 2. Installer les dépendances
```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build
```

### 3. Configuration de l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configuration de la base de données
Modifier le fichier `.env` :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dogschoolresa
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Configuration de l'email
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=cec-condat@yahoo.fr
MAIL_FROM_NAME="CEC Condat"
```

### 6. Exécuter les migrations
```bash
php artisan migrate
```

### 7. Configuration des permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Configuration du cron (optionnel)
Ajouter à la crontab :
```bash
* * * * * cd /path/to/DogSchoolResa && php artisan schedule:run >> /dev/null 2>&1
```

## Comptes par défaut

### Super Administrateur
- **Email** : `admin@ceccondat.fr`
- **Mot de passe** : `admin`
- **Rôle** : `super-admin`
- **Fonctionnalités** : Accès complet à toutes les fonctionnalités

### Administrateur Club
- **Email** : `montuy.alexis@gmail.com`
- **Mot de passe** : `admin`
- **Rôle** : `admin-club`
- **Fonctionnalités** : Gestion des créneaux, des utilisateurs et des inscriptions

### Utilisateur de test
- **Email** : `test@ceccondat.fr`
- **Mot de passe** : `test`
- **Rôle** : `user`
- **Fonctionnalités** : Inscription aux cours, consultation du calendrier

## Données initiales

### Club créé
- **Nom** : CEC Condat
- **Ville** : Condat-Sur-Vienne
- **Site web** : https://ceccondat.e-monsite.com/
- **Description** : Club d'Éducation Canine de Condat-Sur-Vienne

## Fonctionnalités principales

### Gestion des capacités
- **Aucune limite** : Pas de restriction sur le nombre de participants
- **Limite fixe** : Nombre maximum défini par l'administrateur
- **Limite dynamique** : Calculée automatiquement (nombre de moniteurs × 5)

### Système de liste d'attente
- Inscription automatique en liste d'attente quand un cours est plein
- Notification automatique quand une place se libère
- Inscription automatique du premier en liste d'attente

### Clôture automatique des inscriptions
- Configuration du délai de clôture (12h, 24h, 48h, 72h)
- Notification automatique aux administrateurs
- Commande artisan pour la vérification (`php artisan slots:check-closing`)

### Modification d'horaire
- Confirmation obligatoire pour les changements d'horaire
- Annulation automatique des cours futurs
- Notification des participants
- Création de nouveaux cours avec le nouvel horaire

### Notifications par email
- Templates modernes et responsifs
- Informations de contact mises à jour
- Contenu en français

## Sécurité

### Changement des mots de passe
**IMPORTANT** : Changer immédiatement les mots de passe par défaut après l'installation :
```bash
php artisan tinker
```

```php
// Changer le mot de passe du super admin
$user = \App\Models\User::where('email', 'admin@ceccondat.fr')->first();
$user->password = Hash::make('nouveau_mot_de_passe_securise');
$user->save();

// Changer le mot de passe de l'admin club
$user = \App\Models\User::where('email', 'montuy.alexis@gmail.com')->first();
$user->password = Hash::make('nouveau_mot_de_passe_securise');
$user->save();
```

### Permissions des fichiers
```bash
find /path/to/DogSchoolResa -type f -exec chmod 644 {} \;
find /path/to/DogSchoolResa -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
```

## Maintenance

### Sauvegarde de la base de données
```bash
# Sauvegarde quotidienne
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
```

### Logs
- **Laravel** : `storage/logs/laravel.log`
- **Erreurs** : `storage/logs/laravel.log`
- **Notifications** : Vérifier les logs SMTP

### Commandes utiles
```bash
# Vérifier l'état de l'application
php artisan about

# Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Vérifier les créneaux qui se ferment
php artisan slots:check-closing

# Tester l'envoi d'emails
php artisan test:email
```

## Résolution de problèmes

### Problèmes de migrations
Si vous rencontrez des erreurs lors des migrations :

1. **Réinitialiser complètement** :
```bash
php artisan migrate:reset
php artisan migrate
```

2. **Vérifier l'état des migrations** :
```bash
php artisan migrate:status
```

3. **Forcer les migrations** :
```bash
php artisan migrate --force
```

### Problèmes de permissions
```bash
# Corriger les permissions
sudo chown -R www-data:www-data /path/to/DogSchoolResa
sudo chmod -R 775 storage bootstrap/cache
```

## Support

Pour toute question ou problème :
- **Email** : cec-condat@yahoo.fr
- **Site web** : https://ceccondat.e-monsite.com/

## Version

**DogSchoolResa V1.0** - Août 2025
