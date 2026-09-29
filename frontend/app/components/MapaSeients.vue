<template>
  <div class="sala-classic">
    <!-- Pantalla d'Època amb Marc Daurat -->
    <div class="pantalla-luxe">
       <div class="marc-daurat"></div>
       <div class="superficie-textura">
         <span class="text-pantalla">GRAN ESTRENA</span>
       </div>
       <div class="llum-ambiental"></div>
    </div>
    
    <div class="escenari-butaques">
      <div class="quadrícula-luxe">
        <Butaca 
          v-for="seient in seients" 
          :key="seient.id"
          :id="seient.id"
          :estat="seient.estat"
          :es-teva="seient.reservat_per === sessioClient"
          @seleccionar="$emit('reservar', seient.id)"
        />
      </div>
    </div>

    <!-- Llegenda Aristocràtica -->
    <div class="llegenda-classic">
      <div class="item-llegenda"><span class="mostra vellut"></span> Disponible</div>
      <div class="item-llegenda"><span class="mostra ocupat"></span> Ocupat</div>
      <div class="item-llegenda"><span class="mostra seleccio"></span> Seleccionat</div>
    </div>
  </div>
</template>

<script setup>
import Butaca from './Butaca.vue';
defineProps({
  seients: Array,
  sessioClient: String
});
defineEmits(['reservar']);
</script>

<style scoped>
.sala-classic {
  width: 100%;
  display: flex;
  flex-direction: column;
  padding: 80px 40px;
  align-items: center;
  background: radial-gradient(circle at center bottom, rgba(139, 21, 34, 0.1) 0%, transparent 80%);
}

.pantalla-luxe {
  position: relative;
  width: 90%;
  max-width: 1000px;
  height: 60px;
  margin-bottom: 120px;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10;
}

.marc-daurat {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  border-top: 6px solid var(--daurat);
  border-left: 30px solid transparent;
  border-right: 30px solid transparent;
  filter: drop-shadow(0 0 30px rgba(212, 175, 55, 0.5));
  border-radius: 50% / 100% 100% 0 0;
}

.superficie-textura {
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, #fff, #ddd);
  border-radius: 50% / 100% 100% 0 0;
  display: flex;
  align-items: center; justify-content: center;
  box-shadow: 0 0 100px rgba(255,255,255,0.2);
  overflow: hidden;
}

.text-pantalla {
  font-family: 'Playfair Display', serif;
  letter-spacing: 20px;
  font-size: 1rem;
  font-weight: 800;
  color: var(--granat-profund);
  opacity: 0.8;
  animation: parpelleig 5s infinite ease-in-out;
}

@keyframes parpelleig {
  0%, 100% { opacity: 0.8; }
  50% { opacity: 0.4; }
}

.llum-ambiental {
  position: absolute;
  top: 20px; left: -10%; right: -10%; height: 400px;
  background: radial-gradient(ellipse at top, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
  pointer-events: none;
  z-index: -1;
}

.escenari-butaques {
  perspective: 2000px;
  width: 100%;
  display: flex;
  justify-content: center;
  padding: 40px 0;
}

.quadrícula-luxe {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 50px 80px;
  transform: rotateX(25deg);
  transform-style: preserve-3d;
}

.llegenda-classic {
  display: flex;
  gap: 50px;
  background: var(--glass);
  backdrop-filter: blur(20px);
  padding: 25px 60px;
  border-radius: 60px;
  border: 1px solid var(--glass-border);
  margin-top: 120px;
  box-shadow: var(--ombra-premium);
  transition: var(--transicio);
}

.llegenda-classic:hover {
  background: rgba(255, 255, 255, 0.08);
  transform: translateY(-5px);
}

.item-llegenda {
  display: flex;
  align-items: center;
  gap: 15px;
  font-size: 0.95rem;
  color: var(--crema);
  font-weight: 300;
  letter-spacing: 1px;
}

.mostra { 
  width: 16px; height: 16px; border-radius: 50%; 
  box-shadow: 0 0 10px rgba(0,0,0,0.5);
}
.mostra.vellut { background: var(--burgundy); border: 1px solid var(--daurat); }
.mostra.ocupat { background: #2a0505; }
.mostra.seleccio { background: var(--daurat); box-shadow: 0 0 20px var(--daurat); }

@media (max-width: 800px) {
  .quadrícula-luxe { grid-template-columns: repeat(3, 1fr); gap: 30px; }
  .llegenda-classic { flex-direction: column; gap: 15px; padding: 20px 40px; }
}
</style>
