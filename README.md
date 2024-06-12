# Bookings Management API

This is a Laravel-based API for managing room bookings, customers, and payments. The API includes features such as room availability checking to prevent double bookings and token-based authentication using Laravel Sanctum.

## Table of Contents
- [Installation](#installation)
- [API Endpoints](#api-endpoints)
- [Testing](#testing)


## Installation

1. **Clone the repository:**
    ```sh
    git clone https://github.com/MilenPlamenov/laravel_hotel_bookings_api.git
    cd booking
    ```

2. **Install dependencies:**
    ```sh
    composer install
    ```

3. **Copy the example environment file and configure it:**
    ```sh
    cp .env.example .env
    ```
    Edit the `.env` file to match your database and other configurations.

4. **Generate application key:**
    ```sh
    php artisan key:generate
    ```

5. **Run the migrations and seed the database:**
    ```sh
    php artisan migrate --seed
    ```

6. **Install Sanctum:**
    ```sh
    php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
    php artisan migrate
    ```

7. **Run the application:**
    ```sh
    php artisan serve
    ```

## API Endpoints

### Room Management
- **Get all rooms:**
    ```sh
    GET /api/rooms
    ```
- **Create a room:**
    ```sh
    POST /api/rooms
    ```
- **Get a single room:**
    ```sh
    GET /api/rooms/{id}
    ```
- **Update a room:**
    ```sh
    PUT /api/rooms/{id}
    ```
- **Delete a room:**
    ```sh
    DELETE /api/rooms/{id}
    ```

### Booking Management
- **Get all bookings:**
    ```sh
    GET /api/bookings
    ```
- **Create a booking:**
    ```sh
    POST /api/bookings
    ```
- **Get a single booking:**
    ```sh
    GET /api/bookings/{id}
    ```
- **Update a booking:**
    ```sh
    PUT /api/bookings/{id}
    ```
- **Delete a booking:**
    ```sh
    DELETE /api/bookings/{id}
    ```

### Payment Management
- **Get all payments:**
    ```sh
    GET /api/payments
    ```
- **Create a payment:**
    ```sh
    POST /api/payments
    ```
- **Get a single payment:**
    ```sh
    GET /api/payments/{id}
    ```
- **Update a payment:**
    ```sh
    PUT /api/payments/{id}
    ```
- **Delete a payment:**
    ```sh
    DELETE /api/payments/{id}
    ```

### Auth Management
- **Register:**
    ```sh
    POST /api/register
    ```
- **Login:**
    ```sh
    POST /api/login
    ```
- **Logout:**
    ```sh
    POST /api/logout
    ```

## Testing
- **Unit tests for all controllers (Auth, Booking, Room and Payment)**
    ```sh
    php artisan test
    ```