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

### 2. Migrate

Wait for some time to get all the service containers of docker to up.
Then in another conosle,

```bash
docker exec -it laravel-app bash
php artisan migrate:fresh
```

### 3. Seed

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

---

## 🧪 Running Tests

```bash
php artisan test
```

Test coverage includes:

-   Service listing

-   Booking creation

-   Booking status retrieval

---

## Api Documentation

Learn more at http://localhost:8000/api/documentation

**Admin APIs (Protected by JWT)**

-   `POST /api/v1/admin/login`
    Login admin api [Except JWT]
-   `POST /api/v1/admin/logout`
    Logout admin api

-   `GET /api/v1/admin/bookings`
    List bookings with pagination

-   `GET /api/v1/admin/bookings/{id}`
    Get a specific booking

-   `GET /api/v1/admin/services`
    Get a list of services

-   `POST /api/v1/admin/services`
    Create a new service

-   `GET /api/v1/admin/services/{id}`
    Get a specific service by ID

-   `PUT /api/v1/admin/services/{id}`
    Update a specific service

-   `DELETE /api/v1/admin/services/{id}`
    Delete a specific service

**Public Api**

-   `POST /api/v1/bookings`
    Create a new service booking

-   `GET /api/v1/bookings/{bookingId}`
    Retrieve the status of a booking

-   `GET /api/v1/services`
    List available services

---

## Competitive Advantages of this Architecture

-   **Indexed Table**

-   **Mail Sending** after booking confirmation in Asynchronus Queue.
-   **Throtling**

## Competitive Advantages of this Architecture

### **Scalable Architecture**

-   **Microservices-Ready Design**: The architecture is designed to scale horizontally. While the current implementation is monolithic, it follows principles that will make it easier to split into microservices in the future.
-   **Queue-based Asynchronous Processing**: Critical operations such as mail sending after booking confirmation are handled via asynchronous queues (Redis queue), ensuring the API remains responsive under high load.
-   **Event-Driven Architecture**: Leveraging Laravel's events and listeners, we can add new functionality (e.g., sending notifications, processing payments) with minimal changes to core logic. This enables easy scaling and maintenance.
-   **Redis Cache for Performance**: Caching key data in Redis improves the speed of frequently accessed services (e.g., service listing) and booking statuses, reducing database load.

### **Database Scaling**

-   **Sharding**: With a growing number of bookings, we can shard the database by `month` or `year`, ensuring that no single table becomes a bottleneck. The database design is optimized for this partitioning strategy.
-   **Indexing**: All frequently queried fields, such as `service_id`, `booking_id`, and `phone`, are indexed to enhance query performance. This is especially important as booking volume increases.
-   **Optimized Seeder for Large Datasets**: Seeder is optimized for importing large datasets (1M+ bookings) using chunking and queuing techniques, ensuring smooth database population without causing locking or performance degradation. To improve performance:

    -- **Chunking**: Data is imported in smaller chunks to avoid overwhelming the database and causing timeouts or memory issues.

    -- **Query Log Disabled**: We disable query logging during seeding to minimize overhead and speed up the process.

    -- **Caching Relational IDs**: Relational IDs are cached in Redis to avoid repeated database lookups, improving efficiency as lightning fast.

    -- **Manual Garbage Collection**: Garbage collection is manually triggered during the seeding process to free up memory and optimize space usage.

    -- **Queue Dispatching**: Seeder jobs (with chunk data) are dispatched to a queue (using Redis as the queue connection) to process chunks asynchronously, ensuring non-blocking execution.
    -- **MySQL Data Load for Large Imports**: For importing even larger datasets, we use MySQL’s native data load functionality for bulk operations, ensuring extremely fast data insertion without the overhead of ORM.

-   **Bulk Operations for High Volume**: We use bulk inserts and updates in cases where large volumes of data need to be processed, ensuring minimal performance impact during high traffic periods.

### **Data Integrity & Consistency**

