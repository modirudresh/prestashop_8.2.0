# PrestaShop 8.2 Project

This repository contains the complete source code for a PrestaShop 8.2 e-commerce store.

## Requirements

- PHP 7.4 or higher  
- MySQL 5.6+ or MariaDB  
- Apache or Nginx  
- Composer  

## Installation

```bash
git clone https://github.com/yourusername/prestashop8.2-project.git
cd prestashop8.2-project
composer install
chmod -R 755 var vendor modules themes img upload
cp .env.example .env
# Edit .env to configure database credentials
