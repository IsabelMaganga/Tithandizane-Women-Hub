// app/_layout.tsx
import "react-native-get-random-values";
import "react-native-url-polyfill/auto";
import { useEffect, useState, useCallback } from "react";
import { Stack, useRouter, useSegments } from "expo-router";
import AsyncStorage from "@react-native-async-storage/async-storage";
import * as SplashScreen from "expo-splash-screen";
import Toast from "react-native-toast-message";

// Context & Providers
import { AuthProvider, useAuth } from "../context/AuthContext";
import { LanguageProvider } from "../context/LanguageContext";

// Global Styles & i18n
import "../global.css";
import "../src/i18n/i18n";

function Navigation() {
  const router = useRouter();
  const segments = useSegments();
  const { user, loading } = useAuth();
  const [firstLaunch, setFirstLaunch] = useState<boolean | null>(null);

  // 1. Initialize App State (Onboarding check)
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

  // 2. Navigation Guard Logic
  useEffect(() => {
    // Wait until both Auth and AsyncStorage are ready
    if (firstLaunch === null || loading) return;

    const inAuthGroup = segments[0] === "(auth)";
    const inOnboardingGroup = segments[0] === "(onboarding)";
    const inProtectedRoute = segments[0] === "(protected)";

    // --- CASE A: ONBOARDING ---
    // Only redirect to onboarding if NOT already in onboarding or auth
    // This prevents the guard from bouncing the user back after "Get Started"
    if (firstLaunch && !inOnboardingGroup && !inAuthGroup) {
      router.replace("/(onboarding)/splash");
      SplashScreen.hideAsync();
      return;
    }

    // --- CASE B: UNAUTHENTICATED ---
    if (!user) {
      if (inProtectedRoute) {
        router.replace("/(auth)/login");
      }
      SplashScreen.hideAsync();
      return;
    }

    // --- CASE C: AUTHENTICATED (USER LOGGED IN) ---
    if (user) {
      if (inAuthGroup || inOnboardingGroup) {
        router.replace("/(protected)/(tabs)");
      }
    }

    SplashScreen.hideAsync();
  }, [user, loading, firstLaunch, segments]);

  return (
    <Stack
      screenOptions={{
        headerShown: false,
        animation: "fade",
      }}
    >
      <Stack.Screen name="(auth)" options={{ gestureEnabled: false }} />
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