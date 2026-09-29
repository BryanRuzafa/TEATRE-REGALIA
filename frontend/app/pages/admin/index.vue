<template>
  <div class="admin-wrapper">
    <header class="admin-header">
      <div class="brand">
        <NuxtLink to="/" class="boto-tornar">← TORNAR A LA CARTELLERA</NuxtLink>
        <h1>TAULER DE CONTROL <span class="daurat">REAL-TIME</span></h1>
      </div>
      <div class="status-pill" :class="{ connected: connected }">
        <span class="dot"></span>
        {{ connected ? 'SERVIDOR ACTIU' : 'DESCONNECTAT' }}
      </div>
    </header>

    <main class="admin-content">
      <!-- Estadístiques Ràpides (Glassmorphism) -->
      <section class="stats-grid">
        <div class="card-glass">
          <span class="label">USUARIS EN LÍNIA</span>
          <div class="value">{{ usuarisEnLinia }}</div>
          <div class="sub-label">Connexions Socket.IO actives</div>
        </div>
        <div class="card-glass">
          <span class="label">RESERVES ACTIVES</span>
          <div class="value">{{ totalReserves }}</div>
          <div class="sub-label">Bloquejos temporals</div>
        </div>
        <div class="card-glass gold">
          <span class="label">RECAPTACIÓ TOTAL</span>
          <div class="value">{{ recaptacio }}€</div>
          <div class="sub-label">Vendes confirmades</div>
        </div>
      </section>

      <!-- Panell de Creació / Edició (Requeriment 3.2.1) -->
      <section class="admin-actions">
        <div class="card-glass creation-card" :class="{ 'editant-mode': editantId }">
          <h3>{{ editantId ? 'EDITAR ESDEVENIMENT' : 'CREAR NOU ESDEVENIMENT' }}</h3>
          <form @submit.prevent="guardarEsdeveniment" class="form-luxe">
            <div class="row">
              <div class="camp">
                <label>Títol de l'obra</label>
                <input v-model="nouEsdeveniment.titol" type="text" placeholder="Ex: El Fantasma de l'Òpera" required />
              </div>
              <div class="camp">
                <label>Nom del Recinte</label>
                <input v-model="nouEsdeveniment.recinte" type="text" placeholder="Ex: Teatre Principal" required />
              </div>
            </div>
            <div class="row">
              <div class="camp">
                <label>Data i Hora</label>
                <input v-model="nouEsdeveniment.data_hora" type="datetime-local" required />
              </div>
              <div class="camp">
                <label>Preu Base (€)</label>
                <input v-model="nouEsdeveniment.preu_base" type="number" step="0.01" placeholder="25.00" required />
              </div>
              <div class="accions-formulari">
                <button type="submit" class="boto-confirmar-admin" :disabled="enviant">
                  {{ enviant ? 'GUARDANT...' : (editantId ? 'ACTUALITZAR' : 'REGISTRAR ESDEVENIMENT') }}
                </button>
                <button v-if="editantId" type="button" class="boto-cancelar" @click="cancel·larEdicio">
                  CANCEL·LAR
                </button>
              </div>
            </div>
          </form>
          <p v-if="errorCreacio" class="msg error">{{ errorCreacio }}</p>
          <p v-if="exitCreacio" class="msg exit">{{ editantId ? '¡Esdeveniment actualitzat!' : '¡Esdeveniment creat amb èxit!' }}</p>
        </div>
      </section>

      <!-- Panell d'Esdeveniments -->
      <section class="events-section">
        <div class="section-header">
            <h2>ESDEVENIMENTS ACTIUS</h2>
            <button class="boto-refresc" @click="carregarEstadistiques">
                <span class="icon">↻</span> ACTUALITZAR DADES
            </button>
        </div>
        
        <div class="taula-luxe-wrapper">
            <table class="taula-luxe">
            <thead>
                <tr>
                <th>ESDEVENIMENT</th>
                <th>VENDES</th>
                <th>RECAPTACIÓ</th>
                <th>ESTAT</th>
                <th>ACCIONS</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="e in esdeveniments" :key="e.id">
                <td class="titol">{{ e.titol }}</td>
                <td>{{ e.vendes_count || 0 }}</td>
                <td>{{ (e.vendes_count || 0) * e.preu_base }}€</td>
                <td><span class="badge" :class="e.vendes_count > 0 ? 'active' : 'idle'">LIVE</span></td>
                <td>
                    <div class="accions-taula">
                        <button class="boto-editar" @click="prepararEdicio(e)">EDITAR</button>
                        <button class="boto-eliminar" @click="eliminarEsdeveniment(e.id)">ELIMINAR</button>
                    </div>
                </td>
                </tr>
            </tbody>
            </table>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { io } from 'socket.io-client'

