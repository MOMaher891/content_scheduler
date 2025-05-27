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


If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

# Social Media Post Manager

A comprehensive Laravel-based social media management platform that allows users to create, schedule, and publish posts across multiple social media platforms from a single dashboard.

## Key Features

- **Unified Dashboard**: Monitor post statistics and scheduled content at a glance with visual graphs
- **Multi-Platform Support**: Connect and manage multiple social media accounts in one place
- **Post Scheduling**: Create posts in advance and schedule them for automatic publishing
- **Content Management**: Draft, schedule, and publish posts with rich content support including images
- **Automated Publishing**: Background jobs automatically publish scheduled posts at their designated times
- **Advanced Filtering**: Filter posts by status (draft, scheduled, published) and date ranges
- **Real-time Statistics**: Track post performance with dynamic, visual representations

## Technical Stack

- **Framework**: Laravel 10
- **Frontend**: Livewire, Alpine.js, Tailwind CSS
- **Visualization**: ApexCharts for data visualization
- **Authentication**: Laravel Breeze
- **Background Processing**: Laravel Jobs and Scheduler

## Getting Started

1. Clone the repository
2. Run `composer install`
3. Run `npm install && npm run dev`
4. Configure your database in `.env`
5. Run migrations with `php artisan migrate`
6. Start the scheduler with `php artisan schedule:run`
7. Access the application at `http://localhost:8000`

## Scheduler Setup

To ensure scheduled posts are published automatically, add the following to your server's crontab:

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.
