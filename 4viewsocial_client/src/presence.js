import { auth } from "@/firebase";
import {
  getDatabase,
  ref,
  push,
  set,
  onDisconnect,
  serverTimestamp,
} from "firebase/database";
import { onAuthStateChanged } from "firebase/auth";

const db = getDatabase();

export function initPresence() {
  onAuthStateChanged(auth, (user) => {
    if (!user) return;

    const userStatusRef = ref(db, `status/${user.uid}`);
    const connectionRef = push(userStatusRef);

    console.log("🟢 New connection:", connectionRef.key);

    // Khi tab này disconnect → remove node này
    onDisconnect(connectionRef).remove();

    // Set online cho tab này
    set(connectionRef, {
      state: "online",
      last_changed: serverTimestamp(),
    });
  });
}
