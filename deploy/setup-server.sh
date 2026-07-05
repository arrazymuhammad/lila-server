#!/bin/bash
# TASK-356 & TASK-365: Production server setup script
# Run as root or with sudo on fresh Ubuntu 22.04+

set -euo pipefail

echo "=== LILA Production Server Setup ==="

# Update system
apt-get update && apt-get upgrade -y -q

# Install required packages
apt-get install -y -q nginx php8.3-fpm php8.3-mbstring php8.3-xml php8.3-curl \
    php8.3-sqlite3 php8.3-gd php8.3-intl php8.3-bcmath php8.3-apcu \
    certbot python3-certbot-nginx unzip git supervisor

# Firewall (TASK-365)
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp    # SSH
ufw allow 80/tcp    # HTTP
ufw allow 443/tcp   # HTTPS
echo "y" | ufw enable

# PHP OPcache optimal settings (TASK-356)
cat > /etc/php/8.3/fpm/conf.d/99-lila-opcache.ini << 'EOF'
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.jit=1255
opcache.jit_buffer_size=64M
EOF

# PHP-FPM tune (TASK-368)
cat > /etc/php/8.3/fpm/pool.d/www.conf << 'EOF'
[www]
pm = dynamic
pm.max_children = 10
pm.start_servers = 3
pm.min_spare_servers = 2
pm.max_spare_servers = 5
pm.max_requests = 500
EOF

systemctl restart php8.3-fpm

# Supervisor for queue worker (TASK-334)
cat > /etc/supervisor/conf.d/lila-worker.conf << 'EOF'
[program:lila-worker]
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
directory=/var/www/html
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
stopwaitsecs=3600
EOF

supervisorctl reread && supervisorctl update

echo "=== Server setup complete ==="
echo "Next: sudo certbot --nginx -d yourdomain.com"
