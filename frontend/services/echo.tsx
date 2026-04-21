import Echo from "laravel-echo";
import Pusher from "pusher-js/react-native";

global.Pusher = Pusher;

let echoInstance: Echo | null = null;

export const initializeEcho = (token: string) => {
  if (echoInstance) return echoInstance;

  echoInstance = new Echo({
    broadcaster: "reverb",
    key: "1234",
    wsHost: "192.168.1.101", 
    wsPort: 8080,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ["ws", "wss"],
    authorizer: (channel: any) => {
      return {
        authorize: (socketId: string, callback: Function) => {
          fetch("http://192.168.1.101:8000/api/v1/broadcasting/auth", {
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
            .then((response) => response.json())
            .then((data) => callback(false, data))
            .catch((error) => {
              console.error("Echo Auth Error:", error);
              callback(true, error);
            });
        },
      };
    },
  });

  return echoInstance;
};

// Also export a way to get the existing instance
export const getEcho = () => echoInstance;