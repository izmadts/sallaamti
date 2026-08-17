// Sallaamti service worker — handles incoming Web Push notifications and
// notification-click routing. Deliberately plain JS served from the site
// root (not run through Vite): a service worker's scope is limited to the
// directory it's served from, so this has to live at /sw.js to control the
// whole site, not a hashed /build/assets/ path.

self.addEventListener('push', function (event) {
    let data = { title: 'Sallaamti', body: 'You have a new notification.', url: '/', icon: '/images/favicon.png' };

    if (event.data) {
        try {
            data = { ...data, ...event.data.json() };
        } catch (e) {
            data.body = event.data.text();
        }
    }

    event.waitUntil(
        self.registration.showNotification(data.title, {
            body: data.body,
            icon: data.icon,
            badge: '/images/favicon.png',
            data: { url: data.url },
        })
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = event.notification.data && event.notification.data.url ? event.notification.data.url : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
