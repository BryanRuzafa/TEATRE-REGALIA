/**
 * TEST DE CONCURRÈNCIA - TREBALL DE RECERCA
 * Provant el Requisit 4.3: Gestió de conflictes en temps real.
 * 
 * Aquest script llança dos clients simultanis que intenten reservar el mateix seient.
 */

const { io } = require("socket.io-client");

const SOCKET_URL = "http://localhost:3001";
const ESDEVENIMENT_ID = 1;
const SEIENT_ID = 1; // Intentarem reservar el seient amb ID 1

console.log("--- INICIANT TEST DE CONCURRÈNCIA ---");

const clientA = io(SOCKET_URL);
const clientB = io(SOCKET_URL);

let respostes = 0;
let exits = 0;
let errors = 0;

const finalitzarTest = () => {
    console.log("\n--- RESULTATS DEL TEST ---");
    console.log(`Total intents: ${respostes}`);
    console.log(`Reserves acceptades (Hauria de ser 1): ${exits}`);
    console.log(`Reserves rebutjades (Hauria de ser 1): ${errors}`);
    
    if (exits === 1 && errors === 1) {
        console.log("\n✅ TEST SUPERAT: El servidor ha gestionat el conflicte correctament.");
    } else {
        console.log("\n❌ TEST FALLAT: Hi ha hagut un error en la sincronització.");
    }
    
    clientA.disconnect();
    clientB.disconnect();
    process.exit();
};

const executarIntent = (client, nom) => {
    console.log(`[${nom}] Intentant reservar seient ${SEIENT_ID}...`);
    client.emit('intentar_reserva', {
        id_seient: SEIENT_ID,
        client_id: `test-${nom}`,
        esdeveniment_id: ESDEVENIMENT_ID
    }, (resposta) => {
        respostes++;
        if (resposta.exit) {
            exits++;
            console.log(`[${nom}] RESULTAT: RESERVA ACCEPTADA ✅`);
        } else {
            errors++;
            console.log(`[${nom}] RESULTAT: RESERVA REBUTJADA (JA OCUPAT) ❌ -> ${resposta.missatge}`);
        }

        if (respostes === 2) finalitzarTest();
    });
};

// Esperem que els clients es connectin i s'uneixin a l'esdeveniment
const prepararClient = (client, nom) => {
    return new Promise((resolve) => {
        client.on("connect", () => {
            client.emit("unir_esdeveniment", { 
                esdeveniment_id: ESDEVENIMENT_ID, 
                seients_inicials: [] 
            }, () => {
                console.log(`[${nom}] Connectat i a punt.`);
                resolve();
            });
        });
    });
};

async function llançarTest() {
    await Promise.all([
        prepararClient(clientA, "CLIENT A"),
        prepararClient(clientB, "CLIENT B")
    ]);

    console.log("\nLlançant peticions SIMULTÀNIES...");
    
    // Executem els intents al mateix moment
    executarIntent(clientA, "CLIENT A");
    executarIntent(clientB, "CLIENT B");
}

llançarTest();
