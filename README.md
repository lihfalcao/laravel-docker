# Laravel + Docker Setup no Windows

Este repositório contém as instruções para configurar um ambiente de desenvolvimento Laravel utilizando Docker no Windows. O Dockerfile e o docker-compose.yml estão prontos para uso com PHP 8.2, Apache, MySQL e PHPMyAdmin.
## Requisitos

Antes de começar, certifique-se de ter as seguinte ferramenta instalada no seu sistema:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
Através do Docker Desktop mais atualizado teremos todo o necessário para instalar o Docker.

Para o Laravel:
- [PHP](https://windows.php.net/download)
- [Composer](https://getcomposer.org/download/)

## Passos para Instalação

### 1. Clonar o repositório

Primeiro, clone o repositório para sua máquina local:

```bash
git clone https://github.com/lihfalcao/laravel-docker
cd seu-repositorio
```

### 2. Estrutura do Projeto
Certifique-se de que seu diretório de projeto contém a seguinte estrutura:
```bash
.
├── .docker
│   └── vhost.conf        # Arquivo de configuração do Apache
├── example-app
│   └── ...               # Código-fonte do Laravel
├── docker-compose.yml     # Configuração do Docker Compose
└── Dockerfile             # Arquivo Docker para Laravel + Apache
```

### 3. Dockerfile
No arquivo Dockerfile, configuramos a imagem base do PHP com Apache e as dependências necessárias para Laravel:
```bash
# Usar a imagem oficial do PHP com Apache
FROM php:8.2-apache

# Definir o diretório de trabalho
WORKDIR /var/www/html

# Copiar o código-fonte e configuração do Apache
COPY . /var/www/html
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Instalar extensões do PHP necessárias
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Configurar permissões corretas para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configurar nome do servidor e habilitar mod_rewrite
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && a2enmod rewrite

# Expor a porta do Apache
EXPOSE 80
```

### 4. Configuração do Docker Compose
arquivo docker-compose.yml orquestra os serviços:

- Laravel (PHP + Apache)
- MySQL
- PHPMyAdmin

```bash
services:
  laravel:
    build:
      context: /mnt/c/Users/lihfa/Documents/laravel/example-app
    ports:
      - "8081:80"
    networks:
      - example-network
    depends_on:
      - db
    volumes:
      - /mnt/c/Users/lihfa/Documents/laravel/example-app:/var/www/html
      - /mnt/c/Users/lihfa/Documents/laravel/example-app/vendor:/var/www/html/vendor:delegated # Garantir o uso de `delegated` para evitar problemas de sincronização.

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root  
      MYSQL_DATABASE: laravel    
      MYSQL_USER: user             
      MYSQL_PASSWORD: userpassword
    ports:
      - "3307:3306"
    networks:
      - example-network

  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    depends_on:
      - db
    restart: always
    environment:
      PMA_HOST: db
      PMA_PORT: 3306
      PMA_USER: root
      PMA_PASSWORD: root
    ports:
      - "8082:80"
    networks:
      - example-network

networks:
  example-network:
    driver: bridge
```

### 5. Iniciar os Contêineres
Agora você está pronto para rodar o Docker Compose e iniciar os contêineres:

```bash
docker-compose up --build
```

### 6. Acessar os Serviços
- Laravel: http://localhost:8081
- PHPMyAdmin: http://localhost:8082
  - Usuário MySQL: root
  - Senha MySQL: root

### 7. Configuração Final do Laravel
Após a criação dos contêineres, entre no contêiner do Laravel para instalar as dependências:

```bash
docker exec -it seu-repositorio_laravel_1 bash
composer install
```

### 8. Configuração do Banco de Dados
No arquivo .env do Laravel (dentro do contêiner), configure o banco de dados:

```bash
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=user
DB_PASSWORD=userpassword
```

### 9. Finalizar
Agora o Laravel está rodando no Docker, com MySQL e PHPMyAdmin disponíveis. Acesse http://localhost:8081 para ver seu projeto Laravel funcionando.

### Comandos Úteis
```bash
docker exec -it seu-repositorio_laravel_1 bash
composer install
```
