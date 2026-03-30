import React from "react";
import {
  View,
  Text,
  ScrollView,
  Pressable,
  Linking,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Feather, MaterialCommunityIcons, Ionicons } from "@expo/vector-icons";
import { useRouter } from "expo-router";

export default function PrivacyPolicy() {
  const router = useRouter();

  // Helper component for Policy Sections
  const PolicySection = ({ title, content, icon }: { title: string; content: string; icon: any }) => (
    <View className="mb-8">
      <View className="flex-row items-center mb-2">
        <View className="w-8 h-8 bg-violet-100 rounded-lg items-center justify-center mr-3">
          {icon}
        </View>
        <Text className="text-lg font-bold text-gray-900">{title}</Text>
      </View>
      <Text className="text-gray-600 leading-6 text-sm ml-11">
        {content}
      </Text>
    </View>
  );

  return (
    <SafeAreaView className="flex-1 bg-white">
      {/* Header */}
      <View className="flex-row items-center justify-between px-4 py-3 border-b border-gray-50">
        <Pressable onPress={() => router.back()} className="p-2 -ml-2">
          <Feather name="chevron-left" size={28} color="#1F2937" />
        </Pressable>
        <Text className="text-lg font-bold text-gray-900">Privacy Policy</Text>
        <View className="w-10" /> 
      </View>

      <ScrollView showsVerticalScrollIndicator={false} className="flex-1 px-6">
        
        {/* Intro */}
        <View className="py-6 border-b border-gray-100 mb-6">
          <View className="bg-green-50 self-start px-3 py-1 rounded-full mb-3">
            <Text className="text-green-700 text-[10px] font-bold uppercase tracking-wider">
              Last Updated: March 2026
            </Text>
          </View>
          <Text className="text-2xl font-black text-gray-900">Your Privacy Matters</Text>
          <Text className="text-gray-500 mt-2 leading-5">
            At Tithandizane Women Hub, we are committed to protecting your personal information and your right to privacy.
          </Text>
        </View>

        {/* Highlight Box: Data Safety */}
        <View className="bg-violet-600 p-5 rounded-3xl mb-8 shadow-sm">
          <View className="flex-row items-center mb-2">
            <MaterialCommunityIcons name="shield-check" size={24} color="white" />
            <Text className="text-white font-bold ml-2 text-lg">Encrypted & Secure</Text>
          </View>
          <Text className="text-violet-100 text-sm leading-5">
            All your chats with mentors and harassment reports are end-to-end encrypted. We never sell your data to third parties.
          </Text>
        </View>

        {/* Detailed Sections */}
        <PolicySection 
          title="Information We Collect" 
          icon={<Ionicons name="person-add-outline" size={18} color="#7C3AED" />}
          content="We collect your name, email, and bio to personalize your experience. If you report harassment, we securely store the details provided to assist authorities if you choose to take legal action."
        />

        <PolicySection 
          title="How We Use Your Data" 
          icon={<Ionicons name="settings-outline" size={18} color="#7C3AED" />}
          content="Your data is used to provide mentorship matches, community moderation, and educational content tailored to your needs. Aggregated, anonymous data may be used to improve our services."
        />

        <PolicySection 
          title="Data Retention" 
          icon={<Ionicons name="time-outline" size={18} color="#7C3AED" />}
          content="We keep your information as long as your account is active. You can request to delete your account and all associated data at any time through the Settings menu."
        />

        <PolicySection 
          title="Safe Space Guarantee" 
          icon={<Ionicons name="heart-outline" size={18} color="#7C3AED" />}
          content="Mentors are bound by a confidentiality agreement. Any breach of privacy within the hub will result in immediate removal of the offending party."
        />

        {/* Contact Footer */}
        <View className="mt-4 pb-12 items-center bg-gray-50 rounded-2xl p-6 mb-10">
          <Text className="text-gray-900 font-bold mb-2">Questions about your privacy?</Text>
          <Text className="text-gray-500 text-center text-xs mb-4">
            If you have any questions or concerns about our policy, please contact our Data Protection Officer.
          </Text>
          <Pressable 
            onPress={() => Linking.openURL('mailto:privacy@tithandizane.org')}
            className="bg-white border border-gray-200 px-6 py-3 rounded-xl active:bg-gray-100"
          >
            <Text className="text-violet-600 font-bold">Contact Privacy Team</Text>
          </Pressable>
        </View>

      </ScrollView>
    </SafeAreaView>
  );
}