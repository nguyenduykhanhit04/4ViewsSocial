importScripts("https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js");

// Đọc cấu hình Firebase từ Query Parameters khi đăng ký Service Worker
const urlParams = new URLSearchParams(self.location.search);

const firebaseConfig = {
  apiKey: urlParams.get("apiKey") || "",
  authDomain: urlParams.get("authDomain") || "",
  projectId: urlParams.get("projectId") || "",
  storageBucket: urlParams.get("storageBucket") || "",
  messagingSenderId: urlParams.get("messagingSenderId") || "",
  appId: urlParams.get("appId") || "",
  databaseURL: urlParams.get("databaseURL") || "",
  measurementId: urlParams.get("measurementId") || "",
};

if (firebaseConfig.apiKey) {
  firebase.initializeApp(firebaseConfig);
  const messaging = firebase.messaging();

  // Xử lý thông báo khi ứng dụng ở chế độ background
  messaging.onBackgroundMessage((payload) => {
    console.log("Received background message: ", payload);
    const notificationTitle = payload.notification?.title || "Thông báo mới";
    const notificationOptions = {
      body: payload.notification?.body || "",
      icon: "/images/logo.png",
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
  });
}