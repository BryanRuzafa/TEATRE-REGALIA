// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-04-03',
  devtools: { enabled: true },
  modules: ['@pinia/nuxt'],
  ssr: false, 
  runtimeConfig: {
    public: {
      apiBase: '/api', // Ara les peticions passaran pel nostre server proxy
      socketHost: '' 
    }
  }
})
