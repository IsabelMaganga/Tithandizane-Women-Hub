import "react-native-get-random-values";
import "react-native-url-polyfill/auto";
import { useEffect, useState, useCallback } from "react";
import { Stack, useRouter, useSegments } from "expo-router";
import AsyncStorage from "@react-native-async-storage/async-storage";
import * as SplashScreen from "expo-splash-screen";
import Toast from "react-native-toast-message";

// Context & Providers
import { AuthProvider, useAuth } from "../context/AuthContext";

// Global Styles & i18n
import "../global.css";
import "../src/i18n/i18n";

// Prevent the splash screen from auto-hiding before we check Auth/Onboarding state
SplashScreen.preventAutoHideAsync();

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
          // Keep this false for production if you want onboarding to show every time until finished
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

  // 2. High-Performance Guard Logic
  useEffect(() => {
    // Wait until both Auth and AsyncStorage are ready
    if (firstLaunch === null || loading) return;

    const inAuthGroup = segments[0] === "(auth)";
    const inOnboardingGroup = segments[0] === "(onboarding)";
    const inProtectedRoute = segments[0] === "(protected)";
    const isMentorRoute = segments[1] === "(mentor)";

    // --- CASE A: ONBOARDING ---
    if (firstLaunch && !inOnboardingGroup) {
      router.replace("/(onboarding)/splash");
      SplashScreen.hideAsync();
      return;
    }

    // --- CASE B: UNAUTHENTICATED ---
    if (!user) {
      // If they are trying to access protected content, send to login
      if (inProtectedRoute) {
        router.replace("/(auth)/login");
      }
      SplashScreen.hideAsync();
      return;
    }

    // --- CASE C: AUTHENTICATED (USER LOGGED IN) ---
    if (user) {
  // If user is logged in but hits Auth/Onboarding, send them to the Tab root
  if (inAuthGroup || inOnboardingGroup) {
    // Both roles now go to the same place!
    router.replace("/(protected)/(tabs)");
  }
}

    // Final hide of splash screen once routing is settled
    SplashScreen.hideAsync();
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
      <Navigation />
      {/* Toast is outside Navigation so it stays mounted during redirects */}
      <Toast position="top" topOffset={60} />
    </AuthProvider>
  );
}