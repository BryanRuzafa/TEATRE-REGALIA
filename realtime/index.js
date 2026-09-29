import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';
import cors from 'cors';

const app = express();
app.use(cors());

const LARAVEL_API_URL = process.env.LARAVEL_API_URL || 'http://localhost:8000';

const httpServer = createServer(app);
const io = new Server(httpServer, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"]
  }
});

/** 
 * ESTAT DEL SERVIDOR
 * seientsPerEsdeveniment: { 
 *    [esdeveniment_id]: { [seient_id]: { estat, reservat_per, tempsExpira } } 
 * }
 */
const seientsPerEsdeveniment = {};
const timersReserva = new Map(); // [esdeveniment_id-seient_id] -> Timer
const usuarisPerEsdeveniment = {}; // [esdeveniment_id] -> Set of socket.ids

/**
 * Funció auxiliar per calcular el total de reserves actives al servidor (Requeriment 3.2.2)
 */
const obtenirTotalReservesActives = () => {
  let total = 0;
  Object.values(seientsPerEsdeveniment).forEach(esdeveniment => {
    Object.values(esdeveniment).forEach(seient => {
      if (seient.estat === 'reservat') total++;
    });
  });
  return total;
};

io.on('connection', (socket) => {
  console.log(`Nou client connectat: ${socket.id}`);

  // L'usuari tria un esdeveniment al entrar
  socket.on('unir_esdeveniment', (dades, callback) => {
    const { esdeveniment_id, seients_inicials } = dades;
    
    socket.join(`esdeveniment_${esdeveniment_id}`);
    socket.data.esdeveniment_id = esdeveniment_id;

    // Inicialitzem l'estat en memòria si no existeix (primera vegada)
    if (!seientsPerEsdeveniment[esdeveniment_id]) {
      seientsPerEsdeveniment[esdeveniment_id] = {};
      seients_inicials.forEach(s => {
        seientsPerEsdeveniment[esdeveniment_id][s.id_referencia] = {
           id: s.id_referencia,
           estat: s.estat,
           reservat_per: s.reservat_per,
           tempsExpira: s.temps_bloqueig_finalitza ? new Date(s.temps_bloqueig_finalitza).getTime() : null
        };
      });
    }

    // Tracking d'usuaris
    if (!usuarisPerEsdeveniment[esdeveniment_id]) usuarisPerEsdeveniment[esdeveniment_id] = new Set();
    usuarisPerEsdeveniment[esdeveniment_id].add(socket.id);

    // Notifiquem a l'admin del canvi de comptador
    io.emit('actualitzacio_admin', { 
        esdeveniment_id, 
        usuaris: usuarisPerEsdeveniment[esdeveniment_id].size 
    });

    // Enviem l'estat actual només a qui entra
    callback(seientsPerEsdeveniment[esdeveniment_id]);
  });

  socket.on('intentar_reserva', (dades, callback) => {
    const { id_seient, client_id, esdeveniment_id } = dades;
    const sala = seientsPerEsdeveniment[esdeveniment_id];

    if (!sala || !sala[id_seient]) return callback({ exit: false, missatge: 'Seient o sala no trobats.' });

    const seient = sala[id_seient];

    if (seient.estat === 'disponible') {
      // 1. Notifiquem a Laravel primer per assegurar persistència SQL
      fetch(`${LARAVEL_API_URL}/api/seients/reservar`, {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ id_seient, client_id, esdeveniment_id })
      }).then(res => res.json()).then(data => {
        if (data.exit) {
          // 2. Si Laravel dóna l'OK, actualitzem memòria i gestionem timeout
          seient.estat = 'reservat';
          seient.reservat_per = client_id;
          
          const TEMPS_RESERVA = 180000; // 3 minuts
          seient.tempsExpira = Date.now() + TEMPS_RESERVA;

          const timerChave = `${esdeveniment_id}-${id_seient}`;
          if (timersReserva.has(timerChave)) {
            clearTimeout(timersReserva.get(timerChave));
          }

          const timerIdx = setTimeout(async () => {
            if (seientsPerEsdeveniment[esdeveniment_id][id_seient].estat === 'reservat') {
              console.log(`Reserva caducada: ${id_seient} (Esdeveniment ${esdeveniment_id})`);
              
              // Notifiquem alliberament a Laravel
              await fetch(`${LARAVEL_API_URL}/api/seients/alliberar`, {
                method: 'POST',
                headers: { 
                  'Content-Type': 'application/json',
                  'Accept': 'application/json'
                },
                body: JSON.stringify({ id_seient, esdeveniment_id })
              });

              seientsPerEsdeveniment[esdeveniment_id][id_seient].estat = 'disponible';
              seientsPerEsdeveniment[esdeveniment_id][id_seient].reservat_per = null;
              seientsPerEsdeveniment[esdeveniment_id][id_seient].tempsExpira = null;
              
              io.to(`esdeveniment_${esdeveniment_id}`).emit('seient_actualitzat', seientsPerEsdeveniment[esdeveniment_id][id_seient]);
              timersReserva.delete(timerChave);

              // Notifiquem a l'admin l'alliberament
              io.emit('actualitzacio_admin', { total_reserves: obtenirTotalReservesActives() });
            }
          }, TEMPS_RESERVA);

          timersReserva.set(timerChave, timerIdx);
          
          io.to(`esdeveniment_${esdeveniment_id}`).emit('seient_actualitzat', seient);
          
          // Notifiquem a l'admin del canvi en el comptador de reserves globals
          io.emit('actualitzacio_admin', { total_reserves: obtenirTotalReservesActives() });

          return callback({ exit: true, seient });
        } else {
          return callback({ exit: false, missatge: data.missatge || 'Error en la persistència.' });
        }
      }).catch(err => {
        console.error("Error sincronitzant amb Laravel:", err);
        return callback({ exit: false, missatge: 'El sistema de pagament no respon.' });
      });
    } else {
      return callback({ exit: false, missatge: 'Aquest seient ja no està disponible.' });
    }
  });

  socket.on('confirmar_compra', (dades, callback) => {
    const { client_id, esdeveniment_id } = dades;
    const sala = seientsPerEsdeveniment[esdeveniment_id];
    
    if (!sala) return callback({ exit: false });

    Object.keys(sala).forEach(id => {
      if (sala[id].reservat_per === client_id && sala[id].estat === 'reservat') {
        sala[id].estat = 'venut';
        sala[id].tempsExpira = null;
        
        const timerChave = `${esdeveniment_id}-${id}`;
        if (timersReserva.has(timerChave)) {
          clearTimeout(timersReserva.get(timerChave));
          timersReserva.delete(timerChave);
        }
        io.to(`esdeveniment_${esdeveniment_id}`).emit('seient_actualitzat', sala[id]);
      }
    });

    // Enviem actualització a l'admin també
    io.emit('actualitzacio_admin', { 
        esdeveniment_id, 
        refresh_stats: true,
        total_reserves: obtenirTotalReservesActives() 
    });
    
    callback({ exit: true });
  });

  socket.on('disconnect', () => {
    console.log(`Client desconnectat: ${socket.id}`);
    const eid = socket.data.esdeveniment_id;
    if (eid && usuarisPerEsdeveniment[eid]) {
      usuarisPerEsdeveniment[eid].delete(socket.id);
      io.emit('actualitzacio_admin', { 
        esdeveniment_id: eid, 
        usuaris: usuarisPerEsdeveniment[eid].size 
      });
    }
  });
});

const PORT = 3001;
httpServer.listen(PORT, () => {
  console.log(`Servidor TR (Multiesdeveniment) escoltant al port ${PORT}`);
});
