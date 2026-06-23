// app/(protected)/_layout.tsx
import { Stack } from "expo-router";
import { useAuth } from "../../context/AuthContext";
import { ActivityIndicator, View } from "react-native";

export default function ProtectedLayout() {
  const { user, loading } = useAuth();

 

  if (loading) {
    return (
      <View style={{ flex: 1, justifyContent: "center", alignItems: "center" }}>
        <ActivityIndicator size="large" color="#7c3aed" />
      </View>
    );
  }

  // ✅ Removed the !user render block too — root layout handles this redirect
  // Rendering "Redirecting..." while root layout is already navigating
  // caused a flash + double navigation

  return (
    <Stack screenOptions={{ headerShown: false }}>
      <Stack.Screen name="(tabs)"                    options={{ headerShown: false }} />
      <Stack.Screen name="articles/[id]"             options={{ headerShown: false }} />
      <Stack.Screen name="emergencyScreen"           options={{ headerShown: false }} />
      <Stack.Screen name="mentorshipScreen"          options={{ headerShown: false }} />
      <Stack.Screen name="reportHarrasmentScreen"    options={{ headerShown: false }} />
      <Stack.Screen name="menstrualHealthScreen"     options={{ headerShown: false }} />
      <Stack.Screen name="chat/[id]"                 options={{ headerShown: false }} />
      <Stack.Screen name="settingsScreen"            options={{ headerShown: false }} />
      <Stack.Screen name="usersScreen"               options={{ headerShown: false }} />
      <Stack.Screen name="group-info/[id]"           options={{ headerShown: true  }} />
      <Stack.Screen name="user-info/[id]"            options={{ headerShown: false }} />
      <Stack.Screen name="sessionsDashboard"         options={{ headerShown: false }} />
      <Stack.Screen name="editProfile"               options={{ headerShown: false }} />
      <Stack.Screen name="changePasswordScreen"      options={{ headerShown: false }} />
      <Stack.Screen name="twoFactorAuthScreen"       options={{ headerShown: false }} />
      <Stack.Screen name="aboutScreen"               options={{ headerShown: false }} />
      <Stack.Screen name="privacyPolicyScreen"       options={{ headerShown: false }} />
      <Stack.Screen name="guidanceScreen"            options={{ headerShown: false }} />
      <Stack.Screen name="contentDetailScreen"       options={{ headerShown: false }} />
      <Stack.Screen name="myContentScreen"           options={{ headerShown: false }} />
      <Stack.Screen name="publishContentScreen"      options={{ headerShown: false }} />
      <Stack.Screen name="notificationScreen"        options={{ headerShown: false }} />
    </Stack>
  );
}