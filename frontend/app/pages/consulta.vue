<template>
  <div class="consulta-wrapper">
    <header class="consulta-header">
      <NuxtLink to="/" class="boto-tornar">← TORNAR AL CARTELL</NuxtLink>
      <h1>CONSULTA DE LES VOSTRES ENTRADES</h1>
      <p>Introduïu el correu electrònic per recuperar les vostres localitats.</p>
    </header>

    <main class="consulta-main">
      <div class="panell-cerca card-glass">
        <input 
          v-model="email" 
          type="email" 
          placeholder="exemple@correu.com" 
          @keyup.enter="cercar"
        />
        <button @click="cercar" :disabled="carregant" class="boto-daurat">
          {{ carregant ? 'BUSCANT...' : 'CERCAR ENTRADES' }}
        </button>
      </div>

      <div v-if="entrades.length > 0" class="llistat-entrades">
        <div v-for="venda in entrades" :key="venda.id" class="entrada-card card-glass">
          <div class="info-ticket">
            <div class="esdeveniment-titol">{{ venda.esdeveniment.titol }}</div>
            <div class="detalls">
              <span class="dada">SEIENT: <strong>{{ venda.seient.id_referencia }}</strong></span>
              <span class="dada">DATA: <strong>{{ formatarData(venda.esdeveniment.data_hora) }}</strong></span>
            </div>
            <div class="codi-confirmacio">CODI: {{ venda.codi_confirmacio }}</div>
          </div>
          <div class="qr-real">
            <!-- Generem un QR real basat en el codi de confirmació -->
            <img 
              :src="'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=' + venda.codi_confirmacio" 
              :alt="'QR ' + venda.codi_confirmacio"
              class="qr-imatge"
            />
            <span>TIQUET DIGITAL</span>
          </div>
        </div>
      </div>

      <div v-else-if="cercat && !carregant" class="sense-resultats">
        No s'ha trobat cap entrada vinculada a aquest correu.
      </div>
    </main>
  </div>
</template>

<script setup>
const email = ref('')
const entrades = ref([])
const carregant = ref(false)
const cercat = ref(false)
const config = useRuntimeConfig()

const cercar = async () => {
  if (!email.value) return
  
  carregant.value = true
  cercat.value = true
  
  try {
    const res = await fetch(`${config.public.apiBase}/venda/consulta/${email.value}`)
    entrades.value = await res.json()
  } catch (e) {
    console.error("Error en la consulta", e)
  } finally {
    carregant.value = false
  }
}

const formatarData = (dataStr) => {
  if (!dataStr) return 'Data no disponible'
  // Compatibilitat amb format ISO (substituïm l'espai per 'T' si cal)
  const data = new Date(dataStr.replace(' ', 'T'))
  if (isNaN(data.getTime())) return 'Data pendent'
  
  return data.toLocaleDateString('ca-ES', { 
    day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' 
  })
}
</script>

<style scoped>
.consulta-wrapper {
  min-height: 100vh;
  padding: 80px 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.consulta-header { text-align: center; margin-bottom: 60px; }
.consulta-header h1 { font-size: 2rem; letter-spacing: 3px; margin-bottom: 10px; }
.consulta-header p { opacity: 0.6; font-style: italic; }

.boto-tornar { 
  display: inline-block; margin-bottom: 30px; color: var(--daurat); 
  text-decoration: none; font-size: 0.8rem; font-weight: 800; 
}

.panell-cerca {
  display: flex; gap: 20px; padding: 40px; width: 100%; max-width: 600px;
  margin-bottom: 60px;
}
.panell-cerca input {
  flex: 1; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border);
  padding: 15px; border-radius: 8px; color: white; font-family: inherit;
}

.card-glass {
  background: var(--glass);
  backdrop-filter: blur(20px);
  border: 1px solid var(--glass-border);
  border-radius: 20px;
  box-shadow: var(--ombra-premium);
}

.llistat-entrades {
  display: grid;
  grid-template-columns: 1fr;
  gap: 30px;
  width: 100%;
  max-width: 800px;
}

.entrada-card {
  display: flex;
  padding: 30px;
  justify-content: space-between;
  align-items: center;
  transition: var(--transicio);
}
.entrada-card:hover { transform: scale(1.02); background: rgba(255,255,255,0.05); }

.info-ticket .esdeveniment-titol { 
  font-family: 'Playfair Display', serif; 
  font-size: 1.5rem; color: var(--daurat); margin-bottom: 15px; 
}

.detalls { display: flex; gap: 30px; margin-bottom: 20px; font-size: 0.9rem; }
.dada strong { color: var(--daurat-lluminos); }

.codi-confirmacio {
  font-family: monospace; font-size: 1.1rem; color: var(--daurat);
  letter-spacing: 2px; border: 1px dashed var(--daurat);
  padding: 10px 20px; display: inline-block;
}

.qr-real { text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px; }
.qr-imatge {
  width: 100px;
  height: 100px;
  background: white;
  padding: 5px;
  border-radius: 4px;
  box-shadow: 0 0 15px rgba(255,255,255,0.2);
}
.qr-real span { font-size: 0.6rem; font-weight: 800; opacity: 0.5; letter-spacing: 2px; }

.boto-daurat {
  background: var(--daurat); color: var(--granat-profund);
  border: none; padding: 15px 30px; border-radius: 8px;
  font-weight: 800; cursor: pointer; transition: var(--transicio);
}
.boto-daurat:hover { background: var(--daurat-lluminos); transform: translateY(-2px); }
.boto-daurat:disabled { opacity: 0.5; cursor: not-allowed; }

.sense-resultats { opacity: 0.6; font-style: italic; margin-top: 40px; }
</style>
