importScripts(
    "https://storage.googleapis.com/workbox-cdn/releases/6.0.2/workbox-sw.js"
);

const { precacheAndRoute, matchPrecache } = workbox.precaching;
const { skipWaiting, clientsClaim, setCacheNameDetails } = workbox.core;
const { NetworkFirst, NetworkOnly, CacheFirst } = workbox.strategies;
const { registerRoute, setCatchHandler } = workbox.routing;

skipWaiting();
clientsClaim();

const VERSION = 1;
const PRECACHE = "precache-v1";
const RUNTIME = "runtime-v1";

setCacheNameDetails({
    prefix: "",
    suffix: "",
    precache: PRECACHE,
    runtime: RUNTIME,
});

precacheAndRoute(
    [
        { url: "/offline", revision: VERSION },
        { url: "/manifest.json", revision: null },
        { url: "/img/favicon.png", revision: null },
        { url: "/dist/img/PNG/VirtualEqubLogoIcon.png", revision: null },
        { url: "/pwa/pwa-192x192.png", revision: null },
        { url: "/pwa/pwa-512x512.png", revision: null },
        
        { url: "/css/app.css", revision: VERSION },
        { url: "/css/adminlte.min.css", revision: VERSION },
        { url: "/css/bootstrap-datepicker.css", revision: VERSION },
        { url: "/css/bootstrap-datetimepicker.min.css", revision: VERSION },
        { url: "/css/bootstrap.css", revision: VERSION },
        { url: "/css/bootstrap.min.css", revision: VERSION },
        { url: "/css/bootstrap.min.css.map", revision: VERSION },
        { url: "/css/bootstrap3-wysihtml5.min.css", revision: VERSION },
        { url: "/css/custom.css", revision: VERSION },
        { url: "/css/fileinput.min.css", revision: VERSION },
        { url: "/css/jqueryUI.css", revision: VERSION },
        { url: "/css/menu.css", revision: VERSION },
        { url: "/css/style.css", revision: VERSION },
        { url: "/css/vendors.css", revision: VERSION },
        { url: "/css/vendors.unminified.css", revision: VERSION },
        { url: "/css/fontello/css/animation.css", revision: VERSION },
        { url: "/css/fontello/css/fontello-codes.css", revision: VERSION },
        { url: "/css/fontello/css/fontello-embedded.css", revision: VERSION },
        { url: "/css/fontello/css/fontello-ie7-codes.css", revision: VERSION },
        { url: "/css/fontello/css/fontello-ie7.css", revision: VERSION },
        { url: "/css/fontello/css/fontello.css", revision: VERSION },

        { url: "/js/app.js", revision: VERSION },
        { url: "/js/adminlte.min.js", revision: VERSION },
        { url: "/js/bootstrap-datetimepicker.js", revision: VERSION },
        { url: "/js/bootstrap-datetimepicker.min.js", revision: VERSION },
        { url: "/js/jquery.min.js", revision: VERSION },
        {
            url: "https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap",
            revision: null,
        },
        {
            url: "https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap",
            revision: null,
        },
        {
            url: "https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback",
            revision: null,
        },
        {
            url: "https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css",
            revision: null,
        },
        {
            url: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css",
            revision: null,
        },
        {
            url: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css",
            revision: null,
        },
        {
            url: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css",
            revision: null,
        },
        {
            url: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css",
            revision: null,
        },
        {
            url: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker-standalone.css",
            revision: null,
        },
        {
            url: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css",
            revision: null,
        },
        {
            url: "https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css",
            revision: null,
        },
    ],
    {
        ignoreURLParametersMatching: [/.*/],
    }
);

registerRoute(
    ({ request }) => request.destination === "font",
    new CacheFirst({
        cacheName: PRECACHE,
    })
);

registerRoute(
    ({ request }) => request.method !== "GET",
    new NetworkOnly(),
    "POST"
);

registerRoute(({ request }) => request.mode === "navigate", new NetworkFirst());

const handler = async (options) => {
    const dest = options.request.destination;

    if (dest === "document") {
        return (await matchPrecache("offline")) || Response.error();
    }

    return Response.error();
};

setCatchHandler(handler);

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((cacheName) => cacheName !== PRECACHE)
                    .filter((cacheName) => cacheName !== RUNTIME)
                    .map((cacheName) => caches.delete(cacheName))
            );
        })
    );
});

(() => {
    "use strict";

    const WebPush = {
        init() {
            self.addEventListener("push", this.notificationPush.bind(this));
            self.addEventListener("notificationclick", this.notificationClick);
        },

        notificationPush(event) {
            if (
                !(
                    self.Notification &&
                    self.Notification.permission === "granted"
                )
            ) {
                return;
            }

            if (event.data) {
                event.waitUntil(this.sendNotification(event.data.json()));
            }
        },

        notificationClick(event) {
            event.notification.close();

            event.waitUntil(
                clients
                    .matchAll({
                        type: "window",
                    })
                    .then((clientList) => {
                        for (const client of clientList) {
                            if ("focus" in client) {
                                client.navigate(
                                    event.notification.actions[0].action || "/"
                                );
                                client.focus();
                                break;
                            }
                        }
                        if (clients.openWindow)
                            return clients.openWindow(
                                event.notification.actions[0].action || "/"
                            );
                    })
            );
        },

        sendNotification(data) {
            return self.registration.showNotification(data.title, data);
        },
    };

    WebPush.init();
})();