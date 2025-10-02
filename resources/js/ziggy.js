const Ziggy = {
    url: 'http:\/\/localhost',
    port: null,
    defaults: {},
    routes: {
        'sanctum.csrf-cookie': {
            uri: 'sanctum\/csrf-cookie',
            methods: ['GET', 'HEAD']
        },
        'ignition.healthCheck': {
            uri: '_ignition\/health-check',
            methods: ['GET', 'HEAD']
        },
        'ignition.executeSolution': {
            uri: '_ignition\/execute-solution',
            methods: ['POST']
        },
        'ignition.updateConfig': {
            uri: '_ignition\/update-config',
            methods: ['POST']
        }
    }
}
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes)
}
export { Ziggy }
