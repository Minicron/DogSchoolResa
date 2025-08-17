# Système de Gestion de Capacité

## Vue d'ensemble

Le système de gestion de capacité permet de contrôler le nombre de participants dans les cours de trois manières différentes :

1. **Aucune limite** : Le cours peut accepter un nombre illimité de participants, sans contrôle ni alerte
2. **Limite fixe** : Le cours a une capacité fixe définie par l'administrateur (ex: 20 participants maximum)
3. **Limite dynamique** : La capacité dépend du nombre de moniteurs inscrits selon la formule : `Nombre de moniteurs × 5`

## Fonctionnalités

### Types de Capacité

#### 1. Aucune limite (`none`)
- Aucune restriction sur le nombre de participants
- Aucun contrôle ni alerte automatique
- Affichage : `∞` dans l'interface
- Idéal pour les cours ouverts à tous

#### 2. Limite fixe (`fixed`)
- Capacité définie manuellement par l'administrateur
- Contrôle strict du nombre de participants
- Exemple : 20 participants maximum
- Idéal pour les cours avec contraintes d'espace ou de matériel

#### 3. Limite dynamique (`dynamic`)
- Capacité calculée automatiquement : `Nombre de moniteurs × 5`
- Calcul en temps réel à chaque affichage ou action
- Aucune configuration manuelle requise
- Exemple : 3 moniteurs = 15 participants maximum
- Idéal pour les cours nécessitant un ratio moniteur/participant

### Calcul Dynamique

#### Capacité Dynamique
- Calcul automatique à chaque affichage : `Nombre de moniteurs × 5`
- Mise à jour en temps réel lors des inscriptions/désinscriptions
- Aucune intervention manuelle requise
- Affichage dynamique dans l'interface utilisateur



## Implémentation Technique

### Modèles Modifiés

#### Slot.php
- Nouveaux champs : `capacity_type`, `min_monitors`
- Méthodes ajoutées :
  - `getCurrentCapacity()` : Calcule la capacité actuelle
  - `hasEnoughMonitors()` : Vérifie si assez de moniteurs
  - `getMonitorCount()` : Compte les moniteurs inscrits
  - `getMonitorsList()` : Liste des moniteurs inscrits

### Services

#### MonitorCapacityService.php
- `checkMonitorCapacity()` : Vérifie la capacité et envoie les alertes
- `getCurrentCapacity()` : Calcule la capacité d'une occurrence
- `canAcceptMoreParticipants()` : Vérifie si plus de participants peuvent s'inscrire

### Notifications

#### InsufficientMonitorsNotification.php
- Notification d'alerte pour les moniteurs insuffisants
- Template d'email personnalisé avec design moderne
- Envoi à tous les administrateurs du club

### Interface Utilisateur

#### Formulaire de Création/Édition
- Sélecteur de type de capacité
- Champs conditionnels selon le type choisi
- Validation appropriée pour chaque type

#### Affichage des Cours
- Affichage de la capacité selon le type
- Alertes visuelles pour les moniteurs insuffisants
- Indication claire du type de capacité

## Utilisation

### Pour les Administrateurs

1. **Créer un cours** :
   - **Aucune limite** : Pour les cours ouverts à tous
   - **Limite fixe** : Définir un nombre maximum de participants
   - **Limite dynamique** : La capacité se calcule automatiquement

2. **Surveiller les cours** :
   - Voir la capacité en temps réel dans l'interface
   - La capacité dynamique s'ajuste automatiquement
   - Aucune intervention manuelle requise

### Pour les Moniteurs

1. **S'inscrire aux cours** :
   - Inscription normale en tant que moniteur
   - Impact automatique sur la capacité si dynamique

2. **Se désinscrire** :
   - Désinscription normale
   - Déclenchement automatique des alertes si nécessaire

## Configuration

### Variables d'Environnement
Aucune configuration supplémentaire requise. Le système utilise la configuration email existante.

### Base de Données
Nouvelles colonnes ajoutées à la table `slots` :
- `capacity_type` : enum ('none', 'fixed', 'dynamic')

## Sécurité

- Validation des données côté serveur
- Vérification des permissions utilisateur
- Logs des actions importantes
- Gestion des erreurs d'envoi d'emails

## Maintenance

### Logs
- Enregistrement des alertes envoyées
- Suivi des erreurs d'envoi d'emails
- Historique des modifications de capacité

### Monitoring
- Surveillance des échecs d'envoi d'emails
- Vérification de l'intégrité des données
- Alertes système en cas de problème

## Évolutions Futures

- Notifications push en temps réel
- Intégration avec des systèmes de messagerie
- Rapports automatisés de capacité
- Système de remplacement automatique de moniteurs
