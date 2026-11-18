
import { initializeApp } from
"https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js";

import {
    getAuth,
    signInWithEmailAndPassword,
    onAuthStateChanged,
    signOut
} from "https://www.gstatic.com/firebasejs/11.6.0/firebase-auth.js";

import {
    getFirestore,
    collection,
    addDoc,
    getDocs,
    query,
    orderBy,
    serverTimestamp
} from "https://www.gstatic.com/firebasejs/11.6.0/firebase-firestore.js";

import {
    getStorage,
    ref,
    uploadBytes,
    getDownloadURL
} from "https://www.gstatic.com/firebasejs/11.6.0/firebase-storage.js";

// CONFIGURACIÓN FIREBASE
const firebaseConfig = {
    apiKey: "TU_API_KEY",
    authDomain: "TU_PROYECTO.firebaseapp.com",
    projectId: "TU_PROJECT_ID",
    storageBucket: "TU_BUCKET",
    messagingSenderId: "TU_SENDER_ID",
    appId: "TU_APP_ID"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const db = getFirestore(app);
const storage = getStorage(app);

const loginScreen = document.getElementById("loginScreen");
const albumScreen = document.getElementById("albumScreen");
const gallery = document.getElementById("gallery");

let currentUser = null;

// INICIAR SESIÓN
document.getElementById("loginForm")
.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    try {
        await signInWithEmailAndPassword(
            auth, email, password
        );
    } catch (error) {
        document.getElementById("loginMessage").textContent =
            "No se pudo iniciar sesión. Revisa tus datos.";
    }
});

// COMPROBAR SESIÓN
onAuthStateChanged(auth, async (user) => {
    currentUser = user;

    if (user) {
        loginScreen.classList.add("hidden");
        albumScreen.classList.remove("hidden");
        document.getElementById("logoutBtn")
            .classList.remove("hidden");

        await loadMemories();
    } else {
        loginScreen.classList.remove("hidden");
        albumScreen.classList.add("hidden");
        document.getElementById("logoutBtn")
            .classList.add("hidden");
    }
});

// CERRAR SESIÓN
document.getElementById("logoutBtn")
.addEventListener("click", async () => {
    await signOut(auth);
});

// SUBIR ARCHIVOS
document.getElementById("uploadForm")
.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (!currentUser) return;

    const files = document.getElementById("mediaFile").files;
    const description =
        document.getElementById("description").value;

    const message = document.getElementById("uploadMessage");
    const button = document.getElementById("uploadBtn");

    button.disabled = true;
    message.textContent = "Subiendo recuerdos...";

    try {
        for (const file of files) {

            if (!file.type.startsWith("image/") &&
                !file.type.startsWith("video/")) {
                throw new Error("Archivo no permitido");
            }

            const path = "recuerdos/" +
                crypto.randomUUID();

            const storageRef = ref(storage, path);

            await uploadBytes(storageRef, file);

            const url = await getDownloadURL(storageRef);

            await addDoc(collection(db, "recuerdos"), {
                url: url,
                nombre: file.name,
                tipo: file.type.startsWith("image/")
                    ? "imagen" : "video",
                descripcion: description,
                usuario: currentUser.uid,
                fecha: serverTimestamp()
            });
        }

        document.getElementById("uploadForm").reset();

        message.textContent = "¡Recuerdos guardados! ♡";

        await loadMemories();

    } catch (error) {
        console.error(error);
        message.textContent =
            "No se pudo completar la subida. Revisa Firebase.";
    } finally {
        button.disabled = false;
    }
});

// CARGAR GALERÍA
async function loadMemories() {

    gallery.innerHTML = "";

    const memoriesQuery = query(
        collection(db, "recuerdos"),
        orderBy("fecha", "desc")
    );

    const snapshot = await getDocs(memoriesQuery);

    document.getElementById("totalMemories").textContent =
        snapshot.size + " recuerdos";

    if (snapshot.empty) {
        gallery.innerHTML =
            '<p class="empty">Todavía no hay recuerdos ♡</p>';
        return;
    }

    snapshot.forEach((doc) => {

        const memory = doc.data();

        const card = document.createElement("article");
        card.className = "memory";

        let media;

        if (memory.tipo === "imagen") {
            media = document.createElement("img");
            media.alt = memory.nombre || "Recuerdo";
        } else {
            media = document.createElement("video");
            media.controls = true;
            media.preload = "metadata";
        }

        media.src = memory.url;

        const info = document.createElement("div");
        info.className = "memory-info";

        const description = document.createElement("p");
        description.textContent =
            memory.descripcion || memory.nombre;

        const date = document.createElement("small");
        date.textContent = memory.fecha
            ? memory.fecha.toDate().toLocaleDateString("es-MX")
            : "Recuerdo reciente";

        info.append(description, date);
        card.append(media, info);
        gallery.appendChild(card);
    });
}
