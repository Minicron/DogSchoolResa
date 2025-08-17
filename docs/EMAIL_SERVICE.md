# Service Email - DogSchoolResa

## Vue d'ensemble

Le service d'email de DogSchoolResa gère l'envoi de tous les emails de l'application, incluant les notifications automatiques et les emails personnalisés.

## Configuration

### Variables d'Environnement Requises

```env
# Configuration Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@cec-condat.fr
MAIL_FROM_NAME="CEC Condat"
```

### Configuration Gmail

Pour Gmail, vous devez :
1. Activer l'authentification à 2 facteurs
2. Créer un mot de passe d'application pour "Mail"

## Test d'Email

### Commande de Test

```bash
# Test simple
php artisan app:test-email votre@email.com

# Test avec type spécifique
php artisan app:test-email votre@email.com --type=welcome

# Test personnalisé
php artisan app:test-email votre@email.com --type=custom --message="Votre message personnalisé"
```

### Types de Tests Disponibles

- `simple` : Email texte simple
- `welcome` : Email de bienvenue
- `invitation` : Email d'invitation
- `registration` : Email d'inscription à un cours
- `cancellation` : Email d'annulation de cours
- `reminder` : Email de rappel
- `announcement` : Email d'annonce du club
- `custom` : Email personnalisé

## Utilisation dans le Code

### Injection de Dépendance

```php
use App\Services\EmailService;

class MonController extends Controller
{
    public function __construct(private EmailService $emailService)
    {
        //
    }

    public function envoyerEmail()
    {
        $success = $this->emailService->sendWelcomeEmail($user, $club);
        
        if ($success) {
            return response()->json(['message' => 'Email envoyé']);
        }
        
        return response()->json(['error' => 'Erreur d\'envoi'], 500);
    }
}
```

### Méthodes Disponibles

#### `sendWelcomeEmail(User $user, Club $club)`
Envoie un email de bienvenue à un nouvel utilisateur.

#### `sendUserInvitation(User $user, string $token, Club $club)`
Envoie une invitation à rejoindre le club.

#### `sendRegistrationNotification(User $user, SlotOccurence $slotOccurence)`
Confirme l'inscription à un cours.

#### `sendUnregistrationNotification(User $user, SlotOccurence $slotOccurence)`
Confirme la désinscription d'un cours.

#### `sendCancellationNotification(SlotOccurence $slotOccurence, string $reason)`
Notifie de l'annulation d'un cours.

#### `sendReminderNotification(SlotOccurence $slotOccurence)`
Envoie un rappel pour un cours.

#### `sendMonitorAssignmentNotification(User $monitor, SlotOccurence $slotOccurence)`
Notifie un moniteur de son assignation.

#### `sendClubAnnouncement(Club $club, string $subject, string $message)`
Envoie une annonce à tous les membres du club.

#### `sendCustomEmail(User $user, string $subject, string $message, array $data = [])`
Envoie un email personnalisé.

## Dépannage

### Problèmes Courants

1. **Email non reçu** : Vérifiez vos spams
2. **Erreur d'authentification** : Vérifiez le mot de passe d'application Gmail
3. **Erreur de connexion** : Vérifiez les paramètres SMTP

### Logs

Les erreurs sont loggées dans `storage/logs/laravel.log` :

```bash
# Voir les logs d'email
grep -i "email\|mail" storage/logs/laravel.log

# Voir les logs en temps réel
tail -f storage/logs/laravel.log
```

### Test de Configuration

```bash
# Voir la configuration actuelle
php artisan config:show mail

# Nettoyer le cache
php artisan config:clear
```

## Templates d'Email

L'application utilise deux templates d'email :

### Template de Test (`emails/test.blade.php`)
- Template simple pour les tests et diagnostics
- Utilisé par les commandes de test
- Design épuré avec les couleurs du club

### Template Personnalisé (`emails/custom.blade.php`)
- Template complet pour les emails de production
- Inclut en-tête avec logo du club
- Contenu personnalisable
- Bouton d'action optionnel
- Pied de page avec informations de contact

## Sécurité

- Les emails sont envoyés de manière asynchrone via les queues
- Les adresses email sont validées avant envoi
- Les erreurs sont loggées sans exposer d'informations sensibles
