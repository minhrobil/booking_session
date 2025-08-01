# 🏋️‍♂️ Sports Session Booking Backend (Symfony)

This is the backend service for a **sports session booking system**, built with **Symfony** and powered by **Docker**. It provides RESTful APIs for managing sessions, and bookings.

---

## 📦 Requirements

- [PHP ^8.1](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/install/)

---

## 🚀 Getting Started

Follow these steps to set up and run the project locally:

### 1. Configure environment variables
From root, folder booking_session, go to backend folder:

```bash
cd backend
```

Copy the development environment file:

```bash
cp .env.dev .env
```
### 2. Install dependencies

Install PHP packages via Composer:
```bash
composer install
```
### 3. Start Docker environment

```bash
docker compose up --build
```
This will starts and installs database

### 4. Init data

Create db schema
```bash
php bin/console doctrine:migrations:migrate
```
Init some config data:

```bash
php bin/console doctrine:fixtures:load -n
```

### 5. Start the application
Run the Symfony server on port 8080:

```bash
symfony server:start --port=8080 --listen-ip=0.0.0.0 --allow-http --allow-cors
```
This will starts and installs database

## 🌐 API Access
Once the app is running, the backend will be accessible at: 
http://127.0.0.1:8080

### 📌 Available Endpoints

| Method | Endpoint               | Description                           |
|--------|------------------------|---------------------------------------|
| GET    | `/api/sessions`        | Retrieve a list of sessions           |
| POST   | `/api/bookings`        | Book time slots for a session         |

## 🧩 Notes
- Make sure Docker and Docker Compose are properly installed and currently running.
- If you encounter migration errors, check that the database container is fully initialized and reachable.
- Database configurations can be adjusted directly in the .env file to suit your local setup.
- If you’re using Postman, you can import the two available APIs using the collection file located at Document/Booking.postman_collection.json.
- A visual data structure diagram is available in the Document folder for reference.