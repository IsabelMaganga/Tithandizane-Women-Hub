import Echo from "laravel-echo";

// ─── Host Configuration ───────────────────────────────────────────────────────
const LARAVEL_HOST = "192.168.1.132";
const LARAVEL_PORT = "8000";
const REVERB_PORT  = 8080;


const getPusherConstructor = () => {
  // eslint-disable-next-line @typescript-eslint/no-var-requires
  const mod = require("pusher-js/react-native");
  const Ctor = mod?.default ?? mod?.Pusher ?? mod;
  if (typeof Ctor !== "function") {
    throw new Error(
      `pusher-js/react-native did not export a constructor. Got: ${typeof Ctor}. ` +
      `Keys: ${Object.keys(mod ?? {}).join(", ")}`
    );
  }
  return Ctor;
};

// ─── Singleton Echo Instance ──────────────────────────────────────────────────
let echoInstance: Echo | null = null;

export const getEcho = (token: string): Echo => {
  if (echoInstance) return echoInstance;

  const PusherClient = getPusherConstructor();

  const pusher = new PusherClient("1234", {
    wsHost: LARAVEL_HOST,
    wsPort: REVERB_PORT,
    wssPort: REVERB_PORT,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ["ws"],
    cluster: "mt1",
    authorizer: (channel: { name: string }) => ({
      authorize: (
        socketId: string,
        callback: (error: boolean, data: unknown) => void
      ) => {
        fetch(`http://${LARAVEL_HOST}:${LARAVEL_PORT}/broadcasting/auth`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            socket_id: socketId,
            channel_name: channel.name,
          }),
        })
          .then((res) => {
            if (!res.ok) throw new Error(`Auth failed: ${res.status}`);
            return res.json();
          })
          .then((data) => callback(false, data))
          .catch((err) => {
            console.error("Broadcasting auth error:", err);
            callback(true, err);
          });
      },
    }),
  });

  pusher.connection.bind("connected", () =>
    console.log("✅ Reverb connected")
  );
  pusher.connection.bind("error", (err: unknown) =>
    console.error("❌ Reverb connection error:", err)
  );
  pusher.connection.bind("disconnected", () => {
    console.warn("⚠️ Reverb disconnected");
    echoInstance = null;
  });

  echoInstance = new Echo({
    broadcaster: "reverb",
    key: "1234",
    client: pusher,
    wsHost: LARAVEL_HOST,
    wsPort: REVERB_PORT,
    wssPort: REVERB_PORT,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ["ws"],
    cluster: "mt1",
  });

  return echoInstance;
};

// ─── Disconnect & Cleanup ─────────────────────────────────────────────────────
export const disconnectEcho = () => {
  if (echoInstance) {
    echoInstance.disconnect();
    echoInstance = null;
    console.log("🔌 Reverb disconnected");
  }
};

// ─── Channel Helpers ──────────────────────────────────────────────────────────

export const subscribeToChatChannel = (
  token: string,
  conversationId: number,
  onMessage: (payload: unknown) => void
): (() => void) => {
  const echo = getEcho(token);
  echo.private(`chat.${conversationId}`).listen("MessageSent", onMessage);
  return () => {
    echo.leave(`chat.${conversationId}`);
    console.log(`🚪 Left chat.${conversationId}`);
  };
};

export const subscribeToNotifications = (
  token: string,
  userId: number,
  onNotification: (payload: unknown) => void
): (() => void) => {
  const echo = getEcho(token);
  echo.private(`App.Models.User.${userId}`).notification(onNotification);
  return () => {
    echo.leave(`App.Models.User.${userId}`);
    console.log(`🚪 Left App.Models.User.${userId}`);
  };
};