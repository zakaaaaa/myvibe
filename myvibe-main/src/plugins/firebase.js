import { initializeApp } from 'firebase/app';

const firebaseConfig = {
    apiKey: "AIzaSyAVHtcYEs2DkYd6HwIDUtYAuUT5Dwpny-M",
    authDomain: "myvibe-app-51d7f.firebaseapp.com",
    databaseURL: "https://myvibe-app-51d7f-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "myvibe-app-51d7f",
    storageBucket: "myvibe-app-51d7f.firebasestorage.app",
    messagingSenderId: "12107287082",
    appId: "1:12107287082:web:813d9ceee51cbd9fed5153",
    measurementId: "G-V1NT2L2YST"
};

const firebaseApp = initializeApp(firebaseConfig);

export default {
    install: (app) => {
        app.config.globalProperties.$firebase = firebaseApp;
    },
};