self.addEventListener('push', function (event) {
    console.log('[SW] PUSH RECEIVED');
    console.log(`[SW] Data : ${event.data.text()}`);

    const title = "BELAJAR PUSH";
    const options = {
        body: event.data.text(),
        icon: "images/icon.png",
        badge: "images/icon.png",
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    console.log('[SW] Clicked');

    event.notification.close();
    event.waitUntil(
        clients.openWindow('https://google.com')
    );
})