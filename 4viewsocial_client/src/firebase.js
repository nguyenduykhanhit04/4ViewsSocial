import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
import { getAuth } from "firebase/auth";
import { getDatabase } from "firebase/database";

/**
 * Cấu hình Firebase đọc từ biến môi trường .env (Bảo mật, không hardcode API Key).
 */
const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
  authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
  storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
  messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
  appId: import.meta.env.VITE_FIREBASE_APP_ID,
  databaseURL: import.meta.env.VITE_FIREBASE_DATABASE_URL,
  measurementId: import.meta.env.VITE_FIREBASE_MEASUREMENT_ID,
};

// Khởi tạo Firebase App
const app = initializeApp(firebaseConfig);

// Khởi tạo Cloud Messaging
let messaging = null;
try {
  messaging = getMessaging(app);
} catch (e) {
  console.warn("FCM Messaging is not supported or failed to initialize:", e);
}

/**
 * Yêu cầu quyền nhận thông báo từ người dùng và lấy FCM Device Token.
 *
 * @return {Promise<string|null>}
 */
export const requestPermission = async () => {
  try {
    if (!messaging) return null;
    const currentToken = await getToken(messaging, {
      vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY,
    });
    return currentToken;
  } catch (error) {
    console.error("Không thể lấy Device Token FCM:", error);
    return null;
  }
};

/**
 * Lắng nghe thông báo khi ứng dụng đang mở ở Foreground.
 *
 * @return {Promise<any>}
 */
export const onMessageListener = () =>
  new Promise((resolve) => {
    if (!messaging) return;
    onMessage(messaging, (payload) => {
      console.log("Đã nhận thông báo mới:", payload);
      resolve(payload);
    });
  });

export const auth = getAuth(app);
export const db = getDatabase(app);
export { app, messaging };