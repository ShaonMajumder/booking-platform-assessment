# Service Booking Platform — Sheba Platform Ltd. (Interview Assignment)

A simple backend system for a mini service booking platform, inspired by Sheba.xyz.  
Built with Laravel and MySQL.

---

## 📌 Features

### Core Features

-   ✅ **Service Listing API** – Paginated list of available services.
-   ✅ **Service Booking API** – Customers can book a service using their name, phone number, and service ID.
-   ✅ **Booking Status API** – Customers can track the status of a booking using a unique booking ID.

### Bonus Features

-   ✅ Admin API to add/edit/delete services
-   ✅ Admin API to view all bookings
-   ✅ JWT authentication for admin endpoints
-   ✅ Booking notification via mail with queue
-   ✅ Schedule date/time support in bookings
-   ✅ Docker support

---

## Competitive Advantages of this Architecture

-   **Indexed Table**
-   **Large Seeder** capable of importing Big Data :
    We used chunking for seeder for import large data (1M bookings)
    Disabled query log to improve performance.
    We used caching when seeding to store relational IDs values.
    We also used manual garbage collection and deleting to free up space to import very large data.
    We also dispacted the chunk to jobs using queue with DB Connection
    Later we used redis as QUEUE_CONNECTION, the seeding was lightning fast.
    but if more data is needed to import we will use mysql data load.
    Slowly observe - php artisan tinker
    \App\Models\Booking::count();
    -- show the demo
-   **Mail Sending** after booking confirmation in Asynchronus Queue.
-   **Throtling**

---

## 🛠️ Tech Stack

-   **Backend**: PHP 7.4 + Laravel Framework 8.83.29
-   **Database**: MySQL
-   **Cache/Queue**: Redis
-   **Server**: Nginx
-   **API Docs**: Swagger
-   **Containerization**: Docker + Docker Compose

---

## 🚀 Setup Instructions

### 1. Clone & Install

```bash
git clone https://github.com/ShaonMajumder/booking-platform-assessment.git
cd booking-platform-assessment
docker-compose up --build
```

### Migrate & Seed

Wait for some time to get all the service containers of docker to up.
Then in another conosle,

```bash
docker exec -it laravel-app bash
php artisan migrate:fresh
```

### Seed

```bash
php artisan db:seed
```

Result:
You have 10 million users, 20 services, and 1 admin user with email : admin@admin.com and password : 123456 .
Note:
You have to wait and monitor in docker container log to monitor the laravel-queue to get all the seeding operation done.

To verify :

```bash
php artisan tinker
\App\Models\Booking::count();
\App\Models\Service::count();
\App\Models\Admin::count();
```

## 🧪 Running Tests

```bash
php artisan test
```

Test coverage includes:

-   Service listing

-   Booking creation

-   Booking status retrieval

## Api Documentation

Learn more at http://localhost:8000/api/documentation

**Admin APIs (Protected by JWT)**

-   `POST /api/admin/login`
    Login admin api [Except JWT]
-   `POST /api/admin/logout`
    Logout admin api

-   `GET /api/admin/bookings`
    List bookings with pagination

-   `GET /api/admin/bookings/{id}`
    Get a specific booking

-   `GET /api/admin/services`
    Get a list of services

-   `POST /api/admin/services`
    Create a new service

-   `GET /api/admin/services/{id}`
    Get a specific service by ID

-   `PUT /api/admin/services/{id}`
    Update a specific service

-   `DELETE /api/admin/services/{id}`
    Delete a specific service

**Public Api**

-   `POST /api/bookings`
    Create a new service booking

-   `GET /api/bookings/{bookingId}`
    Retrieve the status of a booking

-   `GET /api/services`
    List available services

## 🧠 Assumptions and Scalability Notes

-   Admin actions are low QPS → no caching needed.

-   Public APIs (e.g., service listing) may use custom cache headers or Redis tagging.

-   Booking data grows over time → partition by month or archive old rows if high volume.

## ✅ TODO (Future Enhancements)

-   Load Balancing
-   Observibility 1 - Total Booking Monitoring, HTTP Status Statistics Dashboard and Error Mail Alert with Threshold - with Elasticsearch
-   Observibility 1 - Monitor Health, CPU Usage and other important systems Stats - with Promethues + Grafana
-   Feature 1 - Admin dashboard frontend (Vue/React or Blade)
-   Feature 2 - Booking cancellation or rescheduling

## 📌 Notes
