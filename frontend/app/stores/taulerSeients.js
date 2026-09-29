import { defineStore } from 'pinia'
import { io } from 'socket.io-client'

export const usTaulerSeientsStore = defineStore('taulerSeients', {
  state: () => {
    // Recuperem o generem un ID de sessió persistent per a l'usuari
    // Usem una funció per gestionar l'entorn SSR de Nuxt (localStorage no existeix al servidor)
    const obtenirSessioClient = () => {
      if (typeof window === 'undefined') return 'us_cinp_ssr_placeholder';
      const guardat = localStorage.getItem('tr_sessio_client');
      if (guardat) return guardat;
      const nou = 'us_cinp_' + Math.random().toString(36).substring(2, 10);
      localStorage.setItem('tr_sessio_client', nou);
      return nou;
    };

    return {
    socket: null,
    seients: {}, // Objecte [id_referencia] -> { estat, reservat_per, tempsExpira }
    connectat: false,
    esdeveniment_id: null,
    sessioClient: obtenirSessioClient(),
    missatgeError: null,
    missatgeExit: null,
    usuarisEnLinia: 0,
    dadesComprador: {
       nom: '',
       email: ''
    }
    }
  },

  getters: {
    // Detectem la IP per al Socket (port 3001) però l'API ja va via Proxy a /api
    obtenirUrls(state) {
      if (typeof window === 'undefined') return { socket: '' };
      const hostname = window.location.hostname;
      return {
        socket: `http://${hostname}`
      };
    }
  },

  actions: {
    iniciarConnexioSocket(eid, seientsInicials) {
      const urls = this.obtenirUrls;
      
      this.esdeveniment_id = eid;
      this.socket = io(urls.socket);

      this.socket.on('connect', () => {
        this.connectat = true;
        
        // Ens unim a la sala de l'esdeveniment
        this.socket.emit('unir_esdeveniment', { 
            esdeveniment_id: eid, 
            seients_inicials: seientsInicials 
        }, (estatFinal) => {
            this.seients = estatFinal;
        });
      });

      this.socket.on('seient_actualitzat', (seient) => {
        this.seients[seient.id] = seient;
      });

      this.socket.on('actualitzacio_admin', (dades) => {
        if (dades.esdeveniment_id === this.esdeveniment_id) {
            if (dades.usuaris !== undefined) this.usuarisEnLinia = dades.usuaris;
        }
      });

      this.socket.on('disconnect', () => {
        this.connectat = false;
      });
    },

    solicitarBloqueigSeient(id_seient) {
      this.missatgeError = null;
      this.socket.emit('intentar_reserva', { 
          id_seient, 
          client_id: this.sessioClient,
          esdeveniment_id: this.esdeveniment_id
      }, (res) => {
        if (!res.exit) {
          this.missatgeError = res.missatge;
          setTimeout(() => this.missatgeError = null, 3000);
        } else {
          this.missatgeExit = "Butaca reservada 3 minuts";
          setTimeout(() => this.missatgeExit = null, 3000);
        }
      });
    },

    async processarCompraFinal() {
      if (!this.dadesComprador.nom || !this.dadesComprador.email) {
          this.missatgeError = "Cal omplir les dades del comprador.";
          return;
      }

      try {
        const idSeients = Object.values(this.seients)
          .filter(s => s.reservat_per === this.sessioClient && s.estat === 'reservat')
          .map(s => s.id); // 'id' conté la referència de la butaca (ex: "23") sentida pel socket

        console.log("Enviant compra per seients:", idSeients, "ID Client:", this.sessioClient);

        if (idSeients.length === 0) {
            this.missatgeError = "No teniu cap seient seleccionat o la sessió ha expirat.";
            return;
        }

        const config = useRuntimeConfig();
        // Persistència a Laravel via Proxy
        const resposta = await fetch(`${config.public.apiBase}/venda/confirmar`, {
          method: 'POST',
          headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            client_id: this.sessioClient,
            esdeveniment_id: this.esdeveniment_id,
            nom: this.dadesComprador.nom,
            email: this.dadesComprador.email,
            seients: idSeients // Llista de id_referencia (ex: "1", "5", "12")
          })
        });

        const data = await resposta.json();
        if (data.exit) {
          // Confirmem al socket per alliberar memòria i fer l'emit
          this.socket.emit('confirmar_compra', { 
              client_id: this.sessioClient,
              esdeveniment_id: this.esdeveniment_id
          }, (res) => {
             this.missatgeExit = `Compra confirmada! Codi: ${data.codi_confirmacio}. Reviseu el correu.`;
             this.dadesComprador = { nom: '', email: '' };
             setTimeout(() => this.missatgeExit = null, 8000);
          });
        } else {
          // Mostra el missatge exacte del servidor per facilitar el diagnòstic
          this.missatgeError = data.error || "Els seients ja no estan disponibles o han caducat.";
          setTimeout(() => this.missatgeError = null, 5000);
        }
      } catch (e) {
        this.missatgeError = "Error en el procés de pagament.";
      }
    }
  }
})
