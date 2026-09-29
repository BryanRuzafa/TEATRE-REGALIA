# Resum del Projecte - TR 2026

**Curs:** 2DAW 2025-2026  
**Projecte:** Teatre Regalia - Venda d'Entrades Real-Time  
**Alumne:** Bryan Ruzafa Gonçalves  

## Abstract
Plataforma de venda d'entrades per a esdeveniments d'alta demanda que garanteix la coherència de dades mitjançant WebSockets. El sistema gestiona la concurrència amb bloquejos temporals en memòria (Node.js/Socket.IO) per evitar duplicitats, integrant un backend robust en Laravel i un frontend reactiu en Nuxt 4 amb Pinia.

## Arquitectura del Sistema
El projecte utilitza una arquitectura de serveis desacoblats:
- **Backend (API):** Laravel 11 actua com a punt de persistència SQL i gestió de la lògica de negoci pesada (pagaments, consultes d'usuaris).
- **Temps Real:** Node.js amb Socket.IO per a la gestió de l'estat en memòria, sincronització de butaques i temporitzadors de reserva.
- **Frontend:** Nuxt 4 (Vue 3 Composition API) amb Pinia per a la reactivitat global i una interfície d'usuari immersiva "Cinema Style".

## Estat del Projecte
- [x] Llistat i detall d'esdeveniments (SSR).
- [x] Mapa de seients reactiu via WebSockets.
- [x] Sistema de bloqueig temporal (3 minuts).
- [x] Procés de compra amb confirmació i dades personals.
- [x] Tauler d'administració amb CRUD complet i monitors en temps real.

## Enllaços
- **Vídeo Demo:** [Enllaç a Canva (Pendent d'enregistrament)]
- **Documentació PHP:** [daw.inspedralbes.cat]
- **Projecte en Producció:** [teatretr.daw.inspedralbes.cat]
