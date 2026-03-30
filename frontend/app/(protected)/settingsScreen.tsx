import React, { useState } from "react";
import {
  View,
  Text,
  Pressable,
  Modal,
  TouchableOpacity,
  FlatList,
  Image,
  ScrollView,
  Switch,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useAuth } from "../../context/AuthContext";
import {
  FontAwesome,
  MaterialIcons,
  MaterialCommunityIcons,
  AntDesign,
  Ionicons,
} from "@expo/vector-icons";
import i18n from "i18next";
import { useRouter } from "expo-router";
import { useTranslation } from "react-i18next";

// --- Types & Constants ---
interface Language {
  code: string;
  label: string;
}

const LANGUAGES: Language[] = [
  { code: "en", label: "English" },
  { code: "ch", label: "Chichewa" },
  { code: "tu", label: "Tumbuka" },
];

const Setting = () => {
  const { t } = useTranslation("settings");
  const router = useRouter();
  const { user, logout } = useAuth();

  // States
  const [modalVisible, setModalVisible] = useState(false);
  const [isDarkMode, setIsDarkMode] = useState(false);
  const [pushNotifications, setPushNotifications] = useState(true);
  const [selectedLanguage, setSelectedLanguage] = useState(i18n.language || "en");

  const changeLanguage = (lang: string) => {
    i18n.changeLanguage(lang);
    setSelectedLanguage(lang);
    setModalVisible(false);
  };

  const currentLanguageLabel =
    LANGUAGES.find((l) => l.code === selectedLanguage)?.label || "English";

  // Reusable Setting Item Component
  const SettingItem = ({ 
    icon, 
    label, 
    value, 
    onPress, 
    isLast = false, 
    iconBg = "bg-gray-100", 
    iconColor = "#4B5563" 
  }: any) => (
    <Pressable
      onPress={onPress}
      className={`flex-row items-center justify-between p-4 active:bg-gray-50 ${
        !isLast ? "border-b border-gray-100" : ""
      }`}
    >
      <View className="flex-row items-center space-x-3">
        <View className={`w-9 h-9 rounded-xl ${iconBg} items-center justify-center`}>
          {icon}
        </View>
        <Text className="text-base font-medium text-gray-800">{label}</Text>
      </View>
      <View className="flex-row items-center space-x-2">
        {value && <Text className="text-sm text-gray-400">{value}</Text>}
        <MaterialIcons name="arrow-forward-ios" size={14} color="#9CA3AF" />
      </View>
    </Pressable>
  );

  return (
    <SafeAreaView className="flex-1 bg-gray-50">
      <ScrollView showsVerticalScrollIndicator={false} className="px-4">

        {/* Header */}
        <Text className="text-3xl font-bold text-gray-900 mt-6 mb-4">Settings</Text>


        <Pressable
          onPress={() => router.push("/editProfile")}
          className="flex-row items-center justify-between bg-white p-4 rounded-2xl border border-gray-100 shadow-sm active:opacity-90"
        >
          <View className="flex-row items-center space-x-4">
            <View className="relative">
              {user?.profile_url ? (
                <Image source={{ uri: user.profile_url }} className="w-16 h-16 rounded-full" />
              ) : (
                <View className="w-16 h-16 rounded-full bg-violet-100 items-center justify-center">
                  <MaterialCommunityIcons name="face-woman-profile" size={35} color="#7C3AED" />
                </View>
              )}
              <View className="absolute bottom-0 right-0 w-5 h-5 bg-green-500 border-2 border-white rounded-full" />
            </View>
            <View>
              <Text className="text-lg font-bold text-gray-900">{user?.name || "User"}</Text>
              <Text className="text-xs text-gray-500">{user?.email || "Account details"}</Text>
            </View>
          </View>
          <View className="bg-gray-50 p-2 rounded-lg">
            <Text className="text-xs font-bold text-violet-600">EDIT</Text>
          </View>
        </Pressable>

        {/* General Preferences*/}
        <Text className="text-xs font-bold text-gray-400 uppercase tracking-widest mt-8 mb-3 ml-2">
          Preferences
        </Text>
        <View className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <SettingItem
            label="Language"
            value={currentLanguageLabel}
            icon={<FontAwesome name="language" size={18} color="#3B82F6" />}
            iconBg="bg-blue-50"
            onPress={() => setModalVisible(true)}
          />
          
          {/* Toggle Example: Notifications */}
          <View className="flex-row items-center justify-between p-4 border-b border-gray-100">
            <View className="flex-row items-center space-x-3">
              <View className="w-9 h-9 rounded-xl bg-orange-50 items-center justify-center">
                <Ionicons name="notifications" size={18} color="#F59E0B" />
              </View>
              <Text className="text-base font-medium text-gray-800">Notifications</Text>
            </View>
            <Switch
              value={pushNotifications}
              onValueChange={setPushNotifications}
              trackColor={{ false: "#D1D5DB", true: "#7C3AED" }}
            />
          </View>

          <SettingItem
            label="Dark Mode"
            value={isDarkMode ? "On" : "Off"}
            icon={<Ionicons name="moon" size={18} color="#6B7280" />}
            iconBg="bg-gray-100"
            onPress={() => setIsDarkMode(!isDarkMode)}
            isLast={true}
          />
        </View>

        {/* Security */}
        <Text className="text-xs font-bold text-gray-400 uppercase tracking-widest mt-8 mb-3 ml-2">
          Security
        </Text>
        <View className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <SettingItem
            label="Two-Factor Auth"
            value="Enabled"
            icon={<MaterialCommunityIcons name="shield-check" size={20} color="#10B981" />}
            iconBg="bg-green-50"
            onPress={() => {router.push("../twoFactorAuthScreen")}}
          />
          <SettingItem
            label="Change Password"
            icon={<Ionicons name="lock-closed" size={18} color="#6B7280" />}
            isLast={true}
            onPress={() => {router.push("../changePasswordScreen")}}
          />
        </View>

        {/* Support & Legal */}
        <Text className="text-xs font-bold text-gray-400 uppercase tracking-widest mt-8 mb-3 ml-2">
          Support
        </Text>
        <View className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <SettingItem
            label="Help Center"
            icon={<AntDesign name="customer-service" size={18} color="#6B7280" />}
            onPress={() => {}}
          />
          <SettingItem
            label="Privacy Policy"
            icon={<MaterialIcons name="privacy-tip" size={18} color="#6B7280" />}
            onPress={() => {router.push("../privacyPolicyScreen")}}
          />
          <SettingItem
            label="About"
            icon={<Ionicons name="information-circle" size={20} color="#6B7280" />}
            isLast={true}
            onPress={() => {router.push("../aboutScreen")}}
          />
        </View>

        {/*Logout & Delete*/}
        <Pressable
          onPress={logout}
          className="flex-row items-center justify-center bg-white border border-red-100 py-4 rounded-2xl mt-8 active:bg-red-50"
        >
          <AntDesign name="logout" size={18} color="#EF4444" />
          <Text className="text-red-500 font-bold ml-2">Sign Out</Text>
        </Pressable>

        <Pressable className="mt-4 mb-8">
          <Text className="text-center text-gray-400 text-xs font-medium">
            Delete Account
          </Text>
        </Pressable>

        {/*Footer*/}
        <View className="items-center pb-10">
          <Image
            source={require("../../assets/images/Ellipse 3.png")}
            className="w-12 h-12 rounded-full opacity-50 grayscale"
          />
          <Text className="text-gray-400 text-[10px] mt-2 tracking-widest uppercase">
            Version 1.0.4 (Build 122)
          </Text>
        </View>
      </ScrollView>

      {/*Language Modal*/}
      <Modal animationType="fade" transparent visible={modalVisible}>
        <View className="flex-1 bg-black/50 justify-center items-center px-6">
          <View className="w-full bg-white rounded-3xl p-6">
            <Text className="text-xl font-bold text-gray-900 mb-4">Select Language</Text>
            <FlatList
              data={LANGUAGES}
              keyExtractor={(item) => item.code}
              renderItem={({ item }) => (
                <TouchableOpacity
                  onPress={() => changeLanguage(item.code)}
                  className={`flex-row justify-between items-center py-4 border-b border-gray-50 ${
                    item.code === selectedLanguage ? "bg-violet-50 rounded-xl px-4" : "px-4"
                  }`}
                >
                  <Text className={`text-base ${item.code === selectedLanguage ? "text-violet-700 font-bold" : "text-gray-700"}`}>
                    {item.label}
                  </Text>
                  {item.code === selectedLanguage && <MaterialIcons name="check" size={20} color="#7C3AED" />}
                </TouchableOpacity>
              )}
            />
            <TouchableOpacity
              onPress={() => setModalVisible(false)}
              className="mt-6 py-4 bg-gray-100 items-center rounded-xl"
            >
              <Text className="font-bold text-gray-700">Close</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
};

export default Setting;