# Système de Clôture des Inscriptions

## Vue d'ensemble

Le système de clôture des inscriptions permet de fermer automatiquement les inscriptions pour les cours selon un délai configuré. Il comprend :

1. **Vérification automatique** : Une commande Artisan qui s'exécute toutes les 5 minutes
2. **Notifications aux admins** : Rapport détaillé envoyé par email lors de la clôture
3. **Prévention des inscriptions** : Blocage des nouvelles inscriptions une fois closes
4. **Suivi des notifications** : Évite les doublons grâce à un flag dans la base de données

## Configuration

### 1. Paramètres du cours

Chaque cours peut avoir les paramètres suivants :
- `auto_close` : Boolean - Active la clôture automatique
- `close_duration` : Integer - Délai en heures avant le cours

### 2. Configuration du Cron Job

Ajoutez cette ligne dans votre crontab (`crontab -e`) :

```bash
*/5 * * * * cd /path/to/your/project && php artisan slots:check-closing >> /dev/null 2>&1
```

Ou utilisez le scheduler Laravel dans `app/Console/Kernel.php` :

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('slots:check-closing')->everyFiveMinutes();
}
```

## Fonctionnalités

### Vérification de clôture

La commande `slots:check-closing` :

1. **Récupère** les occurrences futures avec clôture automatique
2. **Vérifie** si la date de clôture est atteinte
3. **Envoie** les notifications aux admins du club
4. **Marque** l'occurrence comme notifiée

### Logique de clôture

```php
// Date de clôture = Date du cours - Délai configuré
$closingDate = $occurrence->date->subHours($slot->close_duration);

// Inscriptions closes si maintenant >= date de clôture
$isClosed = now()->gte($closingDate);
```

### Prévention des inscriptions

Les méthodes d'inscription vérifient automatiquement :
- `register()` : Inscription comme participant
- `registerAsMonitor()` : Inscription comme moniteur

Si les inscriptions sont closes, l'utilisateur reçoit un message d'erreur.

## Notifications

### Contenu du rapport

Le rapport envoyé aux admins contient :

- **Informations du cours** : Nom, date, heure, lieu
- **Statistiques** : Nombre de participants et moniteurs
- **Listes détaillées** : Noms et emails des inscrits
- **Alertes** : Si aucun participant ou moniteur

### Destinataires

Les notifications sont envoyées aux utilisateurs avec les rôles :
- `admin`
- `admin-club`
- `super_admin`

## Modèles et Base de Données

### Nouveau champ

`slot_occurences.closing_notification_sent` : Boolean
- `false` : Notification pas encore envoyée
- `true` : Notification déjà envoyée

### Méthodes utiles

#### SlotOccurence

```php
// Vérifier si les inscriptions sont closes
$occurrence->isRegistrationClosed()

// Obtenir la date de clôture
$occurrence->getClosingDate()

// Obtenir le statut d'inscription
$occurrence->getRegistrationStatus()

// Vérifier si un utilisateur peut s'inscrire
$occurrence->canUserRegister($userId)

// Obtenir les statistiques pour le rapport
$occurrence->getClosingStats()
```

## Utilisation dans les vues

### Affichage du statut

```php
$status = $occurrence->getRegistrationStatus();
```

```html
<span class="{{ $status['class'] }}">
    {{ $status['message'] }}
</span>
```

### Boutons d'inscription

```php
@if($occurrence->canUserRegister(auth()->id()))
    <!-- Afficher le bouton d'inscription -->
@else
    <!-- Afficher un message d'erreur -->
@endif
```

## Commandes Artisan

### Vérification manuelle

```bash
php artisan slots:check-closing
```

### Test avec données

```bash
# Créer une occurrence de test
php artisan tinker
$occurrence = SlotOccurence::first();
$occurrence->update(['date' => now()->addHours(2)); // Cours dans 2h
$slot = $occurrence->slot;
$slot->update(['auto_close' => true, 'close_duration' => 3]); // Clôture dans 3h

# Tester la commande
php artisan slots:check-closing
```

## Dépannage

### Problèmes courants

1. **Notifications non envoyées**
   - Vérifier la configuration email dans `.env`
   - Vérifier les logs Laravel

2. **Cron job ne s'exécute pas**
   - Vérifier les permissions du fichier
   - Vérifier le chemin dans le crontab
   - Tester manuellement la commande

3. **Inscriptions toujours possibles**
   - Vérifier que `auto_close` et `close_duration` sont configurés
   - Vérifier la date du cours

### Logs

Les erreurs sont loggées dans `storage/logs/laravel.log` avec le contexte :
- ID de l'admin
- ID de l'occurrence
- Message d'erreur

## Sécurité

- Les notifications ne sont envoyées qu'une seule fois par occurrence
- Seuls les admins du club concerné reçoivent les notifications
- Les inscriptions sont vérifiées côté serveur et côté client
