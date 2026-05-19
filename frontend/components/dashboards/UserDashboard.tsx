import React from 'react';
import { StatusBar } from 'expo-status-bar';
import { View, ImageBackground, Image, Text, Pressable, TouchableOpacity, ScrollView } from 'react-native';
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from 'expo-router';
import { useAuth } from '../../context/AuthContext';
import Profile from '../Profile';
import { useTranslation } from "react-i18next";
import { useThemeToggle } from "../../hooks/useTheme";
import { MaterialCommunityIcons, MaterialIcons, FontAwesome6, Ionicons, Feather } from '@expo/vector-icons';
import { MenuItem } from '../MenuItem';
import Animated, { FadeInDown, BounceIn, FadeInRight } from "react-native-reanimated";
import { LinearGradient } from 'expo-linear-gradient';

export default function UserDashboard() {
  const router = useRouter();
  const { user } = useAuth();
  const { colorScheme } = useThemeToggle();
  const { t } = useTranslation();
  const isDark = colorScheme === "dark";

  return (
    <View className="flex-1 bg-gray-50 dark:bg-slate-950">
      <StatusBar style={isDark ? "light" : "dark"} />
      
      {/* Background Aesthetic Layer */}
      <View className="absolute top-0 left-0 right-0">
        <ImageBackground
          source={require('../../assets/images/Ellipse 4.png')}
          className="w-full h-[280px]"
          resizeMode="cover"
        >
          <LinearGradient
            colors={isDark ? ['rgba(15,23,42,0.8)', '#0f172a'] : ['rgba(255,255,255,0.1)', '#f9fafb']}
            className="absolute inset-0"
          />
        </ImageBackground>
      </View>

      <SafeAreaView className="flex-1">
        {/* --- TOP NAV BAR --- */}
        <View className="flex-row items-center justify-between px-6 py-4">
          <View className="flex-row items-center space-x-3">
            <Pressable 
              onPress={() => router.push("/settingsScreen")}
              className="border-2 border-white dark:border-slate-700 rounded-full shadow-sm"
            >
              <Profile />
            </Pressable>
            <View>
              <Text className="text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase tracking-widest">
                {t("welcome_back")}
              </Text>
              <Text className="text-slate-900 dark:text-white font-black text-lg">
                {user?.name?.split(' ')[0] ?? "Sister"}
              </Text>
            </View>
          </View>

          <TouchableOpacity
            onPress={() => router.push("/notificationScreen")}
            className="w-11 h-11 bg-white/80 dark:bg-slate-800 rounded-2xl items-center justify-center shadow-sm border border-white/20"
          >
            <Ionicons name="notifications-outline" size={22} color={isDark ? "white" : "#1e293b"} />
            <View className="absolute top-3 right-3 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white dark:border-slate-800" />
          </TouchableOpacity>
        </View>

        <ScrollView 
          showsVerticalScrollIndicator={false}
          contentContainerStyle={{ paddingBottom: 120 }}
        >
          {/*FEATURED INSIGHT CARD*/}
          <Animated.View entering={FadeInDown.delay(200)} className="px-6 mt-2">
            <LinearGradient
              colors={['#7c3aed', '#6d28d9']}
              className="rounded-[32px] p-6 shadow-xl shadow-violet-200 dark:shadow-none"
            >
              <View className="flex-row justify-between items-center">
                <View className="flex-1">
                  <Text className="text-violet-100 text-xs font-bold uppercase tracking-wider">Daily Tip</Text>
                  <Text className="text-white text-lg font-bold mt-1 leading-6">
                    "Your strength is in your sisterhood. Reach out to a mentor today."
                  </Text>
                </View>
                <View className="bg-white/20 p-3 rounded-2xl ml-4">
                  <MaterialCommunityIcons name="comment-quote" size={28} color="white" />
                </View>
              </View>
              
              <TouchableOpacity 
                className="mt-6 bg-white/20 py-3 rounded-2xl items-center flex-row justify-center space-x-2"
                onPress={() => router.push("/mentorshipScreen")}
              >
                <Text className="text-white font-bold text-sm">Talk to a Mentor</Text>
                <Feather name="arrow-right" size={16} color="white" />
              </TouchableOpacity>
            </LinearGradient>
          </Animated.View>

          {/* --- STATUS ROW --- */}
          {/* <View className="flex-row px-6 mt-8 justify-between">
            <View className="bg-white dark:bg-slate-900 flex-1 mr-3 p-4 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm">
              <Text className="text-gray-400 text-[10px] font-bold uppercase">Cycle Day</Text>
              <Text className="text-slate-900 dark:text-white text-xl font-black mt-1">Day 14</Text>
              <Text className="text-emerald-500 text-[10px] font-bold mt-1">● Healthy</Text>
            </View>
            <View className="bg-white dark:bg-slate-900 flex-1 p-4 rounded-3xl border border-gray-100 dark:border-slate-800 shadow-sm">
              <Text className="text-gray-400 text-[10px] font-bold uppercase">Safety Status</Text>
              <Text className="text-slate-900 dark:text-white text-xl font-black mt-1">Secure</Text>
              <Text className="text-blue-500 text-[10px] font-bold mt-1">Hub Encrypted</Text>
            </View>
          </View> */}
          {/*  RECENT ACTIVITY / FORUM PREVIEW */}
          <Animated.View entering={FadeInRight.delay(400)} className="px-6 mt-8">
            <TouchableOpacity 
              className="bg-white dark:bg-slate-900 p-5 rounded-[32px] flex-row items-center justify-between border border-gray-100 dark:border-slate-800"
              onPress={() => router.push("/(protected)/community")}
            >
              <View className="flex-row items-center">
                <View className="w-12 h-12 bg-violet-100 dark:bg-violet-900/30 rounded-2xl items-center justify-center">
                  <Ionicons name="people" size={24} color="#7c3aed" />
                </View>
                <View className="ml-4">
                  <Text className="text-slate-900 dark:text-white font-bold text-base">Community Hub</Text>
                  <Text className="text-gray-500 text-xs">Space for everyone</Text>
                </View>
              </View>
              <Feather name="chevron-right" size={20} color="#94a3b8" />
            </TouchableOpacity>
          </Animated.View>

          {/* --- SERVICES GRID --- */}
          <Text className="px-8 mt-10 text-slate-900 dark:text-white font-black text-xl mb-4">
            Services
          </Text>
          <View className="flex-row flex-wrap px-4">
            <GridItem 
              title="Mentorship" 
              icon="hands-holding-child" 
              color="#8b5cf6" 
              family={FontAwesome6}
              onPress={() => router.push("/mentorshipScreen")} 
            />
            <GridItem 
              title="Emergency" 
              icon="contact-emergency" 
              color="#f43f5e" 
              family={MaterialIcons}
              onPress={() => router.push("/emergencyScreen")} 
            />
            <GridItem 
              title="Health" 
              icon="calendar-days" 
              color="#10b981" 
              family={FontAwesome6}
              onPress={() => router.push("/menstrualHealthScreen")} 
            />
            <GridItem 
              title="Reports" 
              icon="report-problem" 
              color="#f59e0b" 
              family={MaterialIcons}
              onPress={() => router.push("/reportHarrasmentScreen")} 
            />
          </View>

          

        </ScrollView>

        {/* --- FLOATING FAQ BOT --- */}
        <Animated.View
          entering={BounceIn.delay(800)}
          className="absolute bottom-10 right-8"
        >
          <TouchableOpacity 
            className="bg-white dark:bg-slate-800 w-16 h-16 rounded-full items-center justify-center shadow-2xl border border-gray-100 dark:border-slate-700"
            activeOpacity={0.9}
          >
            <MaterialCommunityIcons 
              name="robot-confused" 
              size={28} 
              color={isDark ? "#a78bfa" : "#7c3aed"} 
            />
            <Text className="text-gray-500 dark:text-gray-400 text-[10px] font-extrabold uppercase">
              {t("FAQ")}
            </Text>
          </TouchableOpacity>
        </Animated.View>
      </SafeAreaView>
    </View>
  );
}

// Reusable Professional Grid Component
const GridItem = ({ title, icon, color, family: IconFamily, onPress }: any) => (
  <TouchableOpacity 
    onPress={onPress}
    style={{ width: '50%', padding: 8 }}
    activeOpacity={0.7}
  >
    <View className=" p-6 rounded-[32px]  bg-white shadow-xl items-center">
      <View style={{ backgroundColor: `${color}15` }} className="p-4 rounded-2xl mb-3">
        <IconFamily name={icon} size={26} color={color} />
      </View>
      <Text className="text-slate-800 dark:text-slate-100 font-bold text-sm text-center">{title}</Text>
    </View>
  </TouchableOpacity>
);