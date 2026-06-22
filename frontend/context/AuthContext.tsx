import {
  createContext,
  useState,
  useEffect,
  useContext,
  useCallback,
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

          // Refresh user profile in background
          refreshUser();
        }
      } catch (error) {
        console.error("Hydration error:", error);
      } finally {
        setLoading(false);
      }
    };

    hydrate();
  }, []);

  // ─── Manage WebSocket connection based on user state ─────────────────────────
  useEffect(() => {
    const manageSocket = async () => {
      if (user) {
        const savedToken = await AsyncStorage.getItem(TOKEN_KEY);

        if (savedToken && !echo) {
          // getEcho is synchronous — no await needed
          const echoInstance = getEcho(savedToken);
          setEcho(echoInstance);

          // Subscribe to this user's notification channel
          subscribeToNotifications(savedToken, user.id, (notification: any) => {
            console.log("🔔 New notification:", notification);
            // Handle notification (e.g. update badge count, show toast, etc.)
          });
        }
      } else {
        // User logged out — clean up WebSocket
        if (echo) {
          disconnectEcho();
          setEcho(null);
        }
      }
    };

    manageSocket();
  }, [user]); // ← only depend on user, not echo (avoids infinite loop)

  // ─── Refresh user profile ─────────────────────────────────────────────────
  const refreshUser = async () => {
    try {
      const response = await api.get("/me");
      const freshUser = response.data;
      setUser(freshUser);
      await AsyncStorage.setItem(USER_KEY, JSON.stringify(freshUser));
    } catch (error: any) {
      if (error.response?.status === 401) logout();
    }
  };

  // ─── Login ────────────────────────────────────────────────────────────────
  const login = async (email: string, password: string): Promise<void> => {
    try {
      console.log("Attempting login to:", api.defaults.baseURL);
      console.log("Login data:", { email, password: "***" });

      const response = await api.post("/login", { email, password });
      const { token: newToken, user: userData } = response.data;

      console.log("Login successful:", {
        user: userData.name,
        token: newToken.substring(0, 20) + "...",
      });

      if (userData.role === "admin") {
        throw new Error("Admin accounts cannot log in here");
      }

      setToken(newToken);
      api.defaults.headers.common["Authorization"] = `Bearer ${newToken}`;

      await Promise.all([
        AsyncStorage.setItem(TOKEN_KEY, newToken),
        AsyncStorage.setItem(USER_KEY, JSON.stringify(userData)),
      ]);

      // Setting user triggers the socket useEffect above
      setUser(userData);
    } catch (error: any) {
      console.error("Login failed:", error.message);

      if (error.response) {
        throw new Error(error.response.data?.message || "Login failed");
      } else if (
        error.code === "NETWORK_ERROR" ||
        error.message.includes("Network Error")
      ) {
        throw new Error(
          "Network error. Please check your connection and ensure the server is accessible."
        );
      } else {
        throw new Error(`Login failed: ${error.message || "Unknown error"}`);
      }
    }
  };

  // ─── Logout ───────────────────────────────────────────────────────────────
  const logout = useCallback(async () => {
    try {
      disconnectEcho(); // clean up WebSocket before hitting logout endpoint
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
      console.log("Attempting registration:", { name, email });

      const response = await api.post("/register", {
        name: name.trim(),
        email: email.trim().toLowerCase(),
        phone: phone?.trim() || null,
        password,
        password_confirmation,
      });

      console.log("Registration response:", response.data);

      // Auto-login after successful registration
      if (response.data.token && response.data.user) {
        const newToken = response.data.token;
        await AsyncStorage.setItem(TOKEN_KEY, newToken);
        await AsyncStorage.setItem(USER_KEY, JSON.stringify(response.data.user));
        api.defaults.headers.common["Authorization"] = `Bearer ${newToken}`;
        setToken(newToken);
        // Setting user triggers the socket useEffect
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
