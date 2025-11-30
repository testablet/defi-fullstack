# 🚆 Train Routing System - Full Stack Application

Application complète de routage de trains avec calcul de distances, statistiques analytiques, et interface utilisateur moderne.

## 📋 Table des matières

- [Architecture](#architecture)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Démarrage](#démarrage)
- [API Documentation](#api-documentation)
- [Tests](#tests)
- [CI/CD](#cicd)
- [Structure du projet](#structure-du-projet)

## 🏗️ Architecture

### Backend
- **Framework**: Symfony 7.1 (PHP 8.4)
- **Architecture**: DDD (Domain-Driven Design)
- **Base de données**: PostgreSQL 16
- **ORM**: Doctrine 3
- **API**: REST conforme OpenAPI 3.1
- **Authentification**: JWT (Lexik JWT Bundle)
- **Tests**: PHPUnit 11 avec couverture ≥80%
- **Linting**: PHPCS (PSR-12), PHPStan (niveau 8)

### Frontend
- **Framework**: Vue.js 3 + TypeScript 5
- **UI**: Vuetify 3
- **State Management**: Pinia
- **HTTP Client**: Axios
- **Charts**: Chart.js + vue-chartjs
- **Tests**: Vitest avec couverture ≥80%
- **Linting**: ESLint + Prettier

### Infrastructure
- **Orchestration**: Docker Compose
- **Reverse Proxy**: Nginx
- **CI/CD**: GitHub Actions
- **Security**: Trivy, npm audit, PHPStan

## 📦 Prérequis

- Docker Engine 25+ et Docker Compose
- Git

## 🚀 Installation

1. **Cloner le repository**
   ```bash
   git clone <repository-url>
   cd defi-fullstack
   ```

2. **Configurer les variables d'environnement**
   
   Créer un fichier `.env` à la racine (optionnel, valeurs par défaut disponibles) :
   ```env
   APP_SECRET=your-secret-key-32-chars-minimum
   JWT_PASSPHRASE=your-jwt-passphrase
   ```

3. **Générer les clés JWT** (première fois uniquement)
   ```bash
   docker compose run --rm backend php bin/console lexik:jwt:generate-keypair
   ```

## ▶️ Démarrage

### Démarrage complet avec Docker Compose

```bash
docker compose up -d
```

Cette commande démarre :
- PostgreSQL (port 5432)
- Backend PHP-FPM (port 9000)
- Frontend Nginx (port 80)
- Reverse Proxy Nginx (port 80)

### Accès à l'application

- **Frontend**: http://localhost
- **API**: http://localhost/api/v1
- **API Documentation (Swagger)**: http://localhost/api/doc
- **PostgreSQL**: localhost:5432

### Initialisation de la base de données

```bash
# Exécuter les migrations
docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction

# Charger les stations et distances
docker compose exec backend php bin/console app:load-stations
```

### Génération d'un token JWT pour les tests

```bash
# Créer un utilisateur de test (à implémenter selon vos besoins)
# Pour l'instant, vous pouvez utiliser un token généré manuellement
```

## 📚 API Documentation

### Endpoints

#### POST /api/v1/routes
Crée un nouveau trajet entre deux stations.

**Request Body:**
```json
{
  "fromStationId": "MX",
  "toStationId": "ZW",
  "analyticCode": "ANA-123"
}
```

**Response (201):**
```json
{
  "id": "uuid",
  "fromStationId": "MX",
  "toStationId": "ZW",
  "analyticCode": "ANA-123",
  "distanceKm": 45.67,
  "path": ["MX", "CGE", "VUAR", "...", "ZW"],
  "createdAt": "2025-01-01T12:00:00+00:00"
}
```

#### GET /api/v1/stats/distances
Récupère les statistiques agrégées par code analytique.

**Query Parameters:**
- `from` (optional): Date de début (format: YYYY-MM-DD)
- `to` (optional): Date de fin (format: YYYY-MM-DD)
- `groupBy` (optional): Groupement (none, day, month, year)

**Response (200):**
```json
{
  "from": "2025-01-01",
  "to": "2025-01-31",
  "groupBy": "none",
  "items": [
    {
      "analyticCode": "ANA-123",
      "totalDistanceKm": 150.5,
      "periodStart": "2025-01-01",
      "periodEnd": "2025-01-31"
    }
  ]
}
```

### Authentification

Tous les endpoints (sauf `/api/doc`) nécessitent un token JWT dans le header :
```
Authorization: Bearer <token>
```

## 🧪 Tests

### Backend

```bash
# Lancer les tests
docker compose exec backend composer test

# Avec couverture
docker compose exec backend composer test-coverage

# Linter
docker compose exec backend composer lint

# Analyse statique
docker compose exec backend composer stan
```

### Frontend

```bash
# Lancer les tests
docker compose exec frontend npm run test

# Avec couverture
docker compose exec frontend npm run test:coverage

# Linter
docker compose exec frontend npm run lint
```

## 🔄 CI/CD

Le pipeline GitHub Actions exécute automatiquement :

1. **Tests Backend**
   - PHPUnit avec couverture
   - PHPStan (analyse statique)
   - PHPCS (linting PSR-12)

2. **Tests Frontend**
   - Vitest avec couverture
   - ESLint

3. **Security Scan**
   - Trivy (vulnérabilités)
   - npm audit

4. **Build & Push**
   - Construction des images Docker
   - Push vers GitHub Container Registry

5. **Release**
   - Génération automatique de releases avec changelog

## 📁 Structure du projet

```
defi-fullstack/
├── backend/                 # Backend Symfony
│   ├── config/             # Configuration Symfony
│   ├── migrations/         # Migrations Doctrine
│   ├── src/
│   │   ├── Domain/        # Domain Layer (DDD)
│   │   │   ├── Station/
│   │   │   ├── Distance/
│   │   │   ├── Route/
│   │   │   └── Routing/
│   │   ├── Infrastructure/ # Infrastructure Layer
│   │   │   ├── API/
│   │   │   ├── Persistence/
│   │   │   └── Security/
│   │   └── Command/
│   ├── tests/             # Tests PHPUnit
│   └── Dockerfile
├── frontend/              # Frontend Vue.js
│   ├── src/
│   │   ├── views/         # Pages Vue
│   │   ├── stores/        # Pinia stores
│   │   ├── services/      # API services
│   │   └── router/        # Vue Router
│   ├── tests/             # Tests Vitest
│   └── Dockerfile
├── nginx/                 # Configuration Nginx
├── docker-compose.yml     # Orchestration Docker
├── .github/workflows/     # CI/CD GitHub Actions
└── README.md
```

## 🎯 Fonctionnalités

### Implémentées
- ✅ Calcul de trajet entre deux stations (algorithme Dijkstra)
- ✅ Création de trajets avec code analytique
- ✅ Statistiques agrégées par code analytique
- ✅ Interface utilisateur pour créer des trajets
- ✅ Visualisation des statistiques (graphiques + tableaux)
- ✅ Authentification JWT
- ✅ Tests unitaires et d'intégration (≥80% couverture)
- ✅ CI/CD complet
- ✅ Documentation OpenAPI/Swagger

### Bonus
- ✅ Algorithme Dijkstra pour le routage optimal
- ✅ Endpoint de statistiques avec groupement temporel
- ✅ Visualisation graphique des statistiques
- ✅ Persistance des trajets en base de données

## 🔒 Sécurité

- Authentification JWT pour toutes les routes API
- Headers de sécurité configurés
- Validation des entrées côté backend
- Scan de vulnérabilités dans le pipeline CI/CD
- Gestion des secrets via variables d'environnement

## 📝 Notes de développement

### Algorithme de routage

L'application utilise l'algorithme de Dijkstra pour calculer le chemin le plus court entre deux stations. Le graphe est construit à partir des distances fournies dans `distances.json` et est bidirectionnel (les trains peuvent circuler dans les deux sens).

### Architecture DDD

Le backend suit une architecture Domain-Driven Design :
- **Domain Layer**: Entités métier pures (Station, Route, Distance)
- **Infrastructure Layer**: Implémentations concrètes (Doctrine, API Controllers)
- **Application Layer**: Services applicatifs (DijkstraRoutingService)

## 🤝 Contribution

1. Créer une branche depuis `develop`
2. Implémenter les changements avec tests
3. S'assurer que tous les tests passent et que la couverture ≥80%
4. Créer une Pull Request

## 📄 Licence

MIT

## 👤 Auteur

Développé dans le cadre du défi technique MOB.
