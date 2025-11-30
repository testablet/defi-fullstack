# 🚀 Quick Start Guide

## Démarrage en 3 étapes

### 1. Démarrer les services
```bash
docker compose up -d
```

### 2. Initialiser la base de données
```bash
# Migrations
docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction

# Charger les données
docker compose exec backend php bin/console app:load-stations
```

### 3. Accéder à l'application
- **Frontend**: http://localhost
- **API Docs**: http://localhost/api/doc

## Test rapide de l'API

### 1. Obtenir un token (démo)
```bash
curl -X POST http://localhost/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username": "admin", "password": "password"}'
```

### 2. Créer un trajet
```bash
curl -X POST http://localhost/api/v1/routes \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "fromStationId": "MX",
    "toStationId": "ZW",
    "analyticCode": "ANA-123"
  }'
```

### 3. Voir les statistiques
```bash
curl http://localhost/api/v1/stats/distances \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Structure du projet

```
defi-fullstack/
├── backend/          # Symfony 7.1 + PHP 8.4
├── frontend/         # Vue 3 + TypeScript + Vuetify
├── nginx/            # Configuration reverse proxy
└── docker-compose.yml
```

## Commandes utiles

```bash
# Logs
docker compose logs -f

# Tests backend
docker compose exec backend composer test

# Tests frontend  
docker compose exec frontend npm run test

# Arrêter
docker compose down
```

## Problèmes courants

**Port 80 déjà utilisé**: Modifier le port dans `docker-compose.yml`

**Erreur de connexion DB**: Attendre quelques secondes que PostgreSQL démarre

**Token JWT manquant**: Utiliser `/api/v1/auth/login` pour obtenir un token de démo



