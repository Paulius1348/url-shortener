# URL Shortener

Live demo: https://url-shortener.infinityfreeapp.com

A URL shortening service built with vanilla PHP and MySQL, featuring user authentication, click tracking, and password reset functionality.

## Features

- Shorten long URLs into compact links
- User registration and login
- Dashboard to manage and track your URLs
- Click statistics with daily breakdown
- Password reset via email
- REST API for URL shortening and statistics

## Tech Stack

- PHP 8.3.16 (vanilla, no framework)
- MySQL 8.4.3
- Bootstrap 5
- PHPMailer
  
## Installation

1. Clone the repository
```bash
   git clone https://github.com/Paulius1348/url-shortener.git
```

2. Import the schema (creates the database automatically)
```bash
   mysql -u root -p < schema.sql
```

3. Configure the database connection
```bash
   cp src/config/database.example.php src/config/database.php
```
   Edit `src/config/database.php` with your database credentials.

4. Configure email
```bash
   cp src/config/mail.example.php src/config/mail.php
```
   Edit `src/config/mail.php` with your SMTP credentials.

5. Install PHPMailer
```bash
   composer install
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/?path=stats` | Get total URLs and clicks |
| GET | `/api/?path=urls&code={code}` | Get URL stats by short code |
| POST | `/api/?path=urls` | Create a short URL |

### POST /api/?path=urls
```json
{
    "url": "https://example.com/very-long-url"
}
```

## Project Structure

```
url-shortener/
├── public/          # Front-facing pages
├── src/
│   ├── config/      # Database and mail config
│   ├── controllers/ # Business logic
│   └── models/      # Database interactions
├── api/             # REST API
└── schema.sql       # Database schema
```
