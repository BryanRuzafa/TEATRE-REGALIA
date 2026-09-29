# Specification: Comportament de la reserva de seients

## Flux Funcional Principal

### 1. Inicialització (Càrrega de dades existents)
Quan qualsevol usuari visita la pàgina d'un esdeveniment, crida via API d'HTTP al backend per obtenir l'status sencer. Desa en el Store de tipus `Pinia`. Els estats previstos de fons un seient inclouran:
- **disponible**: (lliure d’escollir)
- **reservat**: (per altre usuari, està temporalment retingut, el front-end no acceptarà el clic o llançarà error visual)
- **seleccionat**: (les meves seleccions, control basat en l'usuari autenticat i session UUID, per exemple)
- **venut**: (sotmesa compra d'exit)

### 2. Procés actiu: Demana de Reserva
Quan un client prem l'objecte botó "Seient A4" que figura al store com a lliure i envia una petició (emissió `intentar_reserva`, pasant `id_seient`, via web socket al backend Node).

### 3. Actuació del servidor (Concurrència)
El fil únic en execució Javascript (Node.js) captura amb mètodes callback/socket. Això evita naturalment problemes tipus threading concurrent de les crides si es centralitza sobre un Array Global a memòria:
- Node captura `intentar_reserva`. Node comprovarà al array si `estat === 'disponible'`.
- Si és veraç: es modifiquen els paràmetres interiors a memòria a `reservat_per = "el id_client"`, `hora_límit = <timestamp + 3-5minuts>` i s'engega un TimeOut asíncron Node (Node setTimeOut de expirarReserva).
  Aleshores genera un `socket.broadcast("estat_seient", preu, localitzacio, status: "reservat")`. L'usuari que ha premut el click rep `callback{OK}` personal a fi que comenci la zona de pagament personal pel Ticket FrontEnd.
- Si és Fals (Seient prìoritat per una transacció de minuts avançats al temps que ens intercanviem els packets websocket fa dos segons amb un un altre persona) a nosaltres se'ns respondrà via un callback privat de WS tipus: `{error: Seient no disponible en temps reial}` o alhora s'apostarà perquè vegis els valors correctes al moment abans de la resposta si el broadcast del rival havia ja intercedit prèviament per sobre escriure.

### 4. Procés caducat de la reserva temporal
En caure el trigger del `TimeOut` associat dintre Node (Desprès del periode de càlcul prefixat: 180.000 ms), el sistema comprova primer de tot el flag del Checkout Final. Si ha fet el pagament satisfactòriament cap mena de acció hi serà realitzada. Tanmateix,  si la reserva expira i la bandera i no conté confirmacions de compra:
- S'allibera del Hash Map dictat a Backend Node tornant-ho `estat = "disponible"`.
- S'envia arreu broadcast notificant una reacció `socket.broadcast.emit(...)` de reseteig.

### 5. Finalització Compra Activa i Verídica
Quan les transaccions de carret a caixa de checkout acaben i retornem des del punt HTTP a backend exitós les funcions de node es dediquen per una part a emular/guardar dades SQL, com també notificar els client-sockets que ja no hi es reserva provisional en curs si no que una modificació definitiva d'estat cap a estat `venut`, tancant operacions amb Broadcast i posant fi al TimeOut per allunyar bugs de cancel·lació automàtica sobre una compra aprovada recentment i tardana en ms.
