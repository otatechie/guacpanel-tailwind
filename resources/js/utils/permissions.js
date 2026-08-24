/**
 * Permission naming, in one place. Three components each carried their own copy
 * of the slug formatter (formatName / formatPerm / formatPermission).
 */

/** `manage-security-settings` → `Manage security settings` */
export const formatPermissionName = name => {
    const words = (name || '').split('-').join(' ')
    return words.charAt(0).toUpperCase() + words.slice(1)
}

/* Grouped by the thing being governed, not by the verb. Splitting on the slug
   itself was the obvious move and it is wrong: it yields fifteen groups, nine of
   them holding a single permission, which is noisier than no grouping at all.
   First match wins, so order matters — `manage-permissions` has to reach Access
   control before anything broader claims it. */
const GROUPS = [
    { label: 'Users', match: /users|profile|impersonate/ },
    { label: 'Access control', match: /roles|permissions/ },
    { label: 'Settings', match: /settings|personalisation/ },
    { label: 'Notifications', match: /notifications/ },
    { label: 'Monitoring', match: /dashboard|audits|sessions|health|backups|login-history/ },
]

/**
 * Bucket permissions for display: `[{ label, permissions: [] }]`, groups in
 * GROUPS order, alphabetical within each, empty groups dropped. Anything a new
 * migration adds that matches nothing lands in `Other` rather than vanishing.
 */
export const groupPermissions = (permissions = []) => {
    const buckets = new Map(GROUPS.map(g => [g.label, []]))
    buckets.set('Other', [])

    for (const permission of permissions) {
        const group = GROUPS.find(g => g.match.test(permission.name || ''))
        buckets.get(group ? group.label : 'Other').push(permission)
    }

    return [...buckets.entries()]
        .filter(([, list]) => list.length)
        .map(([label, list]) => ({
            label,
            permissions: [...list].sort((a, b) => a.name.localeCompare(b.name)),
        }))
}
