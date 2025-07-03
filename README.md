# Symfony Docker Development Environment

A complete Docker-based development environment for Symfony applications with all essential services.

### Core Application
- **PHP/Symfony** - Main application container with PHP-FPM
- **Caddy** - Web server and reverse proxy
- **MySQL** - Primary database
- **Redis** - Caching and session storage

### Development Tools
- **Mailhog** - Email testing service with web interface
- **Webpack Encore** - Asset compilation and management

## 🚀 Quick Start

1. **Clone the repository**
   ```bash
   git clone git@github.com:Shweit/symfony-webapp-docker.git
   cd symfony-webapp-docker
   ```

2. **Start the Docker containers**
   ```bash
   docker-compose up -d
   ```

3. **Install PHP dependencies**
   ```bash
   docker-compose exec php composer install
   ```

4. **Access the application**
   - Main application: http://localhost
   - Mailhog interface: http://localhost:8025
   - Encore dev server: http://localhost:8080

## 🔧 Container Details

### PHP Container
- **Image**: Custom PHP with Symfony optimizations
- **Port**: Internal (via Caddy)
- **Services**: PHP-FPM, Composer, Symfony CLI

### Caddy Web Server
- **Port**: 80 (HTTP), 443 (HTTPS)
- **Features**: Automatic HTTPS, reverse proxy
- **Config**: `docker/caddy/Caddyfile`

### MySQL Database
- **Port**: 3306
- **Database**: `app`
- **User**: `app`
- **Password**: `password`

### Redis Cache
- **Port**: 6379
- **Purpose**: Session storage, application cache
- **Persistence**: In-memory (development)

### Mailhog SMTP
- **SMTP Port**: 1025
- **Web Interface**: 8025
- **Purpose**: Email testing and debugging

### Encore Container
- **Port**: 8080
- **Purpose**: Automatic asset compilation with hot reload
- **Features**: Watches files and rebuilds assets automatically

## 📁 Project Structure

```
symfony-webapp-docker/
├── docker/
│   ├── caddy/
│   │   └── Caddyfile          # Web server configuration
│   └── php/
│       └── Dockerfile         # PHP container setup
├── src/
│   └── Controller/
│       └── HomeController.php # Health dashboard controller
├── templates/
│   └── home/
│       └── index.html.twig    # Dashboard template
├── assets/
│   ├── controllers/
│   │   └── home_controller.js # Stimulus controller
│   ├── styles/
│   │   └── app.css           # Tailwind CSS
│   └── app.js                # Main JavaScript entry
├── config/
├── public/
├── compose.yaml              # Docker services definition
└── compose.override.yaml     # Development overrides
```

## 🔍 Service URLs

| Service | URL | Purpose |
|---------|-----|---------|
| Application | http://localhost | Main Symfony app |
| Mailhog UI | http://localhost:8025 | Email testing interface |
| Encore Dev Server | http://localhost:8080 | Asset development server |
| Database | localhost:3306 | MySQL connection |
| Redis | localhost:6379 | Cache connection |

## 🔒 Environment Variables

Key environment variables in `.env`:

```bash
# Database
DATABASE_URL="mysql://app:password@database:3306/app?serverVersion=8.0&charset=utf8mb4"

# Redis (if configured)
REDIS_URL=redis://localhost:6379

# Mailer
MAILER_DSN=smtp://mailhog:1025
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test with the health dashboard
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

---

**Built with ❤️ using Symfony + Docker + Tailwind CSS + Stimulus**

🚀 Ready for development in Docker containers!
>5551257 (Create Symfony docker Template)
