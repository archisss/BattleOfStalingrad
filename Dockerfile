# Using the base Ubuntu 20.04 image
FROM ubuntu:focal

# Environment variables for unattended installation
ENV DEBIAN_FRONTEND=noninteractive
ENV COUCHBASE_HOST=localhost
ENV COUCHBASE_PORT=28017
ENV COUCHBASE_USERNAME=''
ENV COUCHBASE_PASSWORD=''
ENV COUCHBASE_BUCKET=test
ENV COUCHBASE_N1QL_HOST=0.0.0.0

# Updating packages and installing dependencies
RUN apt update \
    && apt install -y \
    wget \
    software-properties-common \
    curl \
    unzip \
    php-pear \
    build-essential \
    zlib1g-dev \
    libssl-dev \
    libevent-dev

# Adding PHP repository
RUN add-apt-repository ppa:ondrej/php \
    && apt update

# Installing PHP 8.2, Nginx, and necessary extensions
RUN apt install -y \
    php8.2 \
    php8.2-cli \
    php8.2-fpm \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-gd \
    php8.2-zip \
    php8.2-pdo-sqlite \
    php8.2-dev \
    nginx \
    gnupg \
    git

# Add CMake APT repository and install CMake
RUN wget -O - https://apt.kitware.com/keys/kitware-archive-latest.asc 2>/dev/null | gpg --dearmor - | tee /etc/apt/trusted.gpg.d/kitware.gpg >/dev/null \
    && apt-add-repository 'deb https://apt.kitware.com/ubuntu/ focal main' \
    && apt update \
    && apt install -y cmake

# Fix PEAR issue
RUN wget http://pear.php.net/go-pear.phar \
    && php go-pear.phar \
    && pecl channel-update pecl.php.net

RUN git config --global http.postBuffer 524288000

# Install MondoDB
RUN pecl install mongodb

#Enable mongodb PHP extension
RUN echo "extension=mongodb.so" >> /etc/php/8.2/cli/php.ini \
    && echo "extension=mongodb.so" >> /etc/php/8.2/fpm/php.ini

# Installing Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installing node 18
RUN apt -qy install dirmngr apt-transport-https lsb-release ca-certificates
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt -qy install nodejs

# Install dependencies for Redis
RUN apt install lsb-release curl gpg

# # Add the Redis repository
RUN curl -fsSL https://packages.redis.io/gpg | gpg --dearmor -o /usr/share/keyrings/redis-archive-keyring.gpg
RUN echo "deb [signed-by=/usr/share/keyrings/redis-archive-keyring.gpg] https://packages.redis.io/deb $(lsb_release -cs) main" | tee /etc/apt/sources.list.d/redis.list
RUN apt update
RUN apt install -y redis

# # Install the Redis PHP extension
RUN pecl install redis

# # Enable the Redis PHP extension
RUN echo "extension=redis.so" >> /etc/php/8.2/cli/php.ini \
    && echo "extension=redis.so" >> /etc/php/8.2/fpm/php.ini

# Configuring PHP-FPM for Laravel
RUN echo "[www]" >> /etc/php/8.2/fpm/pool.d/www.conf \
    && echo "pm = dynamic" >> /etc/php/8.2/fpm/pool.d/www.conf \
    && echo "pm.max_children = 5" >> /etc/php/8.2/fpm/pool.d/www.conf \
    && echo "pm.start_servers = 2" >> /etc/php/8.2/fpm/pool.d/www.conf \
    && echo "pm.min_spare_servers = 1" >> /etc/php/8.2/fpm/pool.d/www.conf \
    && echo "pm.max_spare_servers = 3" >> /etc/php/8.2/fpm/pool.d/www.

