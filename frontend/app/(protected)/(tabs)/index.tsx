// app/(protected)/(tabs)/index.tsx
import { useAuth } from "../../../context/AuthContext";
import UserDashboard from "../../../components/dashboards/UserDashboard";
import MentorDashboard from "../../../components/dashboards/MentorDashboard";
import { View, ActivityIndicator } from "react-native";

export default function Index() {
  const { user, loading } = useAuth();

  if (loading) {
    return (
      <View style={{ flex: 1, justifyContent: "center", alignItems: "center" }}>
        <ActivityIndicator size="large" color="#7c3aed" />
      </View>
    );
  }

  // ✅ Show the correct dashboard based on role
  if (user?.role === "mentor") {
    return <MentorDashboard />;
  }

  return <UserDashboard />;
}