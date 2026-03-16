import Echo from "laravel-echo";
import Pusher from "pusher-js/react-native";

global.Pusher = Pusher;

let echo: Echo | null = null;

export const initializeEcho = async (token: string) => {
  if (echo) return echo;

  echo = new Echo({
    broadcaster: "reverb",
    key: "your_unique_key",
    wsHost: "192.168.43.103",
    wsPort: 8080,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ["ws", "wss"],
    authEndpoint: "http://192.168.43.103:8000/api/broadcasting/auth",
    auth: {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
      },
    },
  });

  return echo;
};