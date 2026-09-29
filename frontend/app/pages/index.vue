<template>
  <div class="portada-luxe">
    <header class="capçalera-principal">
      <NuxtLink to="/consulta" class="link-consulta-top">BUSCAR LES MEVES ENTRADES</NuxtLink>
      <h1 class="titol-logo">TEATRE <span class="daurat">REGALIA</span></h1>
      <p>Cartellera d'Esdeveniments Exclusius</p>
    </header>

    <main class="llistat-esdeveniments">
      <div v-for="e in esdeveniments" :key="e.id" class="targeta-esdeveniment">
        <div class="contenidor-imatge">
          <img :src="e.imatge_url" :alt="e.titol" class="poster-pelicula" />
          <div class="gradient-overlay"></div>
        </div>
        <div class="contingut-targeta">
          <h2>{{ e.titol }}</h2>
          <p class="data">{{ formulariData(e.data_hora) }}</p>
          <p class="recinte">{{ e.recinte }}</p>
          <p class="preu">Des de {{ e.preu_base }}€</p>
          <NuxtLink :to="'/esdeveniment/' + e.id" class="boto-reserva">
            RESERVAR BUTAQUES
          </NuxtLink>
        </div>
      </div>
    </main>

    <footer class="peu-portada">
      <NuxtLink to="/consulta" class="link-peu">Consulta d'entrades</NuxtLink>
      <span class="separador">|</span>
      <NuxtLink to="/admin" class="link-admin">Accés Administració</NuxtLink>
    </footer>
  </div>
</template>

<script setup>
const config = useRuntimeConfig();
const { data: esdeveniments } = await useFetch(`${config.public.apiBase}/esdeveniments`);

const formulariData = (dataStr) => {
  return new Date(dataStr).toLocaleString('ca-ES', { 
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' 
  });
}
</script>

<style scoped>
.portada-luxe {
  min-height: 100vh;
  padding: 60px 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.capçalera-principal {
  text-align: center;
  margin-bottom: 80px;
}

.titol-logo {
  font-family: 'Playfair Display', serif;
  font-size: 3rem;
  letter-spacing: 8px;
  margin-bottom: 10px;
}

.daurat { color: var(--daurat); }

.llistat-esdeveniments {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 40px;
  width: 100%;
  max-width: 1200px;
}

.targeta-esdeveniment {
  background: var(--crema);
  color: var(--granat);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 20px 50px var(--ombra);
  transition: transform 0.3s;
  display: flex;
  flex-direction: column;
}

.targeta-esdeveniment:hover {
  transform: translateY(-10px);
}

.contenidor-imatge {
  position: relative;
  height: 280px;
  overflow: hidden;
}

.poster-pelicula {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: var(--transicio);
}

.targeta-esdeveniment:hover .poster-pelicula {
  transform: scale(1.1);
}

.gradient-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(to bottom, transparent 40%, var(--crema) 100%);
  pointer-events: none;
}

.contingut-targeta {
  padding: 30px;
}

.contingut-targeta h2 {
  font-family: 'Playfair Display', serif;
  margin: 0 0 15px;
  font-size: 1.4rem;
}

.data { font-weight: 500; color: var(--burgundy); margin-bottom: 5px; text-transform: capitalize; }
.recinte { opacity: 0.7; font-size: 0.9rem; margin-bottom: 20px; }
.preu { font-weight: 800; font-size: 1.1rem; margin-bottom: 25px; }

.boto-reserva {
  display: block;
  text-align: center;
  background: var(--daurat);
  color: var(--granat);
  padding: 15px;
  text-decoration: none;
  font-weight: 800;
  border-radius: 6px;
  letter-spacing: 1px;
}

.peu-portada { margin-top: 100px; opacity: 0.5; display: flex; gap: 15px; align-items: center; }
.link-admin, .link-peu { color: var(--crema); text-decoration: none; font-size: 0.8rem; }
.link-peu:hover, .link-admin:hover { text-decoration: underline; color: var(--daurat); }
.separador { opacity: 0.3; }

.link-consulta-top {
    display: inline-block;
    margin-bottom: 20px;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 2px;
    color: var(--daurat);
    text-decoration: none;
    border: 1px solid var(--daurat);
    padding: 8px 15px;
    border-radius: 4px;
    transition: var(--transicio);
}

.link-consulta-top:hover {
    background: var(--daurat);
    color: var(--granat-profund);
}
</style>
