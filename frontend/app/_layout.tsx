import { Stack, useRouter, useSegments } from "expo-router";
import { useEffect, useState } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { useAuth } from "../hooks/useAuth";
import * as SplashScreen from "expo-splash-screen";
import "../global.css";

SplashScreen.preventAutoHideAsync(); // Keep splash screen visible until ready

//AsyncStorage.clear(); // Clear AsyncStorage for testing purposes
export default function RootLayout() {
  const router = useRouter();
  const segments = useSegments();
  const { user, loading } = useAuth();
  const [firstLaunch, setFirstLaunch] = useState<boolean | null>(null);

  useEffect(() => {
    const init = async () => {
      const opened = await AsyncStorage.getItem("alreadyOpened");
      if (opened === null) {
        await AsyncStorage.setItem("alreadyOpened", "true");
        setFirstLaunch(true);
      } else {
        setFirstLaunch(false);
      }

      // Hide splash once we know firstLaunch
      await SplashScreen.hideAsync();
    };
    init();
  }, []);

  useEffect(() => {
    if (firstLaunch === null || loading) return;

    const group = segments[0];
    const inOnboarding = group === "(onboarding)";
    const inAuth = group === "(auth)";
    const inProtected = group === "(protected)";

    if (firstLaunch && !inOnboarding) {
      router.replace("/(onboarding)/splash");
      return;
    }

    if (!user && inProtected) {
      router.replace("/(auth)/login");
      return;
    }

    if (user && inAuth) {
      router.replace("/(protected)/(tabs)");
      return;
    }
  }, [firstLaunch, user, loading, segments]);

  return <Stack screenOptions={{ headerShown: false }} />;
}