FROM node:20-alpine AS builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .

# Build argument pour le base path
ARG VITE_API_BASE_URL=/projets/train-routing-system/api/v1
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL

# Build avec le base path
ENV NODE_ENV=production
RUN npm run build

FROM nginx:alpine
COPY --from=builder /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]



