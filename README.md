<div align="center">
  <img src="https://github.com/user-attachments/assets/d1256dda-6820-4e21-bcee-36c82ffab1fc" alt="GuacPanel Logo" width="200">
</div>

# GuacPanel - Laravel Vue Admin Starter Kit

[![Laravel](https://img.shields.io/badge/Laravel-v13.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-v3.x-4FC08D?style=flat&logo=vue.js)](https://vuejs.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-v4.x-06B6D4?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/license/MIT)

An opinionated Laravel admin starter kit built with Vue.js, Inertia.js and Tailwind CSS.

Most starter kits give you authentication and a dashboard. This one gives you the
things an admin panel needs on day 90: an audit trail of who changed what, login
history, live sessions you can terminate, guarded and logged impersonation,
scheduled backups, health checks, and a `Cmd/Ctrl+K` palette that searches your
pages, actions and data.

Two things make it different from the alternatives:

- **You own the frontend.** These are Vue files you edit, not PHP that renders
  someone else's components. The shadcn-vue layer sits behind wrappers with a
  documented contract ([docs/ui-contract.md](docs/ui-contract.md)), so the
  component library is replaceable without touching a single page.
- **It is built to be cut down.** A starter kit is forked and stripped. Features
  come out along documented seams ([REMOVING.md](REMOVING.md)), and a test
  cross-checks every route the frontend calls against the ones that exist, so a
  removal that breaks something fails your build instead of a user's request.

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
    - [shadcn-vue](https://www.shadcn-vue.com) primitives behind a wrapper layer, so pages
      stay decoupled from the component library — see [docs/ui-contract.md](docs/ui-contract.md)
    - Dark/Light mode with system preference detection
    - Responsive design with [Tailwind CSS v4](https://tailwindcss.com/docs)
    - Command palette on `Cmd/Ctrl+K`, searching pages, actions and your data
    - Auto-generated avatars via [Laravel Avatar](https://github.com/laravolt/avatar)
    - Customizable theme settings

- 📊 **Data Visualization**
    - Interactive charts with [Unovis v1.6](https://unovis.dev)
        - Line, Area, Bar, Donut and Sparkline
        - Responsive and mobile-friendly
        - Themed by the app's design tokens, so charts follow light/dark mode
    - Shared legend, tooltip and axis formatting

- 📊 **Data Tables**
    - Modern tables with [@tanstack/vue-table v8](https://tanstack.com/table/v8/docs)
        - Server-side pagination
        - Column sorting
        - Search and filtering
        - CSV export and bulk actions
    - Row action menus with confirmation dialogs

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

- PHP >= 8.3
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

### Default login

Seeding creates two accounts (configurable via the `SEED_*` variables in `.env`):

| Account | Email | Password |
|---|---|---|
| Superuser | `ota@example.com` | `password` |
| Regular user | `user@example.com` | `password` |

**🔗 External Resources**

- [Removing features](REMOVING.md) — what to delete, and the shared files to edit
- [UI component contract](docs/ui-contract.md) — the wrapper layer and its rules
- [GuacPanel Documentation](https://guacpanel.com)
- [Laravel Documentation](https://laravel.com/docs)
- [Fortify Documentation](https://laravel.com/docs/fortify)
- [Socialite Documentation](https://laravel.com/docs/socialite)
- [Vue.js Documentation](https://vuejs.org/guide/introduction.html)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Inertia.js Documentation](https://inertiajs.com/)
- [TanStack Table Documentation](https://tanstack.com/table/v8)
- [FilePond Documentation](https://pqina.nl/filepond/)
- [Unovis Documentation](https://unovis.dev/docs/intro)
- [shadcn-vue Documentation](https://www.shadcn-vue.com/docs/introduction.html)

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## License

[MIT License](https://opensource.org/license/MIT)
