# Walkthrough Final: Plataforma de Venda d'Entrades TR

Hem completat el desenvolupament de la plataforma per al Treball de Recerca de 2n de DAW. El sistema és robust, compleix amb els requeriments de temps real i presenta una estètica de primer nivell.

## 1. Flux de l'Usuari (Client)
- **Portada**: Llistat dinàmic de pel·lícules (Torrente, Pa Negre, etc.) carregades des de Laravel.
- **Mapa de Seients**: Visualització en temps real via Socket.IO. Les butaques canvien d'estat a tots els clients a l'instant.
- **Reserva Temporal**: Bloqueig de 3 minuts amb temporitzador gestionat pel servidor de Node.js.
- **Checkout**: Formulari de dades personals per formalitzar la compra.
- **Consulta**: Secció per recuperar tiquets comprats mitjançant el correu electrònic.

## 2. Panell d'Administració
- **Dashboard**: Visualització de la recaptació total i el nombre de vendes.
- **Monitors LIVE**: Seguiment d'usuaris connectats a cada sala en temps real.
- **Gestió**: Botó per crear nous esdeveniments que generen automàticament sales de 25 butaques.

## 3. Documentació Tècnica Generada
- [Diagrames (Seqüència, E/R, Casos d'Ús)](file:///c:/Users/Bryan/Documents/tr3/docs/diagrames.md)
- [Manual d'Instal·lació](file:///c:/Users/Bryan/Documents/tr3/docs/manual.md)
- [Script SQL de la Base de Dades](file:///c:/Users/Bryan/Documents/tr3/database/schema.sql)
- [Registre de Prompts (SDD)](file:///c:/Users/Bryan/Documents/tr3/docs/prompts-log.md)

## 4. Solucions de Concurrència
S'ha utilitzat un model de **Single Source of Truth** en el servidor de Node.js per evitar condicions de carrera. La persistència es realitza a Laravel un cop la compra ha estat validada pel motor de temps real.

---
**Estat Final: Llest per a l'entrega i defensa davant del tribunal.**
