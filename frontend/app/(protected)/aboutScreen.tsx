import React from "react";
import {
  View,
  Text,
  Image,
  ScrollView,
  Pressable,
  Linking,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Feather, MaterialCommunityIcons, FontAwesome5 } from "@expo/vector-icons";
import { useRouter } from "expo-router";

export default function AboutScreen() {
  const router = useRouter();

  const openLink = (url: string) => {
    Linking.openURL(url).catch(() => {
      // Handle error if link cannot be opened
    });
  };

  return (
    <SafeAreaView className="flex-1 bg-white">
      {/* Header */}
      <View className="flex-row items-center px-4 py-3 border-b border-gray-50">
        <Pressable onPress={() => router.back()} className="p-2 -ml-2 active:opacity-60">
          <Feather name="arrow-left" size={24} color="#1F2937" />
        </Pressable>
        <Text className="text-lg font-bold text-gray-900 ml-2">About Us</Text>
      </View>

      <ScrollView showsVerticalScrollIndicator={false} className="flex-1">
        
        {/* Hero Section */}
        <View className="items-center py-10 bg-violet-50/50 px-6">
          <View className="w-24 h-24 bg-white rounded-3xl shadow-xl items-center justify-center mb-4">
            <Image 
              source={require("../../assets/images/Ellipse 3.png")}
              className="w-16 h-16"
              resizeMode="contain"
            />
          </View>
          <Text className="text-2xl font-black text-violet-700 text-center">
            Tithandizane Women Hub
          </Text>
          <Text className="text-gray-500 text-sm mt-1 tracking-widest uppercase font-semibold">
            Empowering Every Woman
          </Text>
        </View>

        {/* Our Mission */}
        <View className="px-6 py-8">
          <Text className="text-xl font-bold text-gray-900 mb-3">Our Mission</Text>
          <Text className="text-gray-600 leading-6 text-base italic">
            "Tithandizane" means "Let us help each other." We are a dedicated safe space designed to uplift women through mentorship, peer support, and essential education on hygiene and health.
          </Text>
        </View>

        {/* Key Features / Pillars */}
        <View className="px-6 space-y-6">
          <View className="flex-row items-start space-x-4">
            <View className="w-12 h-12 bg-pink-100 rounded-2xl items-center justify-center">
              <MaterialCommunityIcons className="justify-center" name="account-group" size={24} color="#DB2777" />
            </View>
            <View className="flex-1 ml-3">
              <Text className="text-lg font-bold text-gray-900">Safe Community</Text>
              <Text className="text-gray-500 text-sm leading-5">
                Engage in meaningful conversations with other women and mentors in a moderated, supportive environment.
              </Text>
            </View>
          </View>

          <View className="flex-row items-start space-x-4">
            <View className="w-12 h-12 bg-blue-100 rounded-2xl items-center justify-center">
              <FontAwesome5 name="book-reader" className="justify-center" size={20} color="#2563EB" />
            </View>
            <View className="flex-1 ml-2">
              <Text className="text-lg font-bold text-gray-900">Knowledge Hub</Text>
              <Text className="text-gray-500 text-sm leading-5">
                Access verified information about hygiene, schooling, and women's rights to help you navigate life's challenges.
              </Text>
            </View>
          </View>

          <View className="flex-row items-start space-x-4">
            <View className="w-12 h-12 bg-red-100 rounded-2xl items-center justify-center">
              <MaterialCommunityIcons name="shield-alert" className="justify-center" size={24} color="#DC2626" />
            </View>
            <View className="flex-1 ml-2">
              <Text className="text-lg font-bold text-gray-900">Safety & Reporting</Text>
              <Text className="text-gray-500 text-sm leading-5">
                Quickly report harassment and find emergency contact numbers for immediate help and legal counseling.
              </Text>
            </View>
          </View>
        </View>

        {/* Social & Contact */}
        <View className="mt-12 px-6 py-8 bg-gray-50 rounded-t-[40px]">
          <Text className="text-center font-bold text-gray-900 mb-6 text-lg">Connect With Us</Text>
          
          <View className="flex-row justify-center space-x-8 mb-8">
            <Pressable onPress={() => openLink("https://facebook.com/tithandizane")}>
              <View className="w-12 h-12 bg-white rounded-full items-center justify-center shadow-sm">
                <Feather name="facebook" size={22} color="#1877F2" />
              </View>
            </Pressable>
            <Pressable onPress={() => openLink("https://instagram.com/tithandizane")}>
              <View className="w-12 h-12 bg-white rounded-full items-center justify-center shadow-sm">
                <Feather name="instagram" size={22} color="#E4405F" />
              </View>
            </Pressable>
            <Pressable onPress={() => openLink("mailto:support@tithandizane.com")}>
              <View className="w-12 h-12 bg-white rounded-full items-center justify-center shadow-sm">
                <Feather name="mail" size={22} color="#7C3AED" />
              </View>
            </Pressable>
          </View>

          <Text className="text-center text-gray-400 text-xs font-medium">
            Version 1.0.0
          </Text>
          <Text className="text-center text-gray-400 text-xs mt-1">
            © 2026 Tithandizane Women Hub. All Rights Reserved.
          </Text>
        </View>

      </ScrollView>
    </SafeAreaView>
  );
}