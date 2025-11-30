# Architecture Technique

## Vue d'ensemble

L'application suit une architecture microservices avec séparation claire entre backend et frontend, orchestrés via Docker Compose.

## Backend Architecture

### Domain-Driven Design (DDD)

Le backend est structuré selon les principes DDD :

```
src/
├── Domain/                    # Couche Domaine (Business Logic)
│   ├── Station/
│   │   ├── Station.php      # Entité domaine
│   │   └── Repository/       # Interface repository
│   ├── Distance/
│   ├── Route/
│   └── Routing/
│       └── Service/
│           └── DijkstraRoutingService.php
├── Infrastructure/            # Couche Infrastructure
│   ├── API/                  # Controllers REST
│   ├── Persistence/          # Doctrine Entities & Repositories
│   └── Security/             # JWT Authentication
└── Command/                   # Console Commands
```

### Flux de données

1. **Requête API** → `RouteController`
2. **Validation** → Symfony Validator
3. **Service Domain** → `DijkstraRoutingService`
4. **Repository** → `RouteRepository` (Doctrine)
5. **Base de données** → PostgreSQL

### Algorithme Dijkstra

L'algorithme de routage implémenté :

1. Construction du graphe à partir des distances
2. Initialisation des distances à l'infini
3. Parcours des nœuds non visités
4. Mise à jour des distances minimales
5. Reconstruction du chemin optimal

**Complexité**: O(V²) où V = nombre de stations

## Frontend Architecture

### Structure Vue.js 3

```
src/
├── views/                     # Pages principales
│   ├── RouteView.vue         # Création de trajets
│   └── StatsView.vue         # Statistiques
├── stores/                    # Pinia stores
│   ├── routeStore.ts
│   └── statsStore.ts
├── services/                  # API clients
│   └── api.ts
└── router/                    # Vue Router
```

### State Management

Utilisation de Pinia pour la gestion d'état :
- `routeStore`: Gestion des trajets
- `statsStore`: Gestion des statistiques

## Base de données

### Schéma

```sql
stations (
    id INTEGER PRIMARY KEY,
    short_name VARCHAR(10) UNIQUE,
    long_name VARCHAR(255)
)

distances (
    id SERIAL PRIMARY KEY,
    parent_station VARCHAR(10),
    child_station VARCHAR(10),
    distance DOUBLE PRECISION,
    network_name VARCHAR(50)
)

routes (
    id VARCHAR(36) PRIMARY KEY,
    from_station_id VARCHAR(10),
    to_station_id VARCHAR(10),
    analytic_code VARCHAR(100),
    distance_km DOUBLE PRECISION,
    path JSON,
    created_at TIMESTAMP
)
```

### Index

- `stations.short_name`: UNIQUE
- `distances.parent_station`: INDEX
- `distances.child_station`: INDEX
- `routes.analytic_code`: INDEX
- `routes.created_at`: INDEX

## Sécurité

### Authentification JWT

1. Client envoie credentials
2. Backend génère JWT token
3. Token inclus dans header `Authorization: Bearer <token>`
4. Middleware valide le token sur chaque requête

### Headers de sécurité

- CORS configuré
- HTTPS ready (via reverse proxy)
- Validation des entrées côté backend

## Docker Architecture

```
┌─────────────────┐
│   Nginx (80)    │ ← Reverse Proxy
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
┌───▼───┐ ┌──▼────┐
│Frontend│ │Backend│
└────────┘ └───┬───┘
               │
        ┌──────▼──────┐
        │ PostgreSQL  │
        └─────────────┘
```

## CI/CD Pipeline

### Étapes

1. **Backend Tests**
   - PHPUnit
   - PHPStan
   - PHPCS

2. **Frontend Tests**
   - Vitest
   - ESLint

3. **Security Scan**
   - Trivy
   - npm audit

4. **Build**
   - Docker images
   - Push to registry

5. **Release**
   - Tagging
   - Changelog

## Performance

### Optimisations

- **Backend**: Opcache activé en production
- **Frontend**: Build optimisé avec Vite
- **Database**: Index sur colonnes fréquemment requêtées
- **Caching**: Doctrine query cache en production

### Scalabilité

- Backend: Stateless, horizontalement scalable
- Frontend: Static files, CDN-ready
- Database: Read replicas possibles

## Monitoring

### Métriques recommandées

- Temps de réponse API
- Taux d'erreur
- Couverture de tests
- Utilisation des ressources Docker

## Déploiement

### Production

1. Variables d'environnement sécurisées
2. Clés JWT générées
3. SSL/TLS configuré
4. Monitoring activé

### Staging

1. Environnement de test
2. Données de test
3. Tests d'intégration



