<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

# OWASP Security Application

This project is a **Laravel-based web application** developed as part of my **Master Thesis** at **Subotica Tech**. It showcases and addresses various **OWASP Security** principles.

## About the Application

This Laravel application is designed to:
- Demonstrate the implementation of **OWASP Top 10 Security Best Practices**.
- Provide a secure foundation for modern web applications.
- Serve as an educational tool for secure application design.

## Getting Started

Follow the steps below to set up and run the application:

### Prerequisites

Ensure you have the following installed:
- PHP 8.2
- Composer
- MySQL
- Node.js and npm

### Installation Steps2

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/owasp-security.git
   cd owasp-security
   
2. Install Dependencies:
- composer install
- npm install

3. Set up your environment file
    ```bash
    cp .env.example .env

Update the .env file with your database credentials:

    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=owasp_security
    DB_USERNAME=your_username
    DB_PASSWORD=your_password23443

4. Generate the application key
    ```bash
    php artisan key:generate

5. Run database migrations and seeders:
    ```bash
   php artisan migrate --seed

6. Start the development server:
    ```bash
   php artisan serve
   Visit http://127.0.0.1:8000 to access the application. 
