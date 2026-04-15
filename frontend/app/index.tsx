import { useEffect } from "react";
import { useRouter } from "expo-router";
import { View, ActivityIndicator } from "react-native";
import AsyncStorage from "@react-native-async-storage/async-storage";

export default function Index() {
  const router = useRouter();

  useEffect(() => {
    // Check first launch status
    const checkAndRedirect = async () => {
      const opened = await AsyncStorage.getItem("alreadyOpened");
      if (!opened) {
        router.replace("/(onboarding)/splash");
      } else {
        // Check auth status and redirect accordingly
        router.replace("/(auth)/login");
      }
    };
    
    checkAndRedirect();
  }, []);

  return (
    <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
      <ActivityIndicator size="large" />
    </View>
  );
}
