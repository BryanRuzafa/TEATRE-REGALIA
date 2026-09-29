<template>
  <div 
    class="butaca-classic"
    :class="[estat, esTeva ? 'es-teva' : '']"
    @click="$emit('seleccionar', id)"
  >
    <!-- Respatller de Vellut -->
    <div class="respatller-vellut"></div>
    
    <!-- Base del seient amb textura -->
    <div class="base-vellut">
      <span class="id-daurat">{{ id }}</span>
    </div>

    <!-- Braços de fusta de luxe -->
    <div class="braç-fusta esquerre"></div>
    <div class="braç-fusta dret"></div>
    
    <!-- Ombra ambiental interior -->
    <div class="ombra-soft"></div>
  </div>
</template>

<script setup>
defineProps({
  id: [String, Number],
  estat: String,
  esTeva: Boolean
});
defineEmits(['seleccionar']);
</script>

<style scoped>
.butaca-classic {
  position: relative;
  width: 58px;
  height: 62px;
  perspective: 600px;
  cursor: pointer;
  transition: var(--transicio);
  transform-style: preserve-3d;
}

.respatller-vellut {
  position: absolute;
  top: 0; left: 4px; right: 4px; height: 44px;
  background: linear-gradient(135deg, var(--burgundy), var(--granat));
  border-radius: 12px 12px 4px 4px;
  box-shadow: 
    inset 0 10px 20px rgba(0,0,0,0.5),
    0 4px 10px rgba(0,0,0,0.3);
  z-index: 1;
  border: 1px solid rgba(212, 175, 55, 0.1);
}

.base-vellut {
  position: absolute;
  bottom: 2px; left: -2px; right: -2px; height: 32px;
  background: linear-gradient(to bottom, var(--burgundy), var(--granat));
  border-radius: 6px;
  display: flex;
  align-items: center; justify-content: center;
  z-index: 3;
  box-shadow: 
    0 10px 20px rgba(0,0,0,0.4), 
    inset 0 1px 4px rgba(255,255,255,0.1);
  border-bottom: 4px solid var(--granat-profund);
  transition: var(--transicio);
}

.braç-fusta {
  position: absolute;
  bottom: 8px; width: 12px; height: 42px;
  background: linear-gradient(to right, #3d1c1a, #5c2c2a, #3d1c1a);
  border-radius: 4px;
  z-index: 4;
  box-shadow: 2px 4px 10px rgba(0,0,0,0.5);
  border: 1px solid rgba(0,0,0,0.3);
}

.braç-fusta.esquerre { left: -8px; }
.braç-fusta.dret { right: -8px; }

.id-daurat {
  font-size: 0.75rem;
  font-weight: 800;
  color: var(--daurat-lluminos);
  text-shadow: 0 2px 4px rgba(0,0,0,0.9);
  letter-spacing: 0.5px;
  opacity: 0.8;
}

/* ESTATS */

/* Disponible */
.butaca-classic.disponible:hover { 
  transform: translateY(-10px) scale(1.1) rotateX(5deg); 
  z-index: 20;
}
.butaca-classic.disponible:hover .base-vellut { 
  background: var(--burgundy); 
  box-shadow: 0 15px 30px rgba(139, 21, 34, 0.4);
}

/* Reservat (Ocupat per un altre) */
.butaca-classic.reservat { pointer-events: none; }
.butaca-classic.reservat .base-vellut, 
.butaca-classic.reservat .respatller-vellut { 
  background: #2a0505; 
  filter: grayscale(0.5) brightness(0.6);
}
.butaca-classic.reservat .id-daurat { opacity: 0.2; }

/* Seleccionat per MI */
.butaca-classic.es-teva { 
  transform: translateY(-5px) scale(1.15) translateZ(30px); 
  z-index: 50; 
}
.butaca-classic.es-teva .base-vellut { 
  background: linear-gradient(135deg, var(--daurat), var(--daurat-lluminos)); 
  box-shadow: 
    0 0 30px rgba(212, 175, 55, 0.4), 
    inset 0 2px 10px rgba(255,255,255,0.4); 
  border-bottom: 4px solid #856404;
}
.butaca-classic.es-teva .id-daurat { 
  color: var(--granat-profund); 
  opacity: 1; 
  text-shadow: none; 
}

/* Venut */
.butaca-classic.venut { 
  pointer-events: none; 
  opacity: 0.15; 
  filter: grayscale(1) contrast(0.5);
}

.ombra-soft {
  position: absolute;
  bottom: -8px; left: 0; right: 0; height: 10px;
  background: radial-gradient(ellipse at center, rgba(0,0,0,0.8) 0%, transparent 70%);
  z-index: 0;
  transform: translateZ(-10px);
}
</style>
