import { createContext, useState, useEffect, useContext, useCallback } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import api from "../services/api";
import { initializeEcho } from "../services/echo";

const AuthContext = createContext();

const TOKEN_KEY = "token";
const USER_KEY = "user_profile";

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(null); // ADD THIS: Store token in state
  const [loading, setLoading] = useState(true);
  const [echo, setEcho] = useState(null);

  useEffect(() => {
    const hydrate = async () => {
      try {
        const [savedToken, savedUser] = await Promise.all([
          AsyncStorage.getItem(TOKEN_KEY),
          AsyncStorage.getItem(USER_KEY),
        ]);

        if (savedToken) {
          setToken(savedToken); // Set token in state
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

  const login = async (email, password) => {
    try {
      const response = await api.post("/login", { email, password });
      const { token: newToken, user: userData } = response.data;

      setToken(newToken); // Update state
      api.defaults.headers.common["Authorization"] = `Bearer ${newToken}`;
      
      await Promise.all([
        AsyncStorage.setItem(TOKEN_KEY, newToken),
        AsyncStorage.setItem(USER_KEY, JSON.stringify(userData)),
      ]);

      setUser(userData);
    } catch (error) {
      throw new Error(error.response?.data?.message || "Login failed");
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
      setToken(null); // Clear state
      setEcho(null);
    }
  }, [echo]);

  // Include 'token' in the Provider value
  return (
    <AuthContext.Provider value={{ user, token, loading, login, logout, echo }}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => useContext(AuthContext);