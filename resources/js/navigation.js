/**
 * One list of where you can go. The sidebar and the command palette each kept
 * their own copy, and they had already drifted -- both showed "Notifications",
 * but the sidebar meant `notifications.index` (your own) and the palette meant
 * `admin.notifications.index` (the authoring tool).
 *
 * Removing a feature should mean deleting its entry here, not hunting for the
 * two places that name its route. See REMOVING.md.
 *
 * Shape:
 *   name        label shown to the user
 *   route       Laravel route name (RouteReferenceTest asserts it exists)
 *   icon        key into the icon maps in the sidebar and CommandPaletteItem
 *   permission  string or array; array means "any of these". Optional.
 *   feature     key on page.props.settings that must be truthy. Optional.
 *   keywords    extra search terms for the palette. Optional.
 */

/** Day-to-day destinations. These carry the sidebar. */
export const workspaceNav = [
    { name: 'Dashboard', route: 'dashboard', icon: 'home', keywords: ['home', 'main'] },
    {
        name: 'Charts',
        route: 'chart.index',
        icon: 'chart',
        keywords: ['analytics', 'graphs', 'data'],
    },
    {
        /* The reader's own notifications, not the admin authoring tool -- that
           lives under adminNav with the rest of system configuration. */
        name: 'Notifications',
        route: 'notifications.index',
        icon: 'bell',
        permission: ['view-notifications', 'manage-notifications'],
        feature: 'notificationEnabled',
        keywords: ['alerts', 'messages'],
    },
]

/** Your own account. Palette only -- the sidebar deliberately stays short. */
export const accountNav = [
    { name: 'My profile', route: 'user.account.index', icon: 'user', keywords: ['account'] },
    {
        name: 'Two-factor authentication',
        route: 'user.two.factor.authentication.index',
        icon: 'shield',
        keywords: ['2fa', 'security', 'totp'],
    },
    {
        name: 'My active sessions',
        route: 'user.session.index',
        icon: 'monitor',
        keywords: ['devices', 'logged in'],
    },
]

/** System configuration, reached from the header's gear. Palette only. */
export const adminNav = [
    {
        name: 'System settings',
        route: 'admin.setting.index',
        icon: 'cog',
        keywords: ['config', 'preferences'],
    },
    {
        name: 'System activity',
        route: 'admin.audit.index',
        icon: 'activity',
        keywords: ['logs', 'audit'],
    },
    {
        name: 'Theme settings',
        route: 'admin.personalization.index',
        icon: 'palette',
        keywords: ['colors', 'appearance'],
    },
    {
        name: 'User management',
        route: 'admin.user.index',
        icon: 'users',
        keywords: ['accounts', 'members'],
    },
    {
        name: 'Data backup',
        route: 'admin.backup.index',
        icon: 'database',
        keywords: ['restore', 'export'],
    },
    {
        name: 'Access control',
        route: 'admin.permission.role.index',
        icon: 'shield',
        keywords: ['roles', 'permissions'],
    },
    {
        name: 'Login history',
        route: 'admin.login.history.index',
        icon: 'history',
        keywords: ['access', 'logins'],
    },
    {
        name: 'Security settings',
        route: 'admin.setting.show',
        icon: 'lock',
        keywords: ['password', 'auth'],
    },
    {
        name: 'All sessions',
        route: 'admin.sessions.index',
        icon: 'monitor',
        keywords: ['devices', 'active', 'session management'],
    },
    {
        name: 'Health status',
        route: 'admin.health.index',
        icon: 'heart',
        keywords: ['status', 'monitoring'],
    },
    {
        name: 'Failed jobs',
        route: 'admin.failed-jobs.index',
        icon: 'activity',
        permission: ['view-failed-jobs', 'manage-failed-jobs'],
        keywords: ['queue', 'errors', 'retry', 'jobs'],
    },
    {
        name: 'Manage notifications',
        route: 'admin.notifications.index',
        icon: 'bell',
        permission: 'manage-notifications',
        feature: 'notificationEnabled',
        keywords: ['authoring', 'send', 'broadcast'],
    },
]
