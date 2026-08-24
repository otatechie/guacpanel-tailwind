import { describe, expect, it } from 'vitest'
import { formatPermissionName, groupPermissions } from '@/utils/permissions'

const perm = name => ({ id: name, name })

describe('formatPermissionName', () => {
    it('turns a slug into a sentence', () => {
        expect(formatPermissionName('manage-security-settings')).toBe('Manage security settings')
        expect(formatPermissionName('')).toBe('')
        expect(formatPermissionName(undefined)).toBe('')
    })
})

describe('groupPermissions', () => {
    it('buckets by the thing governed, not the verb', () => {
        const groups = groupPermissions([
            perm('view-dashboard'),
            perm('manage-users'),
            perm('access-dashboard'),
            perm('ban-users'),
            perm('edit-profile'),
        ])

        const byLabel = Object.fromEntries(
            groups.map(g => [g.label, g.permissions.map(p => p.name)])
        )

        // The pair that sat eight positions apart in the flat list.
        expect(byLabel.Monitoring).toEqual(['access-dashboard', 'view-dashboard'])
        expect(byLabel.Users).toEqual(['ban-users', 'edit-profile', 'manage-users'])
    })

    it('keeps `manage-permissions` out of the broader buckets', () => {
        const [group] = groupPermissions([perm('manage-permissions')])
        expect(group.label).toBe('Access control')
    })

    it('drops empty groups and parks unmatched names in Other', () => {
        const groups = groupPermissions([perm('brew-coffee')])
        expect(groups).toHaveLength(1)
        expect(groups[0].label).toBe('Other')
    })

    it('survives an empty list', () => {
        expect(groupPermissions()).toEqual([])
    })
})
