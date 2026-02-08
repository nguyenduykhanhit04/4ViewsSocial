import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
import { getAuth } from "firebase/auth";
import { getDatabase } from "firebase/database";
const firebaseConfig = {
  apiKey: "AIzaSyD2gbc_1Mpvyx18gjWB3USpn_37ZIENGsQ",
  authDomain: "viewsocial-f038a.firebaseapp.com",
  projectId: "viewsocial-f038a",
  storageBucket: "viewsocial-f038a.firebasestorage.app",
  messagingSenderId: "764424668528",
  appId: "1:764424668528:web:fa3fa2ad858d190fba3bb5",
  databaseURL: "https://viewsocial-f038a-default-rtdb.firebaseio.com",
  measurementId: "G-XDBL137ZSY"
};

// Khởi tạo Firebase App
const app = initializeApp(firebaseConfig);

// Khởi tạo Cloud Messaging
const messaging = getMessaging(app);

// Request notification permission from user.
export const requestPermission = async () => {
    try {
        const currentToken = await getToken(messaging, {
            vapidKey: "BOopdS8jwLe3ZSaDR3hiBvvzR3GXEfCxbIrfMqFKvLAAzu7ehxSrSdoY8p2I04brGLnhwKzRgllQJKX5VUdjB1A",
          });
        return currentToken;
    } catch (error) {
        console.error("Cannot get token: :", error);
    }
};

// Listen for messages from FCM while the page is open
export const onMessageListener = () =>
    new Promise((resolve) => {
      onMessage(messaging, (payload) => {
        console.log("Message received:", payload);
        resolve(payload);
      });
    });

// Export messing as use.
export const auth = getAuth(app);
export const db = getDatabase(app);
export { app, messaging };