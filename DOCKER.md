# Docker Configuration

## Building Image

```bash
docker-compose build
```

### Create Network
```bash
docker network create financeher-main-network --subnet 192.168.55.0/24
```

### Run the container
```bash
docker-compose up -d
```

### Run Laravel migrations (if needed)
```bash
docker-compose exec financeher-backend php artisan migrate
```

### Generate application key (if needed)
```bash
docker-compose exec financeher-backend php artisan key:generate
docker-compose exec financeher-backend php artisan jwt:secret
```
