# Plan: Estratègia d'Implementació de la Reserva Temporitzada via Sockets

## Fase 1: Setup Bases Tecnològic (Arquitectura Mínima)
1. **Frontend Vue3 / Nuxt3:** Creació i configuració de l'assignació d'Stores (Pinia) amb els estats globals i la implementació visual del layout a esdeveniment per dibuixar el mapa SVG interactiu i llistat i caixes. Incorporar les configuració SSR base o client side only al Nuxt a depenència dels socket.
2. **Backend Base:** Posar en arrencada inicial servei Node.js i utilitzar la llibreria clau que és `Socket.IO` a d'alt d'algun servidor simple d'Express per els test initialitzar i compartir port de comunicació. Posarem estructures de Hash Maps simulades en el Backend enlloc de DB des del primer minut per a més velocitat de testatge concurrent de la pràctica inicial de reserva.

## Fase 2: Implementació Backend API Realtime + Temporitzadors de caducitat (Socket.IO + Control Dades Concurrència)
1. Declaració del llistat amb els objectes/seients inicialitzats de "disponible" d'entrada.
2. Codi `on('connection')`. Connexió genèrica i distribució.
3. Creació event callback de Socket principal als llogaters de botó del Vue `intentar_reserva(id_seient, callback_concurrència)`. Amb tractament JS exclusiu sobre memory (A nivell de funció simple sense interferencia de diferents peticions HTTP pel bucle JS de node) s'evalua condicions de buit `estat == 'disponible'`.
   S'implementa funcions per establir estat `"reserva_activa"`.
4. Codificar el flux d'alliberament de Timeout JavaScript genèric `setTimeout(() => {...})`. Emissió sub segons i callback a front en el node mateix.

## Fase 3: Integració Frontend Vue3 Reactivitat (Pinia Store Connect)
1. Creació/integració client Plugin al costat `nuxt.config.js`  o  `plugins/` de les eines de WebSockets pel Node.
2. Integració Store de Pinia des d'arrancament a connectar: Esdevenir Store al canvi i disparar els metalls `socket.on(...)` al connectat de Vue on Mounted o Store Initilization i reflectir valors reactius amb els Refs dins `UI Map` visual de blocs de color a Vue. (Colors: Verd lliure, Groc Reservat extern, Blau Meu Reservat, Gris Venut).
3. Connectar funcions llançades en botó del Nuxt-Vue amb el Event del WebSocket: emissió reserva click a través de botons amb catch i error per gestionar la intercepció i missatges negatius visuals usant algun component "Toaster" notificador del front de cas que estigui fora de joc o hagués algun bloqueig simultani. 

## Fase 4: Validació, Testing Manual de concurrència, Test i Refactoring i CI
1. Crear dos clients / navegadors diferents operant mateixes pestanyes simulador incògnites provant de clicar conjunt al microsegon la "Plaça X23". Només hi entra en acció per un i el bloquejarà ràpid.
2. Afegir Testing unitari sobre components de l'Estat Pinia tal com exigeix els Requisits.
3. Test de integració i de concurrència si pot ser com a Cypress Test Script o de prova com a la documentació OpenSpec Final.

Aquest "Roadmap" permet tenir aïllat tant els canvis cap a Base de dades quan ja no siguin arrays d'exemples i per tant pot avançar de forma autodinàmica.
