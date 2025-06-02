# GuacPanel - Laravel Vue Admin Starter Kit

[![Laravel](https://img.shields.io/badge/Laravel-v11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-v3.x-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-v4.x-06B6D4?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.md)

An opinionated Laravel starter kit with Vue.js, Inertia.js, and Tailwind CSS including authentication, admin dashboard, and essential features for building modern web applications.

## Screenshots

<table>
  <tr>
    <td><img src="https://github.com/user-attachments/assets/fa319d6a-695f-4d6f-95ea-16b72d128647" alt="Dashboard" width="100%"></td>
    <td><img src="https://github.com/user-attachments/assets/7e32de33-8001-425f-a2bb-b08399005335" alt="Dark Mode" width="100%"></td>
  </tr>
  <tr>
    <td><img src="https://github.com/user-attachments/assets/0bb0354f-b565-40f9-9cc8-9787ecd9a632" alt="Settings" width="100%"></td>
    <td><img src="https://github.com/user-attachments/assets/e438b751-ad01-455a-93f4-04e37e1c9537" alt="Personalization" width="100%"></td>
  </tr>
</table>


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

**🎉 That's it!** Visit `http://localhost:8000` to see the app in action.

**🔗 External Resources**

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide/introduction.html)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Inertia.js Documentation](https://inertiajs.com/)
- [TanStack Table Documentation](https://tanstack.com/table/v8)
- [FilePond Documentation](https://pqina.nl/filepond/)

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## License

[MIT License](LICENSE.md)
