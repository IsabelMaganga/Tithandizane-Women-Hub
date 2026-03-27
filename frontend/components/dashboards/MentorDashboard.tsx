import React, { useMemo } from 'react';
import { View, ImageBackground, Image, Text, TouchableOpacity, ScrollView, Pressable } from 'react-native';
import { StatusBar } from 'expo-status-bar';
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from 'expo-router';
import * as Haptics from 'expo-haptics';
import { useTranslation } from "react-i18next";
import Animated, { FadeInDown, BounceIn } from "react-native-reanimated";
import { MaterialCommunityIcons, MaterialIcons, FontAwesome6, Ionicons } from '@expo/vector-icons';
import { useAuth } from '../../context/AuthContext';
import { useThemeToggle } from "../../hooks/useTheme";
import Profile from '../Profile';
import { MenuItem } from '../MenuItem';
import { NotFound } from '../NotFound';


const getUserMenuConfig = (t: any) => [
  {
    id: 'mentorship',
    title: t("mentorship"),
    icon: "hands-holding-child",
    bgColor: "bg-violet-500",
    family: FontAwesome6,
    route: "/(protected)/sessionsDashboard",
  },
  {
    id: 'emergency',
    title: t("emergency_help"),
    icon: "contact-emergency",
    bgColor: "bg-orange-500",
    family: MaterialIcons,
    route: "/(protected)/emergencyScreen",
  },
  {
    id: 'menstrual',
    title: t("menstrual_health"),
    icon: "calendar-days",
    bgColor: "bg-emerald-500",
    family: FontAwesome6,
    route: "/(protected)/menstrualHealthScreen",
  },
  {
    id: 'report',
    title: t("report_harassment"),
    icon: "report-problem",
    bgColor: "bg-rose-500",
    family: MaterialIcons,
    route: "/(protected)/reportHarrasmentScreen",
  },
];

export default function UserDashboard() {
  const router = useRouter();
  const { user } = useAuth();
  const { colorScheme } = useThemeToggle();
  const { t } = useTranslation();

  const isDark = colorScheme === "dark";
  const menuItems = useMemo(() => getUserMenuConfig(t), [t]);

  const handleNavigation = (route: string) => {
    Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Light);
    router.push(route as any);
  };

  return (
    <View className="flex-1 bg-gray-50 dark:bg-slate-900">
      <StatusBar style={isDark ? "light" : "dark"} />


      <View className="absolute top-0 left-0 right-0">
        <ImageBackground
          source={require('../../assets/images/Ellipse 4.png')}
          className="w-full h-[305px]"
          resizeMode="cover"
        >
          <Image
            source={require('../../assets/images/shape (1).png')}
            className="absolute top-0 left-0 w-32 h-32 opacity-60"
          />
        </ImageBackground>
      </View>

      <SafeAreaView className="flex-1">
        {/* Header Section */}
        <View className="flex-row items-center justify-between px-6 mt-4">
          <View className="flex-row items-center space-x-3">
            <Pressable 
              onPress={() => handleNavigation("/(protected)/profileScreen")}
              className="active:opacity-80"
            >
              <View className="border-2 border-white rounded-full shadow-sm">
                <Profile />
              </View>
            </Pressable>
            <View>
              <Text className="text-gray-500 dark:text-gray-300 text-xs font-medium uppercase tracking-widest">
                {t("welcome")}
              </Text>
              <Text className="text-slate-800 dark:text-white font-bold text-xl">
                {user?.name ?? t("guest")}
              </Text>
            </View>
          </View>

          <TouchableOpacity
            onPress={() => handleNavigation("/notificationScreen")}
            className="p-2 bg-white/20 rounded-full backdrop-blur-md"
          >
            <Ionicons name="notifications" size={24} color="white" />
          </TouchableOpacity>
        </View>

        {/* User Content Area */}
        <ScrollView
          contentContainerStyle={{ paddingHorizontal: 20, paddingTop: 40,marginTop:200,marginLeft:20 }}
          showsVerticalScrollIndicator={false}
        >
          {/* Main Grid */}
          <View className="flex-row flex-wrap justify-center items-center">
            {menuItems.map((item, index) => (
              <Animated.View
                key={item.id}
                entering={FadeInDown.delay(index * 100).duration(500)}
                style={{ width: '50%', marginBottom: 16 }}
              >
                <MenuItem
                  title={item.title}
                  icon={item.icon}
                  bgColor={item.bgColor}
                  family={item.family}
                  onPress={() => handleNavigation(item.route)}
                />
              </Animated.View>
            ))}
          </View>

          {/* Featured Action Card for Users */}
          <Animated.View entering={FadeInDown.delay(500)}>
            <TouchableOpacity 
              className="w-full bg-white dark:bg-slate-800 p-6 rounded-[32px] mt-4 flex-row items-center border border-gray-100 dark:border-slate-700 shadow-xl shadow-black/5"
              onPress={() => handleNavigation("/community")}
            >
              <View className="bg-emerald-100 dark:bg-emerald-900/30 p-4 rounded-2xl mr-4">
                <FontAwesome6 name="users-line" size={24} color="#10b981" />
              </View>
              <View className="flex-1">
                <Text className="text-slate-800 dark:text-white font-bold text-base">
                  {t("join_community")}
                </Text>
                <Text className="text-slate-500 dark:text-slate-400 text-xs mt-1">
                  {t("community_desc")}
                </Text>
              </View>
              <MaterialIcons name="chevron-right" size={24} color="#94a3b8" />
            </TouchableOpacity>
          </Animated.View>
        </ScrollView>

        {/* Floating FAQ Bot */}
        <Animated.View
          entering={BounceIn.delay(800)}
          className="absolute bottom-10 right-8 shadow-2xl"
        >
          <TouchableOpacity 
            onPress={() => <NotFound description="Service not available for now" />}
            activeOpacity={0.8}
            className="bg-white dark:bg-slate-800 w-16 h-16 rounded-full items-center justify-center border border-gray-100 dark:border-slate-700 shadow-xl"
          >
            <MaterialCommunityIcons 
              name="robot-confused" 
              size={28} 
              color={isDark ? "#a78bfa" : "#7c3aed"} 
            />
            <Text className="text-gray-500 dark:text-gray-400 text-[10px] font-bold">
              {t("FAQ")}
            </Text>
          </TouchableOpacity>
        </Animated.View>
      </SafeAreaView>
    </View>
  );
}