# Guide de déploiement sur serveur multi-projets

Ce guide explique comment déployer le projet Train Routing System sur un serveur qui contient déjà d'autres projets, accessible via `https://www.amcnumerique.fr/projets/train-routing-system`.

## Prérequis

- Serveur avec Nginx déjà configuré
- Docker et Docker Compose installés
- Accès SSH au serveur
- Domaine configuré : `www.amcnumerique.fr`

## 1. Préparation du serveur

```bash
# Créer le répertoire du projet
sudo mkdir -p /opt/train-routing
cd /opt/train-routing

# Cloner ou transférer les fichiers du projet
git clone <votre-repo> .
# OU
scp -r defi-fullstack/* user@serveur:/opt/train-routing/
```

## 2. Configuration des ports

Éditer `docker-compose.yml` pour utiliser des ports non-conflictuels :

```yaml
services:
  backend:
    ports:
      - "8001:8000"  # Port externe 8001 pour éviter les conflits
    # ... reste de la config

  frontend:
    ports:
      - "5174:5173"  # Port externe 5174 pour éviter les conflits
    # ... reste de la config
```

## 3. Configuration Nginx sur le serveur

### Option A : Configuration dans le fichier principal

Éditer `/etc/nginx/sites-available/amcnumerique.fr` (ou votre fichier de config) :

```nginx
server {
    listen 443 ssl http2;
    server_name www.amcnumerique.fr;

    # ... autres configurations existantes ...

    # Inclure la configuration du projet Train Routing
    include /opt/train-routing/nginx/nginx.production.conf;
}
```

### Option B : Configuration manuelle

Ajouter directement dans votre configuration Nginx :

```nginx
# Upstream pour Train Routing System
upstream train_routing_backend {
    server 127.0.0.1:8001;
    keepalive 32;
}

upstream train_routing_frontend {
    server 127.0.0.1:5174;
    keepalive 32;
}

# Frontend
location /projets/train-routing-system {
    rewrite ^/projets/train-routing-system$ /projets/train-routing-system/ permanent;
    proxy_pass http://train_routing_frontend/;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header X-Forwarded-Prefix /projets/train-routing-system;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
}

# API Backend
location /projets/train-routing-system/api {
    rewrite ^/projets/train-routing-system/api(.*)$ /api$1 break;
    proxy_pass http://train_routing_backend;
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header X-Forwarded-Prefix /projets/train-routing-system;
}
```

## 4. Variables d'environnement

Créer un fichier `.env` à la racine :

```bash
# Backend
APP_ENV=prod
APP_SECRET=votre-secret-32-caracteres-minimum
DATABASE_URL=postgresql://user:password@postgres:5432/train_routing?serverVersion=16

# JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=votre-passphrase-jwt

# Frontend (optionnel, pour override)
VITE_API_BASE_URL=/projets/train-routing-system/api/v1
```

## 5. Build et démarrage

```bash
cd /opt/train-routing

# Build des images
docker compose build

# Démarrer les services
docker compose up -d

# Initialiser la base de données
docker compose exec backend php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec backend php bin/console app:load-stations

# Générer les clés JWT (si pas déjà fait)
docker compose exec backend php bin/console lexik:jwt:generate-keypair
```

## 6. Vérification Nginx

```bash
# Tester la configuration
sudo nginx -t

# Recharger Nginx
sudo systemctl reload nginx
```

## 7. Vérification

- Frontend : `https://www.amcnumerique.fr/projets/train-routing-system`
- API : `https://www.amcnumerique.fr/projets/train-routing-system/api/v1`
- Documentation API : `https://www.amcnumerique.fr/projets/train-routing-system/api/doc`

## 8. Maintenance

```bash
# Voir les logs
docker compose logs -f

# Redémarrer un service
docker compose restart backend

# Mettre à jour
git pull
docker compose build
docker compose up -d

# Backup base de données
docker compose exec postgres pg_dump -U train_user train_routing > backup_$(date +%Y%m%d).sql
```

## Notes importantes

1. **Ports** : Assurez-vous que les ports 8001 et 5174 ne sont pas utilisés par d'autres projets
2. **Base path** : Le projet est configuré pour fonctionner avec le base path `/projets/train-routing-system/`
3. **Assets** : Les assets sont servis avec le bon chemin grâce à la configuration Vite
4. **API** : L'API est accessible via `/projets/train-routing-system/api/v1`
5. **Sécurité** : Changez tous les secrets dans le fichier `.env`