const connected = ref(false)
const usuarisEnLinia = ref(0)
const totalReserves = ref(0)
const recaptacio = ref(0)
const esdeveniments = ref([])
const enviant = ref(false)
const errorCreacio = ref('')
const exitCreacio = ref(false)
const editantId = ref(null)

const nouEsdeveniment = reactive({
  titol: '',
  recinte: '',
  data_hora: '',
  preu_base: 25.00
})

let socket = null
const config = useRuntimeConfig()

// Detecció intel·ligent del Socket (port 3001)
const obtenirHostSocket = () => {
  if (typeof window === 'undefined') return ''
  return `http://${window.location.hostname}:3001`
}

const socketHost = obtenirHostSocket()

const carregarEstadistiques = async () => {
  try {
    const res = await fetch(`${config.public.apiBase}/admin/estadistiques`, {
      headers: { 'Accept': 'application/json' }
    })
    const data = await res.json()
    esdeveniments.value = data.detall_esdeveniments
    recaptacio.value = data.resum_global.recaptacio_total
  } catch (e) {
    console.error("Error carregant dades del backend", e)
  }
}

const prepararEdicio = (e) => {
    editantId.value = e.id
    nouEsdeveniment.titol = e.titol
    nouEsdeveniment.recinte = e.recinte
    nouEsdeveniment.preu_base = e.preu_base
    // Format de data per al local-datetime input (YYYY-MM-DDTHH:mm)
    if (e.data_hora) {
        nouEsdeveniment.data_hora = new Date(e.data_hora).toISOString().slice(0, 16)
    }
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const cancel·larEdicio = () => {
    editantId.value = null
    nouEsdeveniment.titol = ''
    nouEsdeveniment.recinte = ''
    nouEsdeveniment.data_hora = ''
    nouEsdeveniment.preu_base = 25.00
}

const guardarEsdeveniment = async () => {
  enviant.value = true
  errorCreacio.value = ''
  exitCreacio.value = false

  const url = editantId.value 
    ? `${config.public.apiBase}/admin/esdeveniments/${editantId.value}`
    : `${config.public.apiBase}/admin/esdeveniments`
    
  const method = editantId.value ? 'PUT' : 'POST'

  try {
    const res = await fetch(url, {
      method: method,
      headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(nouEsdeveniment)
    })

    if (!res.ok) {
       const errData = await res.json();
       throw new Error(errData.message || "Error en la transacció");
    }

    exitCreacio.value = true
    if (!editantId.value) {
        nouEsdeveniment.titol = ''
        nouEsdeveniment.recinte = ''
        nouEsdeveniment.data_hora = ''
    } else {
        cancel·larEdicio()
    }
    
    await carregarEstadistiques()
    
    setTimeout(() => exitCreacio.value = false, 3000)
  } catch (e) {
    errorCreacio.value = "No s'ha pogut processar la petició. Revisa la connexió."
  } finally {
    enviant.value = false
  }
}

const eliminarEsdeveniment = async (id) => {
  if (!confirm("Estàs segur que vols eliminar aquest esdeveniment? Totes les dades i vendes associades s'esborraran permanentment.")) return;

  try {
    const res = await fetch(`${config.public.apiBase}/admin/esdeveniments/${id}`, {
      method: 'DELETE',
      headers: { 'Accept': 'application/json' }
    })
    
    if (res.ok) {
        await carregarEstadistiques()
    } else {
        alert("No s'ha pogut eliminar l'esdeveniment.")
    }
  } catch (e) {
    console.error("Error eliminant esdeveniment", e)
  }
}

onMounted(() => {
  carregarEstadistiques()

  socket = io(socketHost)
  
  socket.on('connect', () => {
    connected.value = true
  })

  socket.on('actualitzacio_admin', (dades) => {
    if (dades.usuaris !== undefined) usuarisEnLinia.value = dades.usuaris
    if (dades.total_reserves !== undefined) totalReserves.value = dades.total_reserves
    if (dades.refresh_stats) carregarEstadistiques()
  })

  socket.on('disconnect', () => {
    connected.value = false
  })
})

onUnmounted(() => {
  if (socket) socket.disconnect()
})
</script>

<style scoped>
.admin-wrapper {
  min-height: 100vh;
  padding: 40px;
  background: var(--granat-profund);
}

.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 50px;
  border-bottom: 1px solid var(--glass-border);
  padding-bottom: 20px;
}

.boto-tornar {
    display: inline-block;
    color: var(--daurat);
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 800;
    margin-bottom: 10px;
    opacity: 0.6;
    transition: var(--transicio);
}

.boto-tornar:hover {
    opacity: 1;
    transform: translateX(-5px);
}

.brand h1 {
    font-family: 'Playfair Display', serif;
    font-size: 1.8rem;
    letter-spacing: 2px;
    margin: 0;
}

.status-pill {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255,255,255,0.05);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 1px;
    border: 1px solid var(--glass-border);
    color: #f87171;
}

.status-pill.connected {
    color: #5ce0db;
    border-color: rgba(92, 224, 219, 0.3);
}