# Configuring Nginx
RUN echo "server {" > /etc/nginx/sites-available/default \
    && echo "    listen 80 default_server;" >> /etc/nginx/sites-available/default \
    && echo "    listen [::]:80 default_server;" >> /etc/nginx/sites-available/default \
    && echo "" >> /etc/nginx/sites-available/default \
    && echo "    server_name localhost;" >> /etc/nginx/sites-available/default \
    && echo "    root /var/www/battle-of-stalingrad/public;" >> /etc/nginx/sites-available/default \
    && echo "" >> /etc/nginx/sites-available/default \
    && echo "    index index.php index.html index.htm;" >> /etc/nginx/sites-available/default \
    && echo "" >> /etc/nginx/sites-available/default \
    && echo "    gzip on;" >> /etc/nginx/sites-available/default \
    && echo "    gzip_disable \"msie6\";" >> /etc/nginx/sites-available/default \
    && echo "    gzip_vary on;" >> /etc/nginx/sites-available/default \
    && echo "    gzip_proxied any;" >> /etc/nginx/sites-available/default \
    && echo "    gzip_comp_level 6;" >> /etc/nginx/sites-available/default \
    && echo "    gzip_buffers 16 8k;" >> /etc/nginx/sites-available/default \
    && echo "    gzip_http_version 1.1;" >> /etc/nginx/sites-available/default \
    && echo "    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;" >> /etc/nginx/sites-available/default \
    && echo "" >> /etc/nginx/sites-available/default \
    && echo "    location / {" >> /etc/nginx/sites-available/default \
    && echo "        try_files \$uri \$uri/ /index.php?\$query_string;" >> /etc/nginx/sites-available/default \
    && echo "    }" >> /etc/nginx/sites-available/default \
    && echo "" >> /etc/nginx/sites-available/default \
    && echo "    location ~ \.php\$ {" >> /etc/nginx/sites-available/default \
    && echo "        include snippets/fastcgi-php.conf;" >> /etc/nginx/sites-available/default \
    && echo "        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;" >> /etc/nginx/sites-available/default \
    && echo "        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;" >> /etc/nginx/sites-available/default \
    && echo "        include fastcgi_params;" >> /etc/nginx/sites-available/default \
    && echo "    }" >> /etc/nginx/sites-available/default \
    && echo "" >> /etc/nginx/sites-available/default \
    && echo "    error_log  /var/log/nginx/error.log;" >> /etc/nginx/sites-available/default \
    && echo "    access_log /var/log/nginx/access.log;" >> /etc/nginx/sites-available/default \
    && echo "}" >> /etc/nginx/sites-available/default

# Create nonexistent directories
RUN mkdir -p /nonexistent

# Create the bash script to set up the project
RUN echo '#!/bin/bash' > /var/www/html/setup_project.sh \
    && echo 'redis-server' >> /var/www/html/setup_project.sh \
    && echo 'if [ -f /var/www/battle-of-stalingrad/.env ]; then' >> /var/www/html/setup_project.sh \
    && echo '    echo "The container has already been configured correctly."' >> /var/www/html/setup_project.sh \
    && echo '    exit 0' >> /var/www/html/setup_project.sh \
    && echo 'fi' >> /var/www/html/setup_project.sh \
    && echo 'cd /var/www/battle-of-stalingrad' >> /var/www/html/setup_project.sh \
    && echo 'composer install' >> /var/www/html/setup_project.sh \
    && echo 'npm install' >> /var/www/html/setup_project.sh \
    && echo 'php artisan key:generate' >> /var/www/html/setup_project.sh \
    && echo 'echo "Setup completed."' >> /var/www/html/setup_project.sh \
    && chmod +x /var/www/html/setup_project.sh

# Exposing ports
EXPOSE 80 3306 28017

# Init services
CMD service php8.2-fpm start && nginx -g 'daemon off;' & /var/www/html/setup_project.sh && wait


# Steps to complete the installation:

# 1. Start Docker Desktop.
# 2. Open a console in the application directory.
# 3. Run the command `docker-compose up -d` and wait for the script to finish.
# Once you reach this point, your application will be available at `localhost:8080`.
# If you want to access the database remotely, you can do so using the host `localhost`, port `8091`, username `development`, and password `secret`.
