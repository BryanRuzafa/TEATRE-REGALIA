# Guia d'Instal·lació des de Zero (Setup Guide)

Aquesta guia explica com preparar una màquina nova (Windows, Mac o Linux) per executar el projecte "Teatre Regalia".

## Pas 1: Instal·lació dels Programes Necessaris
Abans de tocar el codi, la teva "màquina" ha de parlar el llenguatge del projecte. Instal·la aquests programes en l'ordre indicat:

1. **Git**: Descarrega'l a [git-scm.com](https://git-scm.com/). Serveix per baixar el projecte.
2. **Node.js**: Descarrega la versió **LTS** (recomanat 18 o 20) a [nodejs.org](https://nodejs.org/).
3. **PHP**: Per a Windows, la forma més fàcil és usar [XAMPP](https://www.apachefriends.org/) (assegura't que sigui PHP 8.2 o superior).
4. **Composer**: L'instal·lador de Laravel. Descarrega'l a [getcomposer.org](https://getcomposer.org/).

---

## Pas 2: Obtenir el Codi i Preparar el Backend
Obre un terminal (PowerShell o CMD) i segueix aquests passos:

1. **Clonar el repositori**:
   ```bash
   git clone https://github.com/inspedralbes/prj-entrades-BryanRuzafaGon-3
   cd prj-entrades-BryanRuzafaGon-3
   ```

2. **Configurar el Backend (Laravel)**:
   ```bash
   cd backend
   composer install
   copy .env.example .env
   php artisan key:generate
   ```

3. **Crear la Base de Dades**:
   *El projecte usa SQLite per no haver de configurar servidors complexos.*
   ```bash
   # Crea el fitxer buit de la base de dades
   ni database/database.sqlite   # En Windows
   # O simplement crea un fitxer buit anomenat database.sqlite a la carpeta database/
   
   # Executa les taules i les dades de prova
   php artisan migrate:fresh --seed
   ```

---

## Pas 3: Dependències de Temps Real i Frontend
Necessitem baixar les llibreries de JavaScript per als dos mòduls restants.

1. **Instal·lar Sockets (Realtime)**:
   ```bash
   cd ../realtime
   npm install
   ```

2. **Instal·lar Interfície (Frontend)**:
   ```bash
   cd ../frontend
   npm install
   ```

---

## Pas 4: Com executar el projecte (3 terminals)
Perquè el sistema funcioni al 100%, has de tenir **tres finestres del terminal obertes a la vegada**:

1. **Terminal 1 (Backend)**:
   Dins de la carpeta `backend`, executa: `php artisan serve`
   *(Això aixeca l'API al port 8000)*.

2. **Terminal 2 (Realtime)**:
   Dins de la carpeta `realtime`, executa: `node index.js`
   *(Això aixeca els WebSockets al port 3001)*.

3. **Terminal 3 (Frontend)**:
   Dins de la carpeta `frontend`, executa: `npm run dev`
   *(Això obre la web a [http://localhost:3000](http://localhost:3000))*.

---

¡Ja ho tens! Ara pots entrar a la web i començar a comprar entrades o gestionar l'esdeveniment des de `/admin`.
