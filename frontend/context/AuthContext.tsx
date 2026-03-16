import { createContext, useState, useEffect, useContext } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import api from "../services/api";
import { initializeEcho } from "../services/echo";


const AuthContext = createContext();

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [echo, setEcho] = useState(null);

  useEffect(() => {
    loadUser();
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
}, [user,echo]);

  const loadUser = async () => {
    try {
      const token = await AsyncStorage.getItem("token");

      if (!token) {
        setLoading(false);
        return;
      }

      api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
      const response = await api.get("/me");
      setUser(response.data);
    } catch (error) {
      console.log("LoadUser Error:", error);
      
      //clear token if it's invalid
      await AsyncStorage.removeItem("token");
    } finally {
      setLoading(false);
    }
  };

  const login = async (email, password) => {
    try {
      const response = await api.post("/login", { email, password });
      const token = response.data.token;
      
      await AsyncStorage.setItem("token", token);
      api.defaults.headers.common["Authorization"] = `Bearer ${token}`;

      const userResponse = await api.get("/me");
      setUser(userResponse.data);
    } catch (error) {
      if (error.response) {
        throw new Error(error.response.data?.message || "Login failed");
      } else {
        throw new Error("Login failed");
      }
    }
  };

  const logout = async () => {
    try {
      // Disconnect socket immediately on logout
      if (echo) {
        echo.disconnect();
        setEcho(null);
      }
      await api.post("/logout");
    } catch (error) {
      console.log("Logout API Error:", error);
    } finally {
      await AsyncStorage.removeItem("token");
      delete api.defaults.headers.common["Authorization"];
      setUser(null);
    }
  };

  const register = async (name, email, phone, password, password_confirmation) => {
    try {
      await api.post("/register", { name, email, phone, password, password_confirmation });
    } catch (error) {
      throw new Error(error.response?.data?.message || "Registration failed");
    }
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, register, echo }}>
      {children}
    </AuthContext.Provider>
  );
}

export const useAuth = () => useContext(AuthContext);