.status-pill .dot {
    width: 8px;
    height: 8px;
    background: #f87171;
    border-radius: 50%;
    box-shadow: 0 0 10px #f87171;
}

.status-pill.connected .dot {
    background: #5ce0db;
    box-shadow: 0 0 10px #5ce0db;
}

.admin-content {
    max-width: 1200px;
    margin: 0 auto;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-bottom: 50px;
}

.card-glass {
    background: var(--glass);
    backdrop-filter: blur(12px);
    border: 1px solid var(--glass-border);
    padding: 30px;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-shadow: var(--ombra-premium);
}

.card-glass.gold {
    border-bottom: 3px solid var(--daurat);
}

.card-glass .label {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 2px;
    opacity: 0.6;
    margin-bottom: 10px;
}

.card-glass .value {
    font-size: 2.4rem;
    font-family: 'Playfair Display', serif;
    font-weight: 800;
}

.card-glass .sub-label {
    font-size: 0.8rem;
    opacity: 0.5;
    margin-top: 5px;
}

.taula-luxe-wrapper {
    background: var(--glass);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--ombra-premium);
}

.taula-luxe {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.taula-luxe th {
    padding: 20px;
    background: rgba(255,255,255,0.03);
    font-size: 0.75rem;
    font-weight: 800;
    letter-spacing: 1px;
    opacity: 0.7;
    border-bottom: 1px solid var(--glass-border);
}

.taula-luxe td {
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    font-size: 0.9rem;
}

.taula-luxe tr:last-child td { border-bottom: none; }

.taula-luxe .titol {
    font-family: 'Playfair Display', serif;
    font-weight: 800;
    font-size: 1.1rem;
    color: var(--daurat-lluminos);
}

.badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.6rem;
    font-weight: 900;
    letter-spacing: 1px;
}

.badge.active { background: rgba(92, 224, 219, 0.2); color: #5ce0db; border: 1px solid #5ce0db; }
.badge.idle { background: rgba(255, 255, 255, 0.1); color: white; opacity: 0.5; }

.events-section { margin-top: 40px; }

.admin-actions {
    margin-bottom: 40px;
}

.creation-card {
    text-align: left;
    border-bottom: 4px solid var(--daurat);
}

.creation-card h3 {
    margin: 0 0 25px;
    font-family: 'Playfair Display', serif;
    letter-spacing: 1px;
    color: var(--daurat);
}

.form-luxe .row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    align-items: flex-end;
}

.camp {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.camp label {
    font-size: 0.7rem;
    font-weight: 800;
    opacity: 0.7;
    letter-spacing: 1px;
}

.camp input {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--glass-border);
    padding: 12px;
    border-radius: 4px;
    color: white;
    font-family: 'Inter', sans-serif;
}

.camp input:focus {
    border-color: var(--daurat);
    outline: none;
    background: rgba(255,255,255,0.1);
}

.boto-confirmar-admin {
    background: var(--daurat);
    color: var(--granat-profund);
    border: none;
    padding: 14px 25px;
    font-weight: 800;
    cursor: pointer;
    border-radius: 4px;
    letter-spacing: 1px;
}

.boto-confirmar-admin:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.msg {
    margin-top: 15px;
    font-size: 0.85rem;
    font-weight: 600;
}
.msg.error { color: #f87171; }
.msg.exit { color: #5ce0db; }

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.section-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem;
    letter-spacing: 1px;
    color: var(--crema);
}

.boto-refresc {
    background: var(--glass);
    color: var(--crema);
    border: 1px solid var(--glass-border);
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
    transition: var(--transicio);
}

.boto-refresc:hover {
    background: rgba(255,255,255,0.1);
    border-color: var(--daurat);
}

.boto-eliminar {
    background: transparent;
    color: #f87171;
    border: 1px solid #f87171;
    padding: 6px 12px;
    font-size: 0.7rem;
    font-weight: 800;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.boto-eliminar:hover {
    background: #f87171;
    color: var(--granat-profund);
}

.accions-taula {
    display: flex;
    gap: 10px;
}

.boto-editar {
    background: transparent;
    color: var(--daurat);
    border: 1px solid var(--daurat);
    padding: 6px 12px;
    font-size: 0.7rem;
    font-weight: 800;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.boto-editar:hover {
    background: var(--daurat);
    color: var(--granat-profund);
}

.accions-formulari {
    display: flex;
    gap: 15px;
    align-items: center;
}

.boto-cancelar {
    background: rgba(255,255,255,0.1);
    color: white;
    border: 1px solid var(--glass-border);
    padding: 14px 25px;
    font-weight: 800;
    cursor: pointer;
    border-radius: 4px;
    letter-spacing: 1px;
}

.editant-mode {
    border-bottom: 4px solid #5ce0db !important;
}

.editant-mode h3 {
    color: #5ce0db !important;
}
</style>