-   **Transactional Integrity**: For critical operations (like creating bookings and services), we utilize database transactions to ensure data consistency. In case of failure, all changes are rolled back, ensuring no partial data is committed.
-   **Advanced Seeding and Garbage Collection**: We handle large data seeding by chunking operations and enabling garbage collection, freeing up space as necessary to avoid performance degradation.

### **High Availability & Fault Tolerance**

-   **Database Replication**: In a production environment, MySQL can be configured with master-slave replication to offload read-heavy operations to slave databases, reducing the load on the primary instance.
-   **Queue Failover with Redis**: Redis-backed queues offer reliable message delivery even if workers crash or become unavailable. Additionally, Redis can be configured to provide high availability with Sentinel, ensuring continuous operations.

### **Mail Sending in Asynchronous Queue**

-   **Email Queuing**: Email notifications for booking confirmations are sent via Laravel’s queue system, allowing the platform to handle high volumes of bookings without delay. This ensures that emails are processed in the background without affecting API response times.

### **Rate Limiting & Throttling**

-   **API Rate Limiting**: To protect the service from abuse and ensure fair usage, we’ve implemented rate limiting on APIs, particularly the public booking and service listing endpoints. This ensures that no single user or bot can overwhelm the system.

### **Future-proofed for Scale**

-   **Horizontal Scaling**: The backend is designed to scale horizontally. Both Laravel and MySQL support horizontal scaling to accommodate increasing traffic. We can add more app servers and database replicas as needed.
-   **Containerization with Docker**: The app is Dockerized, enabling easy deployment and scalability across different environments (development, staging, production). This also supports auto-scaling in cloud environments such as AWS or Google Cloud.
-   **ElasticSearch for Search and Monitoring**: The use of ElasticSearch for advanced search functionality (like booking queries, service discovery) will ensure that the platform can handle complex search queries and grow without performance bottlenecks.
-   **Grafana for monitoring container services healths.**

### **Optimized Querying with Indexes**

-   **Database Indexing**: Indexes are added to key columns like `service_id`, `booking_id`, and `user_id`. This reduces query times significantly and is essential for performance as the database grows with millions of bookings.
-   **Query Optimization**: Complex queries are optimized using `JOIN` conditions and limiting the amount of data fetched per query. We leverage `select` only the necessary fields, further improving performance.

### **Extensibility & Maintainability**

-   **Service Layer**: The service layer in the app isolates business logic from controllers, making it easier to refactor or extend. We’ve implemented the SOLID principles to ensure that code is modular and maintainable.
-   **Testable Code**: All core logic is unit-tested, ensuring that new features and changes can be implemented with confidence. We use PHPUnit to run tests and maintain high code quality.
-   **Swagger API Documentation**: The API is documented using Swagger, making it easier for future developers to understand, extend, or maintain the platform.

### **Scalability with Redis**

-   **Queueing System**: By using Redis as the queue backend for email and background tasks, we ensure that tasks are processed asynchronously, and no customer experience is delayed due to back-end processes.
-   **Cache Optimization**: Redis is used to cache services and booking statuses, which helps in reducing database load for frequently accessed resources.

---

This architecture is optimized for high availability, performance, and scalability. As the platform grows, these strategies will ensure that it can handle increasing traffic and data efficiently while maintaining a seamless user experience.

---

## 🧠 Assumptions and Scalability Notes

-   Admin actions are low QPS → no caching needed.

-   Public APIs (e.g., service listing) may use custom cache headers or Redis tagging.

-   Booking data grows over time → partition by month or archive old rows if high volume.

---

## ✅ TODO (Future Enhancements)

-   Load Balancing
-   Observibility 1 - Total Booking Monitoring, HTTP Status Statistics Dashboard and Error Mail Alert with Threshold - with Elasticsearch
-   Observibility 1 - Monitor Health, CPU Usage and other important systems Stats - with Promethues + Grafana
-   Feature 1 - Admin dashboard frontend (Vue/React or Blade)
-   Feature 2 - Booking cancellation or rescheduling

---

## 📌 Demo
