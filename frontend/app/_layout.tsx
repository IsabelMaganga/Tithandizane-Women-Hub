// app/_layout.tsx
import "react-native-get-random-values";
import "react-native-url-polyfill/auto";
import { useEffect, useState, useRef } from "react";
import { Stack, useRouter, useSegments } from "expo-router";
import AsyncStorage from "@react-native-async-storage/async-storage";
import * as SplashScreen from "expo-splash-screen";
import Toast from "react-native-toast-message";

import { AuthProvider, useAuth } from "../context/AuthContext";
import { LanguageProvider } from "../context/LanguageContext";
import "../global.css";
import "../src/i18n/i18n";

function Navigation() {
  const router = useRouter();
  const segments = useSegments();
  const { user, loading } = useAuth();
  const [firstLaunch, setFirstLaunch] = useState<boolean | null>(null);

  const userId = user?.id ?? null;
  const isNavigating = useRef(false);

  useEffect(() => {
    const checkFirstLaunch = async () => {
      try {
        const opened = await AsyncStorage.getItem("alreadyOpened");
        if (!opened) {
          await AsyncStorage.setItem("alreadyOpened", "true");
          setFirstLaunch(true);
        } else {
          setFirstLaunch(false);
        }
      } catch (e) {
        setFirstLaunch(false);
      }
    };
    checkFirstLaunch();
  }, []);

  useEffect(() => {
    if (firstLaunch === null || loading) return;
    if (isNavigating.current) return;

    const currentSegment = segments[0] ?? null;
    const inOnboardingGroup = currentSegment === "(onboarding)";
    const inAuthGroup       = currentSegment === "(auth)";
    const inProtectedRoute  = currentSegment === "(protected)";

    // ✅ CASE 0: User is authenticated — reset firstLaunch so
    // onboarding guard never fires for logged-in users
    if (userId && firstLaunch) {
      setFirstLaunch(false);
      SplashScreen.hideAsync();
      return;
    }

    // ✅ CASE A: ONBOARDING — first time unauthenticated user
    if (firstLaunch && !inOnboardingGroup && !inAuthGroup) {
      isNavigating.current = true;
      setTimeout(() => {
        router.replace("/(onboarding)/splash");
        setTimeout(() => { isNavigating.current = false; }, 500);
      }, 0);
      SplashScreen.hideAsync();
      return;
    }

    // ✅ CASE B: AUTHENTICATED — already logged in, send to dashboard
    // This handles app relaunch when token is still valid
    if (userId && !inProtectedRoute) {
      isNavigating.current = true;
      setTimeout(() => {
        router.replace("/(protected)/(tabs)");
        setTimeout(() => { isNavigating.current = false; }, 500);
      }, 0);
      SplashScreen.hideAsync();
      return;
    }

    // ✅ CASE C: UNAUTHENTICATED — protect private routes
    if (!userId && inProtectedRoute) {
      isNavigating.current = true;
      setTimeout(() => {
        router.replace("/(auth)/login");
        setTimeout(() => { isNavigating.current = false; }, 500);
      }, 0);
    }

    SplashScreen.hideAsync();

  }, [userId, loading, firstLaunch, segments[0]]);

  return (
    <Stack screenOptions={{ headerShown: false, animation: "fade" }}>
      <Stack.Screen name="(auth)"      options={{ gestureEnabled: false }} />
      <Stack.Screen name="(protected)" options={{ gestureEnabled: false }} />
    </Stack>
  );
}

export default function RootLayout() {
  return (
    <AuthProvider>
      <LanguageProvider>
        <Navigation />
        <Toast position="top" topOffset={60} />
      </LanguageProvider>
    </AuthProvider>
  );
}