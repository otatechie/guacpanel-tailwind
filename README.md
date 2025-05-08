# GuacPanel - Laravel Vue Admin Starter Kit

A modern admin dashboard starter kit built with Laravel, Vue.js, Inertia, and Tailwind CSS.

![Dashboard Preview](https://via.placeholder.com/800x450?text=GuacPanel+Preview)

[🔗 Live Demo](https://demo-url.com) | [📘 Official Documentation](https://guacpanel.com)

## Features

- 🔐 **Authentication & Security**
  - Secure login with Laravel Fortify
  - Passwordless magic link authentication
  - Two-factor authentication (2FA)
  - Role-based permissions system
  - Password security policies
  - Password expiry enforcement
  - Force password change capability
  - Account disabling functionality
  - Authentication logs tracking
  - Browser session management

- 🎨 **Interface & Design**
  - Responsive mobile-friendly design
  - Dark/Light mode support
  - Modern dashboard layout
  - Google Fonts integration with local caching
  - Customizable theme configuration
  - Beautiful UI components

- 📊 **Data Management**
  - Interactive data tables with TanStack Table
  - Advanced filtering and sorting
  - Server-side pagination
  - Data export capabilities
  - Customizable columns
  - Action buttons integration

- 📁 **File Management**
  - FilePond integration for modern uploads
  - Drag and drop file uploads
  - Image and PDF preview support
  - File type validation
  - File size limits
  - Multiple file upload support
  - Progress indicators

- 🔄 **System Features**
  - Automated backup system
  - Activity tracking and auditing
  - Model change logging
  - User activity dashboard
  - Comprehensive audit logs
  - Browser session tracking
  - Security middleware

## Quick Start

### Prerequisites

- PHP >= 8.2
- Node.js & NPM (Latest LTS)
- Composer

### Installation

1. Clone the repository
```bash
git clone https://github.com/otatechie/guacpanel-tailwind.git
cd guacpanel-tailwind
```

2. Install dependencies
```bash
composer install
npm install
```

3. Set up environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in `.env`
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations and seed
```bash
php artisan migrate
php artisan db:seed
```

6. Start development servers
```bash
npm run dev
php artisan serve
```

## Tech Stack

- Laravel v11.x - PHP framework
- Vue.js v3.x - Frontend framework
- Inertia.js v2.x - Modern monolith
- Tailwind CSS v4.x - Utility-first CSS

## Additional Resources

- [👉 Official GuacPanel Documentation](https://guacpanel.com) - Complete guides and tutorials
- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide/introduction.html)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Inertia.js Documentation](https://inertiajs.com/)
- [TanStack Table Documentation](https://tanstack.com/table/v8)
- [FilePond Documentation](https://pqina.nl/filepond/)

## License

[MIT License](LICENSE.md)
