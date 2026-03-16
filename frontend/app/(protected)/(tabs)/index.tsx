import React from 'react';
import { StatusBar } from 'expo-status-bar';
import { View, ImageBackground, Image, Text, Pressable, TouchableOpacity, ScrollView } from 'react-native';
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from 'expo-router';
import { useAuth } from '../../../context/AuthContext';
import Profile from '../../../components/Profile';
import { useTranslation } from "react-i18next";
import { useThemeToggle } from "../../../hooks/useTheme";
import { MaterialCommunityIcons, MaterialIcons, FontAwesome6, Ionicons } from '@expo/vector-icons';
import { MenuItem  }  from '../../../components/MenuItem';
import Animated, { BounceIn } from "react-native-reanimated";

export default function App() {
  const router = useRouter();
  const { user } = useAuth();
  const { colorScheme, toggleTheme } = useThemeToggle();
  const { t } = useTranslation();

  const isDark = colorScheme === "dark";


  

  return (
    <View className="flex-1 bg-gray-50 dark:bg-slate-900">
      <StatusBar style={isDark ? "light" : "dark"} />
      
      <ImageBackground
        source={require('../../../assets/images/Ellipse 4.png')}
        className="absolute top-0 w-full h-[305px]"
        resizeMode="cover"
      >
        <Image
          source={require('../../../assets/images/shape (1).png')}
          className="absolute top-0 left-0 w-32 h-32 opacity-60"
        />
      </ImageBackground>

      <SafeAreaView className="flex-1">
        {/* Header Section */}
        <View className="flex-row items-center justify-between px-6 mt-4">
          <View className="flex-row items-center space-x-3">
            <Pressable onPress={() => router.push("../profileScreen")}>
              <View className="border-2 border-white rounded-full">
                <Profile />
              </View>
            </Pressable>
            <View>
              <Text className="text-gray-500 dark:text-gray-400 text-xs font-medium">
                {t("welcome")}
              </Text>
              <Text className="text-slate-800 dark:text-white font-bold text-base">
                {user?.name ?? "Guest"}
              </Text>
            </View>
          </View>

          <View className="flex-row items-center space-x-4">
            <Pressable 
              onPress={toggleTheme}
              className="p-2 bg-white/20 rounded-full"
            >
              <MaterialCommunityIcons
                name={isDark ? "weather-night" : "white-balance-sunny"}
                size={22}
                color={isDark ? "#fbbf24" : "white"}
              />
            </Pressable>

            <Pressable className="p-2 bg-white/20 rounded-full">
              <Ionicons name="notifications" size={22} color="white" />
            </Pressable>
          </View>
        </View>

        {/* Dashboard Grid */}
        <ScrollView 
          contentContainerStyle={{ paddingHorizontal: 20, paddingTop: 40 }}
          showsVerticalScrollIndicator={false}
        >
          <View className="flex-row flex-wrap justify-between">
            <MenuItem 
              title="Mentorship" 
              icon="hands-holding-child" 
              bgColor="bg-violet-500" 
              family={FontAwesome6}
              onPress={() => router.push("./mentorshipScreen")}
            />
            
            <MenuItem 
              title="Emergency Help" 
              icon="contact-emergency" 
              bgColor="bg-orange-500" 
              family={MaterialIcons}
              onPress={() => router.push("./emergencyScreen")}
            />

            <MenuItem 
              title="Menstrual Health" 
              icon="calendar-days" 
              bgColor="bg-emerald-500" 
              family={FontAwesome6}
              onPress={() => router.push("./menstrualHealthScreen")}
            />

            <MenuItem 
              title="Report Harassment" 
              icon="report-problem" 
              bgColor="bg-rose-500" 
              family={MaterialIcons}
              onPress={() => router.push("../reportHarrasmentScreen")}
            />
          </View>
        </ScrollView>

        {/* FAQ button */}
        <Animated.View
          entering={BounceIn.delay(300)}
          className="absolute bottom-8 right-8"
        >
          <TouchableOpacity 
            onPress={() => {}}
            className="bg-white dark:bg-slate-800 w-16 h-16 rounded-full items-center justify-center shadow-lg border border-gray-100 dark:border-slate-700"
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