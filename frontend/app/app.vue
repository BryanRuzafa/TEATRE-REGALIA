<template>
  <div>
    <NuxtPage />
  </div>
</template>

<script setup>
// Layout global gestionat per Nuxt Pages
</script>

<style>
/* Estils base - Treball de Recerca (Cinema Style Luxury) */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,400;0,800;1,400&display=swap');

:root {
  --granat-profund: #1a0505;
  --granat: #4a0912;
  --burgundy: #8b1522;
  --daurat: #d4af37;
  --daurat-lluminos: #f0d78c;
  --crema: #faf9f6;
  --text-suau: rgba(250, 249, 246, 0.7);
  --glass: rgba(255, 255, 255, 0.05);
  --glass-border: rgba(255, 255, 255, 0.1);
  --ombra-premium: 0 20px 40px rgba(0,0,0,0.6);
  --transicio: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
}

body {
  margin: 0; padding: 0; 
  background-color: var(--granat-profund); 
  background-image: 
    radial-gradient(circle at 20% 20%, rgba(139, 21, 34, 0.15) 0%, transparent 40%),
    radial-gradient(circle at 80% 80%, rgba(139, 21, 34, 0.15) 0%, transparent 40%);
  color: var(--crema);
  font-family: 'Outfit', sans-serif;
  -webkit-font-smoothing: antialiased;
  min-height: 100vh;
}

h1, h2, h3, .serif {
  font-family: 'Playfair Display', serif;
}

/* Scrollbar personalitzada per a l'estètica del projecte */
::-webkit-scrollbar { width: 8px; }
::-webkit-scrollbar-track { background: var(--granat-profund); }
::-webkit-scrollbar-thumb { background: var(--daurat); border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: var(--daurat-lluminos); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
