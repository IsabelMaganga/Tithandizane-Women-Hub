import { useState, useEffect } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import api from "../services/api";


export function useAuth() {

  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    checkUser();
  }, []);


  const checkUser = async () => {
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
      console.log("Auth error:", error);
    } finally {
      setLoading(false);
    }
  };

  return { user, loading };
}


export const getUserToken = async (): Promise<string | null> => {
  try {
    const token = await AsyncStorage.getItem("token");
    return token;
  } catch (error) {
    console.error("Error getting token:", error);
    return null;
  }
};
