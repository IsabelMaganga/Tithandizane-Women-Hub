import { Stack, useRouter } from "expo-router";
import { useAuth } from "../../context/AuthContext";
import { ActivityIndicator, View, Text } from "react-native";
import { useEffect } from "react";

export default function ProtectedLayout() {
  const { user, loading } = useAuth();
  const router = useRouter();

  useEffect(() => {
    if (!loading && !user) {
      router.replace("/(auth)/login");
    }
  }, [user, loading]);

  if (loading)
    return (
      <View style={{ flex: 1, justifyContent: "center", alignItems: "center" }}>
        <ActivityIndicator size="large" />
      </View>
    );

  if (!user) {

    return (
      <View style={{ flex: 1, justifyContent: "center", alignItems: "center" }}>
        <Text>Redirecting to login...</Text>
      </View>
    );
  }

  return <Stack screenOptions={{ headerShown: false }}>
    <Stack.Screen name="(tabs)"  options={ { headerShown:false } }/>
    <Stack.Screen name="articles/[id]" options={ { headerShown:false,headerTitle: "Article Details" } } />
    <Stack.Screen name="emergencyScreen" options={ { headerShown:false,headerTitle: "Emergency Contacts" } } />
    <Stack.Screen name="mentorshipScreen" options={ { headerShown:false,headerTitle: "Mentorship" } } />
    <Stack.Screen name="reportHarrasmentScreen" options={ { headerShown:false,headerTitle: "Report Harassment" } } />
    <Stack.Screen name="menstrualHealthScreen" options={ { headerShown:false,headerTitle: "Menstrual Health" } } />
    <Stack.Screen name="chat/[id]" options={ { headerShown:true,headerTitle: `${Conversation.name}` } } />
    <Stack.Screen name="profileScreen" options={ { headerShown:true,headerTitle: "Settings" } } />

  </Stack>
}