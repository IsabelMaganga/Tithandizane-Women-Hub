import React, { useState } from "react";
import {
  View,
  Text,
  Pressable,
  KeyboardAvoidingView,
  Platform,
  ActivityIndicator,
  Alert,
  ScrollView,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { TextInput } from "react-native-paper";
import { Feather, MaterialCommunityIcons } from "@expo/vector-icons";
import { useRouter } from "expo-router";

export default function ForgotPassword() {
  const router = useRouter();

  const [email, setEmail] = useState("");
  const [loading, setLoading] = useState(false);
  const [isSent, setIsSent] = useState(false);

  const handleResetRequest = async () => {
    if (!email.trim() || !email.includes("@")) {
      Alert.alert("Invalid Email", "Please enter a valid email address to receive the reset link.");
      return;
    }

    try {
      setLoading(true);
      // 🔥 Replace with your actual Auth API call (e.g., Firebase sendPasswordResetEmail)
      await new Promise((resolve) => setTimeout(resolve, 2000));
      
      setIsSent(true);
    } catch (error) {
      Alert.alert("Error", "We couldn't find an account with that email.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView className="flex-1 bg-white">
      {/* Header */}
      <View className="px-4 py-2">
        <Pressable onPress={() => router.back()} className="p-2 -ml-2 active:opacity-60">
          <Feather name="chevron-left" size={28} color="#1F2937" />
        </Pressable>
      </View>

      <KeyboardAvoidingView
        behavior={Platform.OS === "ios" ? "padding" : "height"}
        className="flex-1"
      >
        <ScrollView contentContainerStyle={{ flexGrow: 1 }} className="px-8">
          
          {!isSent ? (
            /* --- STEP 1: REQUEST FORM --- */
            <View className="mt-4">
              <View className="w-16 h-16 bg-violet-100 rounded-2xl items-center justify-center mb-6">
                <MaterialCommunityIcons name="key-variant" size={32} color="#7C3AED" />
              </View>

              <Text className="text-3xl font-black text-gray-900">Forgot Password?</Text>
              <Text className="text-gray-500 mt-2 text-base leading-6">
                Don't worry! It happens. Please enter the email address associated with your account.
              </Text>

              <View className="mt-10 space-y-6">
                <TextInput
                  label="Email Address"
                  value={email}
                  onChangeText={setEmail}
                  mode="outlined"
                  keyboardType="email-address"
                  autoCapitalize="none"
                  outlineColor="#F3F4F6"
                  activeOutlineColor="#7C3AED"
                  className="bg-gray-50"
                  outlineStyle={{ borderRadius: 16 }}
                  left={<TextInput.Icon icon="email-outline" color="#9CA3AF" />}
                />

                <Pressable
                  onPress={handleResetRequest}
                  disabled={loading}
                  className={`h-14 rounded-2xl items-center justify-center shadow-sm mt-4 ${
                    loading ? "bg-violet-300" : "bg-violet-600 active:bg-violet-700"
                  }`}
                >
                  {loading ? (
                    <ActivityIndicator color="white" />
                  ) : (
                    <Text className="text-white font-bold text-lg">Send Reset Link</Text>
                  )}
                </Pressable>
              </View>
            </View>
          ) : (
            /* --- STEP 2: SUCCESS STATE --- */
            <View className="mt-10 items-center">
              <View className="w-20 h-20 bg-green-100 rounded-full items-center justify-center mb-6">
                <Feather name="check" size={40} color="#10B981" />
              </View>

              <Text className="text-2xl font-bold text-gray-900 text-center">Check Your Email</Text>
              <Text className="text-gray-500 text-center mt-3 text-base leading-6">
                We have sent a password reset link to:{"\n"}
                <Text className="font-bold text-gray-800">{email}</Text>
              </Text>

              <Pressable
                onPress={() => router.replace("/login")}
                className="w-full h-14 bg-gray-900 rounded-2xl items-center justify-center mt-10"
              >
                <Text className="text-white font-bold text-lg">Back to Login</Text>
              </Pressable>

              <Pressable 
                onPress={() => setIsSent(false)}
                className="mt-6 p-2"
              >
                <Text className="text-violet-600 font-semibold">Resend Link</Text>
              </Pressable>
            </View>
          )}

          {/* Help Footer */}
          <View className="flex-1 justify-end pb-10">
            <Text className="text-center text-gray-400 text-sm">
              Having trouble? <Text className="text-violet-600 font-bold">Contact Support</Text>
            </Text>
          </View>

        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}