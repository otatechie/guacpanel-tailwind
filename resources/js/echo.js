import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const csrf = document
  .querySelector('meta[name="csrf-token"]')
  ?.getAttribute("content");

window.Echo = new Echo({
  broadcaster: "reverb",
  key: import.meta.env.VITE_REVERB_APP_KEY,

  wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
  wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
  wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),

  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "http") === "https",
  enabledTransports: ["ws", "wss"],

  // IMPORTANT for Fortify/session auth:
  authEndpoint: "/broadcasting/auth",
  withCredentials: true,
  auth: {
    headers: {
      "X-Requested-With": "XMLHttpRequest",
      ...(csrf ? { "X-CSRF-TOKEN": csrf } : {}),
    },
  },
});
