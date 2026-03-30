import { createContext, useState, useEffect, useContext, ReactNode, useCallback } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import api from "../services/api";
import { initializeEcho } from "../services/echo";

// Types
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
  register: (name: string, email: string, phone: string, password: string, password_confirmation: string) => Promise<void>;
}

interface AuthProviderProps {
  children: ReactNode;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

const TOKEN_KEY = "token";
const USER_KEY = "user_profile";

export function AuthProvider({ children }: AuthProviderProps) {
  const [user, setUser] = useState<User | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [loading, setLoading] = useState<boolean>(true);
  const [echo, setEcho] = useState<any | null>(null);

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
          refreshUser();
        }
      } catch (error) {
        console.error("Hydration Error:", error);
      } finally {
        setLoading(false);
      }
    };
    hydrate();
  }, []);

  // Handle socket connection/disconnection based on user state
  useEffect(() => {
    const manageSocket = async () => {
      if (user) {
        const token = await AsyncStorage.getItem("token");

        if (token && !echo) {
          const echoInstance = await initializeEcho(token);
          setEcho(echoInstance);
        }
      } else {
        if (echo) {
          echo.disconnect();
          setEcho(null);
        }
      }
    };

    manageSocket();
  }, [user, echo]);

  const refreshUser = async () => {
    try {
      const response = await api.get("/me");
      const freshUser = response.data;
      setUser(freshUser);
      await AsyncStorage.setItem(USER_KEY, JSON.stringify(freshUser));
    } catch (error) {
      if (error.response?.status === 401) logout();
    }
  };

  const login = async (email: string, password: string): Promise<void> => {
    try {
      console.log('Attempting login to:', api.defaults.baseURL);
      console.log('Login data:', { email, password: '***' });
      
      const response = await api.post("/login", { email, password });
      const { token: newToken, user: userData } = response.data;
      console.log('Login successful:', { user: userData.name, token: newToken.substring(0, 20) + '...' });

      setToken(newToken);
      api.defaults.headers.common["Authorization"] = `Bearer ${newToken}`;
      
      await Promise.all([
        AsyncStorage.setItem(TOKEN_KEY, newToken),
        AsyncStorage.setItem(USER_KEY, JSON.stringify(userData)),
      ]);

      setUser(userData);
    } catch (error: any) {
      console.error('Login failed with error:', error);
      console.error('Error details:', {
        message: error.message,
        code: error.code,
        response: error.response?.data,
        status: error.response?.status
      });
      
      if (error.response) {
        throw new Error(error.response.data?.message || "Login failed");
      } else if (error.code === 'NETWORK_ERROR' || error.message.includes('Network Error')) {
        throw new Error("Network error. Please check your connection and ensure the server is accessible.");
      } else {
        throw new Error(`Login failed: ${error.message || 'Unknown error'}`);
      }
    }
  };

  const logout = useCallback(async () => {
    try {
      if (echo) echo.disconnect();
      await api.post("/logout");
    } catch (e) {
      console.log("Logout error", e);
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
  }, [echo]);

  const register = async (
    name: string, 
    email: string, 
    phone: string, 
    password: string, 
    password_confirmation: string
  ): Promise<void> => {
    try {
      console.log('Attempting registration with:', { name, email, phone: phone || null });
      
      const response = await api.post("/register", { 
        name: name.trim(),
        email: email.trim().toLowerCase(),
        phone: phone?.trim() || null,
        password: password,
        password_confirmation: password_confirmation,
      });
      
      console.log('Registration response:', response.data);
      
      // Auto-login after successful registration
      if (response.data.token && response.data.user) {
        await AsyncStorage.setItem("token", response.data.token);
        api.defaults.headers.common["Authorization"] = `Bearer ${response.data.token}`;
        setUser(response.data.user);
      }
      
    } catch (error: any) {
      console.error('Registration error details:', error);
      
      if (error.response?.data?.errors) {
        const errors = error.response.data.errors;
        const firstErrorKey = Object.keys(errors)[0];
        const firstErrorMessage = errors[firstErrorKey][0];
        throw new Error(firstErrorMessage);
      } else if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      } else if (error.code === 'NETWORK_ERROR' || error.message === 'Network Error') {
        throw new Error("Unable to connect to server. Please check your internet connection.");
      } else {
        throw new Error(`Registration failed: ${error.message || 'Unknown error'}`);
      }
    }
  };

  return (
    <AuthContext.Provider value={{ user, token, loading, login, logout, echo, register }}>
      {children}
    </AuthContext.Provider>
  );
}

// Custom hook for using auth context
export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error("useAuth must be used within an AuthProvider");
  }
  return context;
};