import { initializeApp } from 'firebase/app';

const firebaseConfig = {
    apiKey: "AIzaSyB_9X7c-z7bO4bdeId_Jz5ZfGSexfT6DOA",
    authDomain: "vibe-8222d.firebaseapp.com",
    projectId: "vibe-8222d",
    storageBucket: "vibe-8222d.firebasestorage.app",
    messagingSenderId: "383379717528",
    appId: "1:383379717528:web:196d54a0ede2516329ffed",
    measurementId: "G-EP0WXD3MLS"
};

// Initialize Firebase
const firebaseApp = initializeApp(firebaseConfig);

export default firebaseApp;
