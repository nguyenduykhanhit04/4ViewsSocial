importScripts("https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js");

// Cấu hình Firebase
const firebaseConfig = {
  apiKey: "AIzaSyD2gbc_1Mpvyx18gjWB3USpn_37ZIENGsQ",
  authDomain: "viewsocial-f038a.firebaseapp.com",
  projectId: "viewsocial-f038a",
  storageBucket: "viewsocial-f038a.firebasestorage.app",
  messagingSenderId: "764424668528",
  appId: "1:764424668528:web:fa3fa2ad858d190fba3bb5",
  measurementId: "G-XDBL137ZSY"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Xử lý thông báo khi app ở chế độ background
messaging.onBackgroundMessage((payload) => {
  console.log("Received background message: ", payload);
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: "/firebase-logo.png",
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});