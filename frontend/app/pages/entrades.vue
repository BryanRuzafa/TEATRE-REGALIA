<template>
  <div class="consulta-entrades">
    <header class="capçalera-luxe">
      <NuxtLink to="/" class="boto-tornar">← TORNAR AL TEATRE</NuxtLink>
      <h1 class="titol-classic">LES MEVES <span class="destacat-daurat">ENTRADES</span></h1>
    </header>

    <main class="contingut-consulta">
      <div class="cercador-luxe">
        <p>Introduïu el vostre correu electrònic per recuperar els vostres tiquets regalies.</p>
        <div class="camp-cerca">
          <input v-model="emailCerca" type="email" placeholder="correu@exemple.com" @keyup.enter="consultar" />
          <button @click="consultar" :disabled="carregant">CONSULTAR</button>
        </div>
      </div>

      <div v-if="vendes.length > 0" class="llistat-tiquets">
         <div v-for="v in vendes" :key="v.id" class="tiquet-fisic">
            <div class="bord-decoratiu">
              <div class="info-tiquet">
                <h2>{{ v.esdeveniment.titol }}</h2>
                <p class="data">{{ formulariData(v.esdeveniment.data_hora) }}</p>
                <div class="detall-butaca">
                  <span class="label">BUTACA:</span>
                  <span class="valor">{{ v.seient.id_referencia }}</span>
                </div>
                <div class="codi-qr-simulat">
                  <div class="codi-text">{{ v.codi_confirmacio }}</div>
                  <p>CODI DE VALIDACIÓ</p>
                </div>
              </div>
              <div class="comprador-peu">
                Pels senyors: {{ v.nom_comprador }}
              </div>
            </div>
         </div>
      </div>

      <div v-else-if="haBuscat" class="no-resultats">
        No s'han trobat entrades associades a aquest correu.
      </div>
    </main>
  </div>
</template>

<script setup>
const emailCerca = ref('')
const vendes = ref([])
const carregant = ref(false)
const haBuscat = ref(false)
const config = useRuntimeConfig()

const consultar = async () => {
  if (!emailCerca.value) return;
  carregant.value = true;

  try {
    const res = await fetch(`${config.public.apiBase}/venda/consulta/${emailCerca.value}`);
    vendes.value = await res.json();
    haBuscat.value = true;
  } catch (e) {
    console.error(e);
  } finally {
    carregant.value = false;
  }
}

const formulariData = (dataStr) => {
  return new Date(dataStr).toLocaleString('ca-ES', { 
    weekday: 'short', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' 
  });
}
</script>

<style scoped>
.consulta-entrades { min-height: 100vh; }

.contingut-consulta {
  max-width: 900px; margin: 0 auto; padding: 60px 20px;
}

.cercador-luxe {
  background: var(--crema); color: var(--granat);
  padding: 40px; border-radius: 8px; border: 2px solid var(--daurat);
  text-align: center; margin-bottom: 60px;
}
.cercador-luxe p { margin-bottom: 25px; font-style: italic; opacity: 0.8; }

.camp-cerca { display: flex; gap: 10px; max-width: 500px; margin: 0 auto; }
.camp-cerca input { flex: 1; padding: 15px; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; }
.camp-cerca button { 
    padding: 0 30px; background: var(--granat); color: var(--crema); border: none; 
    font-weight: 800; cursor: pointer; border-radius: 4px;
}

.llistat-tiquets { display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 30px; }

.tiquet-fisic {
  background: #fff; color: #333; padding: 5px; border-radius: 4px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.5);
}
.bord-decoratiu { border: 2px dashed var(--daurat); padding: 25px; position: relative; }

.info-tiquet h2 { font-family: 'Playfair Display', serif; margin: 0 0 10px; font-size: 1.3rem; color: var(--granat); }
.data { font-weight: 800; font-size: 0.9rem; margin-bottom: 20px; text-transform: uppercase; color: var(--burgundy); }

.detall-butaca { margin-bottom: 30px; border-top: 1px solid #eee; padding-top: 15px; }
.detall-butaca .label { font-size: 0.7rem; opacity: 0.6; margin-right: 10px; }
.detall-butaca .valor { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 800; color: var(--daurat); }

.codi-qr-simulat { text-align: right; }
.codi-text { font-family: monospace; font-size: 1.5rem; font-weight: 800; letter-spacing: 5px; color: #000; }
.codi-qr-simulat p { font-size: 0.6rem; opacity: 0.5; margin: 0; }

.comprador-peu { margin-top: 20px; font-size: 0.8rem; font-style: italic; opacity: 0.7; border-top: 1px solid #eee; padding-top: 10px; }

.no-resultats { text-align: center; opacity: 0.5; font-style: italic; }
</style>
