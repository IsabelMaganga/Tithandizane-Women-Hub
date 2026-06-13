// app/_layout.tsx
import "react-native-get-random-values";
import "react-native-url-polyfill/auto";
import { useEffect, useState } from "react";
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

// Must be called at module level before any component mounts.
// On web, hideAsync() is a no-op unless preventAutoHideAsync() was called first.
SplashScreen.preventAutoHideAsync().catch(() => {});

const hideSplash = () => SplashScreen.hideAsync().catch(() => {});

function Navigation() {
  const router = useRouter();
  const segments = useSegments();
  const { user, loading } = useAuth();
  const [firstLaunch, setFirstLaunch] = useState<boolean | null>(null);

  // Fallback: force-hide the splash after 3 s in case the guard effect is
  // delayed by slow async storage or auth checks (especially on web).
  useEffect(() => {
    const t = setTimeout(hideSplash, 3000);
    return () => clearTimeout(t);
  }, []);

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
      } catch {
        setFirstLaunch(false);
      }
    };
    checkFirstLaunch();
  }, []);

  // 2. Routing guard — runs once auth + onboarding state are both known
  useEffect(() => {
    if (firstLaunch === null || loading) return;

    const inAuthGroup = segments[0] === "(auth)";
    const inOnboardingGroup = segments[0] === "(onboarding)";
    const inProtectedRoute = segments[0] === "(protected)";

    // --- CASE A: ONBOARDING ---
    if (firstLaunch && !inOnboardingGroup) {
      router.replace("/(onboarding)/splash");
      hideSplash();
      return;
    }

    // --- CASE B: UNAUTHENTICATED ---
    if (!user) {
      if (inProtectedRoute) {
        router.replace("/(auth)/login");
      }
      hideSplash();
      return;
    }

    // --- CASE C: AUTHENTICATED ---
    if (inAuthGroup || inOnboardingGroup) {
      router.replace("/(protected)/(tabs)");
    }

    hideSplash();
  }, [user, loading, firstLaunch, segments]);

  return (
    <Stack
      screenOptions={{
        headerShown: false,
        animation: 'fade', // Clean transition for role-switching
      }}
    >
      {/* Define explicit stacks if needed, otherwise Slot/Stack handles it */}
      <Stack.Screen name="(auth)" options={{ gestureEnabled: false }} />
      <Stack.Screen name="(protected)" options={{ gestureEnabled: false }} />
    </Stack>
  );
}

export default function RootLayout() {
  return (
    <AuthProvider>
      <LanguageProvider> {/* Wrap with LanguageProvider */}
        <Navigation />
        <Toast position="top" topOffset={60} />
      </LanguageProvider>
    </AuthProvider>
  );
}