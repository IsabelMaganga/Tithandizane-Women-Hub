// hooks/useAuth.ts
// ✅ This file only exports the token utility helper.
// For the useAuth hook, always import from: context/AuthContext

import AsyncStorage from "@react-native-async-storage/async-storage";

// ✅ Kept only for legacy WebSocket/echo service calls that need
// a one-time token read outside of React components.
// Inside React components, use: const { token } = useAuth() from context/AuthContext
export const getUserToken = async (): Promise<string | null> => {
  try {
    const token = await AsyncStorage.getItem("token");
    return token;
  } catch (error) {
    console.error("Error getting token:", error);
    return null;
  }
};