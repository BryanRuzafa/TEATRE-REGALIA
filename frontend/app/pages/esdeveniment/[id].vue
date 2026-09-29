<template>
  <div class="sala-detall">
    <header class="header-sala">
      <NuxtLink to="/" class="boto-tornar">← TORNAR AL CARTELL</NuxtLink>
      <div v-if="esdeveniment" class="info-esdeveniment">
        <h1>{{ esdeveniment.titol }}</h1>
        <p>{{ esdeveniment.recinte }} | {{ dadesStore.usuarisEnLinia }} usuaris connectats</p>
      </div>
      <div class="div-usuari">
        ID: {{ dadesStore.sessioClient }}
        <span class="estat-connexió" :class="{ ok: dadesStore.connectat }">
          {{ dadesStore.connectat ? '●' : '○' }}
        </span>
      </div>
    </header>

    <main class="contingut-sala">
      <div class="notificacions-area">
        <Transition name="fade">
          <div v-if="dadesStore.missatgeError" class="notif error">{{ dadesStore.missatgeError }}</div>
        </Transition>
        <Transition name="fade">
          <div v-if="dadesStore.missatgeExit" class="notif exit">{{ dadesStore.missatgeExit }}</div>
        </Transition>
      </div>

      <template v-if="dadesStore.connectat">
        <MapaSeients 
          :seients="Object.values(dadesStore.seients)"
          :sessio-client="dadesStore.sessioClient"
          @reservar="solicitar"
        />

        <!-- Formulari de Checkout Formal (Requeriment 3.1.4) Reubicat abaix -->
        <Transition name="invitacio">
          <div v-if="butaquesSeleccionades.length > 0" class="panell-reservat">
               <div class="formulari-comprador">
                  <h3>Formalitzar Compra</h3>
                  <p>Heu seleccionat {{ butaquesSeleccionades.length }} butaques regalies.</p>
                  <div class="camps">
                    <input v-model="dadesStore.dadesComprador.nom" type="text" placeholder="El vostre nom complet" />
                    <input v-model="dadesStore.dadesComprador.email" type="email" placeholder="Correu electrònic" />
                  </div>
                  <button @click="dadesStore.processarCompraFinal" class="boto-confirmar">
                    CONFIRMAR I PAGAR
                  </button>
               </div>
          </div>
        </Transition>
      </template>
      
      <div v-else class="carregant">
        Establint connexió amb el Teatre...
      </div>
    </main>
  </div>
</template>

<script setup>
import { usTaulerSeientsStore } from '~/stores/taulerSeients.js'
import MapaSeients from '~/components/MapaSeients.vue'

const route = useRoute()
const dadesStore = usTaulerSeientsStore()

const config = useRuntimeConfig()

// Carreguem dades de l'esdeveniment via SSR/Fetch
const { data: esdeveniment } = await useFetch(`${config.public.apiBase}/esdeveniments/${route.params.id}`);

onMounted(() => {
  if (esdeveniment.value) {
    dadesStore.iniciarConnexioSocket(esdeveniment.value.id, esdeveniment.value.seients);
  }
})

const butaquesSeleccionades = computed(() => {
  return Object.values(dadesStore.seients).filter(s => s.reservat_per === dadesStore.sessioClient && s.estat === 'reservat')
})

const solicitar = (id) => {
  dadesStore.solicitarBloqueigSeient(id);
}
</script>

<style scoped>
.sala-detall { min-height: 100vh; display: flex; flex-direction: column; }

.header-sala {
  display: flex; justify-content: space-between; align-items: center;
  padding: 20px 40px; border-bottom: 1px solid var(--daurat);
  background: rgba(0,0,0,0.3);
}

.boto-tornar { color: var(--daurat); text-decoration: none; font-size: 0.8rem; font-weight: 800; }

.info-esdeveniment { text-align: center; }
.info-esdeveniment h1 { margin: 0; font-family: 'Playfair Display', serif; font-size: 1.5rem; }
.info-esdeveniment p { margin: 5px 0 0; font-size: 0.8rem; opacity: 0.6; }

.div-usuari { font-size: 0.7rem; font-family: monospace; color: var(--daurat); }
.estat-connexió.ok { color: #5ce0db; }

.contingut-sala { flex: 1; display: flex; flex-direction: column; align-items: center; padding-top: 40px; }

.notificacions-area { position: fixed; top: 100px; right: 20px; z-index: 100; }
.notif { padding: 15px 25px; border-radius: 4px; border: 1px solid var(--daurat); background: var(--crema); color: var(--granat); font-size: 0.9rem; }
.notif.error { background: #f87171; color: white; border: none; }

.panell-reservat { 
  background: var(--crema); color: var(--granat); 
  padding: 25px 40px; border-radius: 8px; border: 2px solid var(--daurat);
  box-shadow: 0 20px 60px var(--ombra); margin-bottom: 40px;
}
.formulari-comprador h3 { margin: 0 0 10px; font-family: 'Playfair Display', serif; }
.formulari-comprador p { font-size: 0.85rem; margin-bottom: 20px; font-style: italic; }

.camps { display: flex; gap: 15px; margin-bottom: 20px; }
.camps input { 
    padding: 12px; border: 1px solid #ccc; border-radius: 4px; flex: 1; 
    font-family: 'Inter', sans-serif;
}

.boto-confirmar {
  width: 100%; background: var(--granat); color: var(--crema);
  border: 1px solid var(--daurat); padding: 15px; font-weight: 800;
  cursor: pointer; letter-spacing: 2px;
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
