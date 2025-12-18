<div align="center">
  <img src="https://github.com/user-attachments/assets/d1256dda-6820-4e21-bcee-36c82ffab1fc" alt="GuacPanel Logo" width="200">
</div>

# GuacPanel - Laravel Vue Admin Starter Kit

[![Laravel](https://img.shields.io/badge/Laravel-v12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-v3.x-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-v4.x-06B6D4?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/license/MIT)

An opinionated Laravel starter kit built with Vue.js, Inertia.js, and Tailwind CSS including authentication, admin dashboard, and essential features for building modern web applications.

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
    - Secure login with [Laravel Fortify](https://laravel.com/docs/fortify)
    - Passwordless magic link authentication
    - Social Authentication with [Laravel Socialite](https://laravel.com/docs/socialite)
        - [Google](https://console.developers.google.com/)
        - [GitHub](https://github.com/settings/applications/new) (Will work with local dev callback)
        - [Facebook](https://developers.facebook.com/) (Will work with local dev callback)
        - [LinkedIn](https://www.linkedin.com/developers/apps/) (Will work with local dev callback)
    - Two-factor authentication (2FA) via [Laravel Fortify](https://laravel.com/docs/fortify#two-factor-authentication)
    - Role-based permissions with [Spatie Permission](https://spatie.be/docs/laravel-permission)
        - Visual role and permission management
        - User role assignment interface
    - Session and security management
        - Active sessions overview
        - Login history tracking
        - Password policies enforcement

- 🎨 **Interface & Design**
    - Dark/Light mode with system preference detection
    - Responsive design with [Tailwind CSS v4](https://tailwindcss.com/docs)
    - Auto-generated avatars via [Laravel Avatar](https://github.com/laravolt/avatar)
    - Local Google Fonts via [Spatie Laravel Google Fonts](https://github.com/spatie/laravel-google-fonts)
    - Customizable theme settings

- 📊 **Data Visualization**
    - Interactive charts with [ApexCharts v3](https://apexcharts.com)
        - Line, Area, Bar, and Donut charts
        - Responsive and mobile-friendly
        - Export capabilities
    - Automatic data formatting
    - Dynamic chart resizing

- 📊 **Data Tables**
    - Modern tables with [@tanstack/vue-table v8](https://tanstack.com/table/v8/docs)
        - Server-side pagination
        - Column sorting
        - Search functionality
    - Data export with [Laravel Excel](https://docs.laravel-excel.com)
    - Action buttons with confirmation dialogs

- 📁 **File Management**
    - Drag & drop uploads with [FilePond v4](https://pqina.nl/filepond/docs/)
        - Image preview
        - File type validation
        - Size restrictions
    - Multiple file selection

- 🔄 **System Features**
    - Backup management via [Spatie Backup](https://spatie.be/docs/laravel-backup)
        - User-friendly dashboard
        - One-click backup creation
        - Backup download
    - Activity logging with [Laravel Auditing](https://laravel-auditing.com)
        - User action tracking
        - Data change history
    - Security event monitoring

- 🔔 **Real-time Notifications (Laravel Reverb)**
    - Live, in-app notifications via WebSockets (Reverb)
    - Demo mode toggle via `APP_NOTIFICATIONS_IN_DEMO_MODE`
    - Run locally with `php artisan reverb:start`

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

### Real-time Notifications (Laravel Reverb)

GuacPanel ships with an optional real-time notifications system powered by **Laravel Reverb** (WebSockets).

1. Enable notifications in `.env`

```dotenv
APP_NOTIFICATIONS_ENABLED=true
APP_NOTIFICATIONS_IN_DEMO_MODE=true # set this to false to enable live reverb notifications.
```

2. Install broadcasting + Reverb (generates credentials)

```bash
php artisan install:broadcasting
```

This will populate the following in your `.env` (you can override these as needed):

```dotenv
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=https
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080
REVERB_TLS_CERT=
REVERB_TLS_KEY=
```

3. Start the Reverb server (separate terminal)

```bash
php artisan reverb:start
```

> If you're using Herd / Valet secure domains, you can point `REVERB_TLS_CERT` and `REVERB_TLS_KEY` at the generated certificate + key to use `wss://` in local development.

**🎉 That's it!** Visit `http://localhost:8000` to see the app in action.

**🔗 External Resources**

- [GuacPanel Documentation](https://guacpanel.com)
- [Laravel Documentation](https://laravel.com/docs)
- [Fortify Documentation](https://laravel.com/docs/fortify)
- [Socialite Documentation](https://laravel.com/docs/socialite)
- [Vue.js Documentation](https://vuejs.org/guide/introduction.html)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Inertia.js Documentation](https://inertiajs.com/)
- [TanStack Table Documentation](https://tanstack.com/table/v8)
- [FilePond Documentation](https://pqina.nl/filepond/)
- [ApexCharts Documentation](https://apexcharts.com/docs/)

## 🚀 Live Demo

**[👀 View Live Demo](https://guacpanel.com)**

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## License

[MIT License](https://opensource.org/license/MIT)
