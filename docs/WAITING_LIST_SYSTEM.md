# Système de Liste d'Attente

## Vue d'ensemble

Le système de liste d'attente permet de gérer automatiquement les inscriptions aux cours complets. Quand un cours est plein, les nouveaux inscrits sont automatiquement placés en liste d'attente et notifiés par email.

## Fonctionnalités

### Gestion Automatique
- **Détection automatique** : Le système détecte si un cours est plein selon le type de capacité
- **Liste d'attente automatique** : Les nouveaux inscrits sont automatiquement ajoutés en liste d'attente
- **Promotion automatique** : Les personnes en liste d'attente sont automatiquement inscrites quand une place se libère

### Types de Capacité Supportés

#### 1. Capacité Fixe
- Limite définie manuellement par l'administrateur
- Liste d'attente activée quand le nombre de participants atteint la limite

#### 2. Capacité Dynamique
- Limite calculée selon `Nombre de moniteurs × 5`
- Liste d'attente activée quand le nombre de participants atteint la capacité dynamique
- **Promotion automatique** quand un moniteur s'inscrit (augmentation de la capacité)

#### 3. Aucune Limite
- Pas de liste d'attente (inscriptions illimitées)

### Notifications Automatiques

#### Notification d'Ajout en Liste d'Attente
- **Déclenchement** : Quand un utilisateur s'inscrit à un cours complet
- **Contenu** :
  - Détails du cours (nom, date, heure, lieu)
  - Explication du processus de liste d'attente
  - Information sur la notification automatique

#### Notification de Place Disponible
- **Déclenchement** : Quand une place se libère et qu'un utilisateur est promu
- **Contenu** :
  - Confirmation d'inscription automatique
  - Détails du cours (nom, date, heure, lieu)
  - Message de bienvenue

## Implémentation Technique

### Modèles

#### WaitingList.php
- **Relations** : `user()`, `slotOccurrence()`, `slot()`
- **Méthodes utilitaires** :
  - `isUserWaiting()` : Vérifier si un utilisateur est en liste d'attente
  - `getNextWaitingUser()` : Obtenir le prochain utilisateur en attente
  - `getWaitingUsers()` : Obtenir tous les utilisateurs en attente
  - `markAsNotified()` : Marquer comme notifié

#### SlotOccurence.php
- **Nouvelles méthodes** :
  - `isFull()` : Vérifier si le cours est plein
  - `hasAvailableSpots()` : Vérifier s'il y a de la place
  - `getAvailableSpots()` : Obtenir le nombre de places disponibles
  - `waitingList()` : Relation avec la liste d'attente

### Services

#### WaitingListService.php
- **addToWaitingList()** : Ajouter un utilisateur en liste d'attente
- **processWaitingList()** : Traiter la liste d'attente quand une place se libère
- **checkAndProcessWaitingList()** : Vérifier et traiter automatiquement
- **getWaitingListCount()** : Obtenir le nombre d'utilisateurs en attente
- **isUserWaiting()** : Vérifier si un utilisateur est en attente

### Notifications

#### WaitingListNotification.php
- Notification envoyée quand un utilisateur est ajouté en liste d'attente
- Template d'email avec détails du cours et explications

#### PlaceAvailableNotification.php
- Notification envoyée quand un utilisateur est promu de la liste d'attente
- Template d'email avec confirmation d'inscription

## Utilisation

### Pour les Utilisateurs

1. **Inscription à un cours complet** :
   - L'utilisateur clique sur "S'inscrire"
   - Si le cours est plein, il est automatiquement ajouté en liste d'attente
   - Il reçoit un email de confirmation

2. **Promotion automatique** :
   - Quand une place se libère, l'utilisateur est automatiquement inscrit
   - Il reçoit un email de notification
   - Aucune action requise de sa part

### Pour les Administrateurs

1. **Surveillance** :
   - Voir le nombre de personnes en liste d'attente dans l'interface
   - Accéder à la liste complète via la modal des participants

2. **Gestion** :
   - Le système fonctionne automatiquement
   - Aucune intervention manuelle requise

## Base de Données

### Table `waiting_lists`
- `id` : Identifiant unique
- `user_id` : Référence vers l'utilisateur
- `slot_occurence_id` : Référence vers l'occurrence de cours
- `joined_at` : Date d'ajout en liste d'attente
- `is_notified` : Statut de notification
- `notified_at` : Date de notification
- `created_at`, `updated_at` : Timestamps

### Index et Contraintes
- Index sur `slot_occurence_id` et `joined_at` pour optimiser les requêtes
- Contrainte unique sur `user_id` et `slot_occurence_id` (un utilisateur ne peut être qu'une fois en liste d'attente pour une occurrence)

## Logs et Monitoring

### Logs Automatiques
- Ajout en liste d'attente
- Promotion de la liste d'attente
- Erreurs lors du traitement

### Surveillance
- Nombre d'utilisateurs en liste d'attente par cours
- Taux de promotion (efficacité du système)
- Temps moyen d'attente

## Sécurité

### Validation
- Vérification que l'utilisateur n'est pas déjà inscrit
- Vérification que l'utilisateur n'est pas déjà en liste d'attente
- Contraintes de base de données

### Gestion d'Erreurs
- Logs détaillés des erreurs
- Fallbacks en cas de problème
- Notifications d'erreur appropriées

## Évolutions Futures

### Fonctionnalités Avancées
- **Liste d'attente prioritaire** : Priorité selon différents critères
- **Notifications push** : Notifications en temps réel
- **Gestion des désistements** : Permettre aux utilisateurs de se retirer de la liste d'attente
- **Statistiques avancées** : Rapports détaillés sur l'utilisation

### Intégrations
- **SMS** : Notifications par SMS en plus des emails
- **API** : Interface pour applications tierces
- **Webhooks** : Notifications vers d'autres systèmes
