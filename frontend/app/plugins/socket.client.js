import { io } from 'socket.io-client';

export default defineNuxtPlugin((nuxtApp) => {
  const socket = io('http://localhost:3001', {
    autoConnect: false // Connexió controlada pel Store Pinia
  });
  
  return {
    provide: {
      socket // fa d'injector del component global
    }
  }
})
