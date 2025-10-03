# Dev Interview

## Project Setup

### Prerequisites

- PHP 8.2+
- Node.js 18+
- Composer

### Clone & Install

```bash
git clone <repository-url>
cd dev-interview

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup (PostgreSQL)
# Create database and update .env with your PostgreSQL credentials
php artisan migrate

# Build frontend assets
npm run build
```

### Development Server

```bash
# Option 1: All services with hot reload
composer dev

# Option 2: Manual setup
php artisan serve                # Backend (http://localhost:8000)
npm run dev                      # Frontend with hot reload
php artisan queue:listen         # Queue worker (optional)
```

### Database Configuration

I used PostgreSQL for development, change the credentials in `.env` for the database connection:

```env
# For MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dev_interview
DB_USERNAME=root
DB_PASSWORD=

# For PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dev_interview
DB_USERNAME=postgres
DB_PASSWORD=
```

Then run migrations:

```bash
php artisan migrate:fresh
```

## Seeding

I use [bgg](https://boardgamegeek.com/) to fetch the images for the board games. There are a few ways to do this:

### Option 1: Limited Seeding with Images (Recommended)

```bash
# Seed 50 games with images (takes ~1 minute)
php artisan boardgames:seed --fresh --limit=50

# Or seed 100 games (takes ~2 minutes)
php artisan boardgames:seed --fresh --limit=100
```

### Option 2: Fast Seeding + Gradual Image Updates

```bash
# Step 1: Seed many games quickly without images
php artisan boardgames:seed --fresh --limit=500 --no-images

# Step 2: Update images in batches
php artisan products:update-images --limit=50
php artisan products:update-images --limit=50
# Repeat as needed...
```

### Option 3: Environment Configuration

Edit your `.env` file:

```env
SEED_GAMES_LIMIT=50        # Number of games to seed
SEED_FETCH_IMAGES=true     # Whether to fetch images during seeding
```

Then run:

```bash
php artisan migrate:fresh --seed
```

## Seeding commands

```bash
# Custom seeding with options
php artisan boardgames:seed --limit=50              # 50 games with images
php artisan boardgames:seed --limit=200 --no-images # 200 games without images
php artisan boardgames:seed --fresh --limit=30      # Fresh DB + 30 games

# Standard Laravel seeding (uses .env config)
php artisan migrate:fresh --seed
php artisan db:seed --class=BoardgamesSeeder
```

## Performance Comparison

| Approach        | Games | Images | Time    | Use Case         |
| --------------- | ----- | ------ | ------- | ---------------- |
| 50 with images  | 50    | 50     | ~1 min  | Development/Demo |
| 100 with images | 100   | 100    | ~2 min  | Small production |
| 500 no images   | 500   | 0      | ~30 sec | Fast setup       |
| 1000 no images  | 1000  | 0      | ~1 min  | Large dataset    |
