import React from 'react';
import { useAuth } from '../../../context/AuthContext';
import UserDashboard from '../../../components/dashboards/UserDashboard';
import MentorDashboard from '../../../components/dashboards/MentorDashboard';
import { ActivityIndicator, View } from 'react-native';

export default function SmartLandingPage() {
  const { user, loading } = useAuth();

  // 1. Safety check for the "Hydration" period
  if (loading || !user) {
    return (
      <View className="flex-1 justify-center items-center bg-gray-50 dark:bg-slate-900">
        <ActivityIndicator size="large" color="#7c3aed" />
      </View>
    );
  }

  // deciding which landing screen to show
  return user.role === 'mentor' ? <MentorDashboard /> : <UserDashboard />;
}