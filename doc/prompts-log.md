# Registre de Prompts i Traçabilitat OpenSpec

## Funcionalitat: Reserva temporal de seients en temps real i bloqueig concurrent

Aquest document manté la traçabilitat sol·licitada pel projecte sobre la interacció amb IA.

### 1. Definició de la funcionalitat (OpenSpec: propose)
**Prompt 1 (generació de l'especificació bàsica):**
"Considerant que el projecte és una plataforma de venda d'entrades on els usuaris poden seleccionar els mateixos seients simultàniament, utilitzaré Socket.IO i Pinia per gestionar una reserva amb temporitzador temporal de 3-5 minuts. Redacta l'OpenSpec d'aquesta feature."

### 4. Implementació de la Migració a Socket.IO (Execució Final)
**Prompt 2 (Migració de Reverb a Socket.IO):**
"Com que el requeriment obligatori del TR és Socket.IO, crea un servidor de Node.js independent al port 3001. Implementa la lògica de concurrència en memòria per als seients, amb temporitzadors de 3 minuts. Adapta el frontend de Nuxt 3 per connectar-se a aquest nou servidor i modularitza els components de la vista de la sala."

### 6. Redisseny Classic Luxury i Compliment de Requeriments (Execució Final)
**Prompt 3:**
"Millora el disseny per fer-lo Wow Aesthetics i aplica un estil Classic Luxury de cinema de luxe (vermell i daurat). A més, assegura't que el projecte compleix TOTS els requeriments del TR: part administrativa, multiesdeveniment, flux de compra amb dades personals i documentació tècnica."

### 8. Millora de l'Aparell Visual i Nous Mòduls
- **Objectiu:** Implementar l'estètica "Rich Aesthetics" demanada pel tutor i completar la plataforma amb els mòduls d'administració i consulta.
- **Accions realitzades:**
    1. **Sistema de Disseny Global:** S'han definit variables de color premium (granats, daurats i glassmorphism) i tipografies modernes (Outfit i Playfair Display).
    2. **Redisseny de Components:** Les butaques i la sala han estat dotades de gradients, ombres profundes i animacions de transició fluides.
    3. **Panell d'Administració:** S'ha creat la vista `/admin` que consumeix dades en temps real de Socket.IO per monitorar l'activitat.
    4. **Consulta d'Entrades:** S'ha implementat la vista `/consulta` per recuperar historial de compres.
- **Resultat:** L'aplicació ara presenta un aspecte professional, immersiu i compleix amb tots els punts de l'especificació del TR.

## Valoració Crítica de l'ús de la IA
L'ús de l'agent d'IA ha permès accelerar el desenvolupament del frontend reactiu i la gestió de concurrència. La capacitat de l'IA per interpretar l'especificació OpenSpec i traduir-la en components de Vue 3 amb Composition API ha estat clau. S'ha hagut de corregir manualment algunes referències a taules de la base de dades per mantenir el català estricte, però en general, l'IA ha seguit les directrius de disseny "Classic Luxury" amb gran precisió.

