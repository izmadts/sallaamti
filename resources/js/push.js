// Web Push registration — deliberately does NOT request notification
// permission on page load (browsers penalize/auto-block unsolicited
// prompts). Permission is only ever requested from enablePushNotifications(),
// called by an explicit user tap (see components/pwa-install-banner.blade.php
// and the "Enable Notifications" button wherever it appears).

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
}

function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
}

export function isPushSupported() {
    return 'serviceWorker' in navigator && 'PushManager' in window;
}

export function registerServiceWorker() {
    if (!isPushSupported()) return Promise.resolve(null);
    return navigator.serviceWorker.register('/sw.js');
}

export async function enablePushNotifications() {
    if (!isPushSupported()) {
        return { success: false, reason: 'unsupported' };
    }

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        return { success: false, reason: 'denied' };
    }

    const registration = await navigator.serviceWorker.ready;
    const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]')?.content;
    if (!vapidPublicKey) {
        return { success: false, reason: 'not-configured' };
    }

    const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
    });

    const res = await fetch('/push/subscribe', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        body: JSON.stringify(subscription.toJSON()),
    });

    if (!res.ok) {
        return { success: false, reason: 'server-error' };
    }

    return { success: true };
}

registerServiceWorker();
