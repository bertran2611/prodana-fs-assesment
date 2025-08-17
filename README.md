# Prodana FS Test - Laravel Application -by Bertrandus Gabriel Utomo

A Laravel web application with Livewire components for product management.

## Prerequisites

Before running this application, make sure you have the following installed:

-   **PHP** >= 8.1
-   **Composer** (PHP package manager)
-   **Node.js** >= 16.0 and **npm**
-   **MySQL** or **SQLite** database
-   **Git**

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd prodana-fs-test
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

Copy the environment file and configure your database settings:

```bash
cp .env.example .env
```

Edit `.env` file and update the following variables:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prodana_fs_test
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations

```bash
php artisan migrate
```

### 7. Seed the Database (Optional)

```bash
php artisan db:seed
```

### 8. Build Frontend Assets

```bash
npm run build
```

## Running the Application

### Development Mode

1. **Start the Laravel development server:**

    ```bash
    php artisan serve
    ```

    The application will be available at `http://localhost:8000`

2. **For Livewire development (watch mode):**
    ```bash
    npm run dev
    ```

### Production Mode

1. **Build production assets:**

    ```bash
    npm run build
    ```

2. **Start the server:**
    ```bash
    php artisan serve
    ```

## Features

-   **Authentication System** - User registration, login, and profile management
-   **Product Management** - CRUD operations for products with categories
-   **Livewire Components** - Dynamic, reactive interfaces
-   **Role-based Access Control** - User roles and permissions
-   **Responsive Design** - Built with Tailwind CSS

## File Structure

-   `app/Livewire/` - Livewire components
-   `app/Models/` - Eloquent models
-   `app/Http/Controllers/` - HTTP controllers
-   `resources/views/` - Blade templates
-   `database/migrations/` - Database schema
-   `database/seeders/` - Sample data

## Testing

Run the test suite:

```bash
php artisan test
```

## Troubleshooting

### Common Issues

1. **Permission denied errors:**

    ```bash
    chmod -R 755 storage bootstrap/cache
    ```

2. **Composer autoload issues:**

    ```bash
    composer dump-autoload
    ```

3. **Cache issues:**
    ```bash
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    ```

### Database Connection Issues

-   Ensure your database server is running
-   Verify database credentials in `.env` file
-   Check if the database exists

## Support

For issues and questions, please check:

-   Laravel documentation: https://laravel.com/docs
-   Livewire documentation: https://laravel-livewire.com/docs

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
