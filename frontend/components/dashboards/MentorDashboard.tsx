import React from 'react';
import { View, Text, TouchableOpacity, ScrollView, Pressable, Dimensions, Alert } from 'react-native';
import { StatusBar } from 'expo-status-bar';
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from 'expo-router';
import * as Haptics from 'expo-haptics';
import { useTranslation } from "react-i18next";
import Animated, { FadeInDown, FadeInRight } from "react-native-reanimated";
import { MaterialCommunityIcons, MaterialIcons, FontAwesome6, Ionicons, Feather } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { LineChart } from "react-native-chart-kit"; // Switched to stable kit
import { useAuth } from '../../context/AuthContext';
import { useThemeToggle } from "../../hooks/useTheme";
import Profile from '../Profile';

const { width } = Dimensions.get('window');

export default function UserDashboard() {
  const router = useRouter();
  const { user } = useAuth();
  const { colorScheme } = useThemeToggle();
  const { t } = useTranslation();
  const isDark = colorScheme === "dark";

  const handleNavigation = (route: string) => {
    Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Medium);
    router.push(route as any);
  };

  const handleUnderConstruction = () => {
    Haptics.notificationAsync(Haptics.NotificationFeedbackType.Warning);
    Alert.alert(
      "Coming Soon",
      "The Sisterhood Forum is currently under construction.",
      [{ text: "OK" }]
    );
  };

  // Chart Configuration
  const chartConfig = {
    backgroundGradientFrom: isDark ? "#0f172a" : "#ffffff",
    backgroundGradientTo: isDark ? "#0f172a" : "#ffffff",
    color: (opacity = 1) => `rgba(124, 58, 237, ${opacity})`,
    labelColor: (opacity = 1) => isDark ? `rgba(255, 255, 255, ${opacity})` : `rgba(100, 116, 139, ${opacity})`,
    strokeWidth: 3,
    propsForDots: { r: "5", strokeWidth: "2", stroke: "#7c3aed" },
    decimalPlaces: 0,
  };

  const chartData = {
    labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
    datasets: [{ data: [2, 5, 3, 8, 5, 9] }]
  };

  return (
    <View className="flex-1 bg-gray-50 dark:bg-slate-950">
      <StatusBar style={isDark ? "light" : "dark"} />

      <SafeAreaView className="flex-1">
        {/* --- Header --- */}
        <View className="flex-row items-center justify-between px-6 py-4">
          <View className="flex-row items-center space-x-3">
            <Pressable 
              onPress={() => handleNavigation("/(protected)/settingsScreen")}
              className="border-2 border-violet-200 dark:border-slate-700 rounded-full p-0.5"
            >
              <Profile />
            </Pressable>
            <View>
              <Text className="text-gray-400 text-xs font-bold uppercase tracking-tighter">
                {t("welcome_back")},
              </Text>
              <Text className="text-slate-900 dark:text-white font-black text-xl">
                {user?.name || "Sister"}
              </Text>
            </View>
          </View>
          <TouchableOpacity
            onPress={() => handleNavigation("/notificationScreen")}
            className="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl items-center justify-center shadow-sm border border-gray-100 dark:border-slate-700"
          >
            <Ionicons name="notifications-outline" size={24} color={isDark ? "white" : "#1e293b"} />
          </TouchableOpacity>
        </View>

        <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={{ paddingBottom: 100 }}>

          {/*SESSION REQUESTS */}
          <Animated.View entering={FadeInDown.delay(200)} className="px-6 mt-4">
            <LinearGradient
              colors={['#7c3aed', '#4f46e5']}
              start={{ x: 0, y: 0 }}
              end={{ x: 1, y: 1 }}
              className="rounded-[32px] p-6 shadow-xl shadow-violet-300 dark:shadow-none"
            >
              <View className="flex-row justify-between items-start">
                <View>
                  <Text className="text-violet-100 font-medium text-sm">Session Requests</Text>
                  <Text className="text-white text-3xl font-black mt-1">02 Pending</Text>
                  <View className="bg-white/20 self-start px-3 py-1 rounded-full mt-2">
                    <Text className="text-white text-[10px] font-bold tracking-widest uppercase">Awaiting Mentor</Text>
                  </View>
                </View>
                <MaterialCommunityIcons name="clock-fast" size={48} color="rgba(255,255,255,0.3)" />
              </View>

              <TouchableOpacity
                onPress={() => handleNavigation("/(protected)/sessionsDashboard")}
                className="mt-6 flex-row justify-between items-center bg-white/20 p-4 rounded-2xl"
              >
                <Text className="text-white text-xs font-bold">View Recent Requests</Text>
                <Feather name="arrow-right" size={16} color="white" />
              </TouchableOpacity>
            </LinearGradient>
          </Animated.View>

          {/* ANALYTICS CHART SECTION */}
          <View className="px-6 mt-8">
            <Text className="text-slate-900 dark:text-white font-bold text-lg mb-4">Hub Activity</Text>
            <View className="bg-white dark:bg-slate-900 rounded-[28px] py-4 items-center border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
              <LineChart
                data={chartData}
                width={width - 48}
                height={160}
                chartConfig={chartConfig}
                bezier
                style={{ marginLeft: -20, borderRadius: 28 }}
                withInnerLines={false}
                withOuterLines={false}
                withHorizontalLabels={true}
                withVerticalLabels={true}
              />
            </View>
          </View>

          {/* QUICK ACTIONS GRID */}
          <Text className="px-8 mt-10 text-slate-500 dark:text-white font-black text-lg mb-4">Core Services</Text>
          <View className="flex-row flex-wrap px-4 border-t border-gray-200 dark:border-slate-700 pt-2">
            <GridItem 
              title="Mentorship" 
              icon="hands-holding-child" 
              color="#8b5cf6" 
              onPress={() => handleNavigation("/(protected)/sessionsDashboard")} 
            />
            <GridItem 
              title="Emergency" 
              icon="shield-alert" 
              color="#f43f5e" 
              onPress={() => handleNavigation("/(protected)/emergencyScreen")} 
              isMaterial
            />
            <GridItem 
              title="Hygiene Hub" 
              icon="water-outline" 
              color="#0ea5e9" 
              onPress={() => handleNavigation("/(protected)/menstrualHealthScreen")} 
              isIonicons
            />
            <GridItem 
              title="Reports" 
              icon="flag" 
              color="#f59e0b" 
              onPress={() => handleNavigation("/(protected)/reportHarrasmentScreen")} 
            />
          </View>

          {/* COMMUNITY HUB CARD*/}
          <Animated.View entering={FadeInRight.delay(400)} className="px-6 mt-6">
            <TouchableOpacity 
              onPress={()=>handleNavigation("/(protected)/community")}
              className="bg-slate-900 dark:bg-slate-800 p-6 rounded-[28px] flex-row items-center justify-between"
            >
              <View className="flex-row items-center">
                <View className="w-12 h-12 bg-white/10 rounded-2xl items-center justify-center">
                  <Ionicons name="chatbubbles-outline" size={24} color="white" />
                </View>
                <View className="ml-4">
                  <Text className="text-white font-bold text-base">Community Hub</Text>
                  <Text className="text-slate-400 text-xs italic">space for all</Text>
                </View>
              </View>
              
            </TouchableOpacity>
          </Animated.View>

        </ScrollView>

        {/*EMERGENCY FAB */}
        <TouchableOpacity 
          activeOpacity={0.9}
          onPress={() => handleNavigation("/(protected)/emergencyScreen")}
          className="absolute bottom-10 right-6 w-16 h-16 bg-rose-600 rounded-full items-center justify-center shadow-2xl border-4 border-white dark:border-slate-900"
        >
          <MaterialIcons name="sos" size={32} color="white" />
        </TouchableOpacity>
      </SafeAreaView>
    </View>
  );
}

// Reusable Grid Component
const GridItem = ({ title, icon, color, onPress, isMaterial, isIonicons }: any) => (
  <TouchableOpacity 
    onPress={onPress}
    style={{ width: '50%', padding: 8 }}
    activeOpacity={0.7}
  >
    <View className="p-6  bg-white shadow-xl dark:bg-slate-900  rounded-[32px]  items-center">
      <View style={{ backgroundColor: `${color}15` }} className="p-4 rounded-2xl mb-3">
        {isMaterial ? (
          <MaterialCommunityIcons name={icon} size={26} color={color} />
        ) : isIonicons ? (
          <Ionicons name={icon} size={26} color={color} />
        ) : (
          <FontAwesome6 name={icon} size={22} color={color} />
        )}
      </View>
      <Text className="text-slate-800 dark:text-slate-200 font-bold text-xs">{title}</Text>
    </View>
  </TouchableOpacity>
);