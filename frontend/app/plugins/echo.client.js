/*
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Global assignation per Pusher
window.Pusher = Pusher;

export default defineNuxtPlugin((nuxtApp) => {
  const echo = new Echo({
      broadcaster: 'reverb',
      key: 'reverb_app_key', // Simulant configuració .env
      wsHost: '127.0.0.1', 
      wsPort: 8080,
      wssPort: 8080,
      forceTLS: false,
      enabledTransports: ['ws', 'wss'],
  });

  return {
    provide: { echo }
  };
});
*/
export default defineNuxtPlugin(() => {});
