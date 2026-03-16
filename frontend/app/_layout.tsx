import "react-native-get-random-values";
import "react-native-url-polyfill/auto";

import { Stack, useRouter, useSegments } from "expo-router";
import { useEffect, useState } from "react";
import AsyncStorage from "@react-native-async-storage/async-storage";
import { AuthProvider, useAuth } from "../context/AuthContext";
import * as SplashScreen from "expo-splash-screen";
import "../global.css";
import "../src/i18n/i18n";
import Toast from "react-native-toast-message";

SplashScreen.preventAutoHideAsync();

function Navigation() {
  const router = useRouter();
  const segments = useSegments();
  const { user, loading } = useAuth();
  const [firstLaunch, setFirstLaunch] = useState(null);

  useEffect(() => {
    const init = async () => {
      const opened = await AsyncStorage.getItem("alreadyOpened");

      if (!opened) {
        await AsyncStorage.setItem("alreadyOpened", "true");
        setFirstLaunch(true);
      } else {
        setFirstLaunch(false);
      }
    };

    init();
  }, []);

  useEffect(() => {
    if (firstLaunch === null || loading) return;

    SplashScreen.hideAsync(); // hide immediately

    const group = segments[0];

    if (firstLaunch && group !== "(onboarding)") {
      router.replace("/(onboarding)/splash");
      return;
    }

    if (!user && group === "(protected)") {
      router.replace("/(auth)/login");
      return;
    }

    if (user && group === "(auth)") {
      router.replace("/(protected)/(tabs)");
      return;
    }
  }, [firstLaunch, user, loading, segments]);

  return <Stack screenOptions={{ headerShown: false }} />;
}

export default function RootLayout() {
  return (
    <AuthProvider>
      <Navigation />
        <Toast
        position="top"
        topOffset={60}
      />
    </AuthProvider>
  );
}