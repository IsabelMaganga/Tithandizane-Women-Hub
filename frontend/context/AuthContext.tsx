import {
  createContext,
  useState,
  useEffect,
  useContext,
  useCallback,
  useRef,
  ReactNode,
} from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import api from "../services/api";
import { getEcho, disconnectEcho, subscribeToNotifications } from "../services/echo";

// ─── Types ────────────────────────────────────────────────────────────────────

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  phone?: string | null;
  created_at?: string;
  updated_at?: string;
}

interface AuthContextType {
  user: User | null;
  token: string | null;
  loading: boolean;
  echo: any | null;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  register: (
    name: string,
    email: string,
    phone: string,
    password: string,
    password_confirmation: string
  ) => Promise<void>;
}

interface AuthProviderProps {
  children: ReactNode;
}

// ─── Context ──────────────────────────────────────────────────────────────────

const AuthContext = createContext<AuthContextType | undefined>(undefined);

const TOKEN_KEY = "token";
const USER_KEY  = "user_profile";

// ─── Provider ─────────────────────────────────────────────────────────────────

export function AuthProvider({ children }: AuthProviderProps) {
  const [user, setUser]       = useState<User | null>(null);
  const [token, setToken]     = useState<string | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [echo, setEcho]       = useState<any | null>(null);

  const socketInitialized = useRef(false);

  // ─── Hydrate from storage on mount ──────────────────────────────────────────
  useEffect(() => {
    const hydrate = async () => {
      try {
        const [savedToken, savedUser] = await Promise.all([
          AsyncStorage.getItem(TOKEN_KEY),
          AsyncStorage.getItem(USER_KEY),
        ]);

        if (savedToken) {
          setToken(savedToken);
          api.defaults.headers.common["Authorization"] = `Bearer ${savedToken}`;

          if (savedUser) {
            setUser(JSON.parse(savedUser));
          }

          try {
            const response = await api.get("/me");
            const freshUser = response.data;
            if (JSON.stringify(freshUser) !== savedUser) {
              setUser(freshUser);
              await AsyncStorage.setItem(USER_KEY, JSON.stringify(freshUser));
            }
          } catch (error: any) {
            if (error.response?.status === 401) {
              await AsyncStorage.removeItem(TOKEN_KEY);
              await AsyncStorage.removeItem(USER_KEY);
              setToken(null);
              setUser(null);
            }
          }
        }
      } catch (error) {
        console.error("Hydration error:", error);
      } finally {
        setLoading(false);
      }
    };

    hydrate();
  }, []);

  // ─── Manage WebSocket connection based on user ID only ───────────────────────
  useEffect(() => {
    const manageSocket = async () => {
      if (user?.id) {
        if (socketInitialized.current) return;
        socketInitialized.current = true;

        const savedToken = await AsyncStorage.getItem(TOKEN_KEY);
        if (savedToken) {
          const echoInstance = getEcho(savedToken);
          setEcho(echoInstance);

          subscribeToNotifications(savedToken, user.id, (notification: any) => {
            console.log("🔔 New notification:", notification);
          });
        }
      } else {
        if (socketInitialized.current) {
          disconnectEcho();
          setEcho(null);
          socketInitialized.current = false;
        }
      }
    };

    manageSocket();
  }, [user?.id]);

  // ─── Login ────────────────────────────────────────────────────────────────
  const login = async (email: string, password: string): Promise<void> => {
    const response = await api.post("/login", { email, password });
    const { token: newToken, user: userData } = response.data;

    console.log("Login successful:", {
      user: userData.name,
      token: newToken.substring(0, 20) + "...",
    });

    // Guard: block admin accounts from using the mobile app
    if (userData.role === "admin") {
      throw new Error("Admin accounts cannot log in here");
    }

    setToken(newToken);
    api.defaults.headers.common["Authorization"] = `Bearer ${newToken}`;

    await Promise.all([
      AsyncStorage.setItem(TOKEN_KEY, newToken),
      AsyncStorage.setItem(USER_KEY, JSON.stringify(userData)),
    ]);

    setUser(userData);
  };

  // ─── Logout ───────────────────────────────────────────────────────────────
  const logout = useCallback(async () => {
    try {
      disconnectEcho();
      await api.post("/logout");
    } catch (e) {
      console.log("Logout error:", e);
    } finally {
      await Promise.all([
        AsyncStorage.removeItem(TOKEN_KEY),
        AsyncStorage.removeItem(USER_KEY),
      ]);
      delete api.defaults.headers.common["Authorization"];
      setUser(null);
      setToken(null);
      setEcho(null);
      socketInitialized.current = false;
    }
  }, []);

  // ─── Register ─────────────────────────────────────────────────────────────
  const register = async (
    name: string,
    email: string,
    phone: string,
    password: string,
    password_confirmation: string
  ): Promise<void> => {
    try {
      const response = await api.post("/register", {
        name: name.trim(),
        email: email.trim().toLowerCase(),
        phone: phone?.trim() || null,
        password,
        password_confirmation,
      });

      if (response.data.token && response.data.user) {
        const newToken = response.data.token;
        await AsyncStorage.setItem(TOKEN_KEY, newToken);
        await AsyncStorage.setItem(USER_KEY, JSON.stringify(response.data.user));
        api.defaults.headers.common["Authorization"] = `Bearer ${newToken}`;
        setToken(newToken);
        setUser(response.data.user);
      }
    } catch (error: any) {
      console.error("Registration error:", error);

      if (error.response?.data?.errors) {
        const errors = error.response.data.errors;
        const firstKey = Object.keys(errors)[0];
        throw new Error(errors[firstKey][0]);
      } else if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      } else if (
        error.code === "NETWORK_ERROR" ||
        error.message === "Network Error"
      ) {
        throw new Error(
          "Unable to connect to server. Please check your internet connection."
        );
      } else {
        throw new Error(`Registration failed: ${error.message || "Unknown error"}`);
      }
    }
  };

  // ─── Provider Value ───────────────────────────────────────────────────────
  return (
    <AuthContext.Provider
      value={{ user, token, loading, login, logout, echo, register }}
    >
      {children}
    </AuthContext.Provider>
  );
}

// ─── Hook ─────────────────────────────────────────────────────────────────────

export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error("useAuth must be used within an AuthProvider");
  }
  return context;
};