# Guide de Setup Rapide

## Démarrage rapide

### 1. Prérequis
- Docker 25+ et Docker Compose installés
- Git

### 2. Cloner et démarrer

```bash
git clone <repository-url>
cd defi-fullstack
docker compose up -d
```

### 3. Initialiser la base de données

```bash
# Attendre que PostgreSQL soit prêt (quelques secondes)
docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction

# Charger les données
docker compose exec backend php bin/console app:load-stations
```

### 4. Générer un token JWT (optionnel pour les tests)

Pour tester l'API, vous pouvez utiliser le endpoint `/api/v1/auth/login` avec n'importe quelles credentials (démo).

### 5. Accéder à l'application

- Frontend: http://localhost
- API Docs: http://localhost/api/doc
- API: http://localhost/api/v1

## Commandes utiles

```bash
# Voir les logs
docker compose logs -f

# Arrêter
docker compose down

# Rebuild
docker compose up -d --build

# Accéder au shell backend
docker compose exec backend sh

# Accéder au shell frontend
docker compose exec frontend sh

# Lancer les tests backend
docker compose exec backend composer test

# Lancer les tests frontend
docker compose exec frontend npm run test
```

## Dépannage

### Erreur de connexion à la base de données
Attendre quelques secondes que PostgreSQL démarre complètement.

### Port déjà utilisé
Modifier les ports dans `docker-compose.yml`.

### Erreur de permissions
```bash
sudo chown -R $USER:$USER .
```



