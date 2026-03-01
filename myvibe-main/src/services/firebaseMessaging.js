import { FirebaseMessaging } from '@capacitor-firebase/messaging';

export const requestPermission = async () => {
    try {
        // Minta izin push notification
        const { granted } = await FirebaseMessaging.requestPermissions();
        if (!granted) {
            console.error('Permission not granted for push notifications');
            return null;
        }

        // Ambil token setelah izin diberikan
        const token = await FirebaseMessaging.getToken();
        console.log('FCM Token:', token);
        return token;
    } catch (error) {
        console.error('Error requesting permissions:', error);
        return null;
    }
};

export const listenForMessages = () => {
    // Dengarkan pesan FCM
    FirebaseMessaging.addListener('messageReceived', (message) => {
        console.log('Message received:', message);
    });

    // Dengarkan token FCM baru
    FirebaseMessaging.addListener('tokenReceived', (token) => {
        console.log('New FCM Token received:', token);
    });
};
