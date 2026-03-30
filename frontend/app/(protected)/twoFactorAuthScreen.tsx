import React, { useState, useRef, useEffect } from "react";
import {
  View,
  Text,
  Pressable,
  TextInput,
  KeyboardAvoidingView,
  Platform,
  ActivityIndicator,
  Alert,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import { Feather, MaterialCommunityIcons } from "@expo/vector-icons";

const TWO_FA_CODE_LENGTH = 6;

export default function TwoFactorAuth() {
  const router = useRouter();
  
  // States
  const [code, setCode] = useState("");
  const [loading, setLoading] = useState(false);
  const [timer, setTimer] = useState(59);
  
  // Ref for the hidden text input to maintain focus
  const inputRef = useRef<TextInput>(null);

  // Countdown timer logic
  useEffect(() => {
    const interval = setInterval(() => {
      setTimer((prev) => (prev > 0 ? prev - 1 : 0));
    }, 1000);
    return () => clearInterval(interval);
  }, []);

  const handleVerify = async () => {
    if (code.length !== TWO_FA_CODE_LENGTH) {
      Alert.alert("Invalid Code", "Please enter the full 6-digit code.");
      return;
    }

    try {
      setLoading(true);
      // 🔥 Replace with your actual 2FA verification API call
      await new Promise((resolve) => setTimeout(resolve, 2000));
      
      Alert.alert("Success", "Authentication successful!", [
        { text: "Continue", onPress: () => router.replace("/home") }
      ]);
    } catch (error) {
      Alert.alert("Error", "Verification failed. Please check the code.");
    } finally {
      setLoading(false);
    }
  };

  const resendCode = () => {
    if (timer === 0) {
      setTimer(59);
      // 🔥 Trigger your resend API here
      console.log("Resending code...");
    }
  };

  // Helper to render individual OTP boxes
  const renderOTPBoxes = () => {
    const boxes = [];
    for (let i = 0; i < TWO_FA_CODE_LENGTH; i++) {
      const char = code[i] || "";
      const isFocused = code.length === i;

      boxes.push(
        <View
          key={i}
          className={`w-12 h-14 border-2 rounded-xl items-center justify-center bg-white shadow-sm ${
            isFocused ? "border-violet-600 ring-2 ring-violet-200" : "border-gray-200"
          }`}
        >
          <Text className="text-2xl font-bold text-gray-900">{char}</Text>
        </View>
      );
    }
    return boxes;
  };

  return (
    <SafeAreaView className="flex-1 bg-white">
      {/* Header */}
      <View className="px-4 py-2">
        <Pressable onPress={() => router.back()} className="p-2 -ml-2">
          <Feather name="arrow-left" size={24} color="#1F2937" />
        </Pressable>
      </View>

      <KeyboardAvoidingView
        behavior={Platform.OS === "ios" ? "padding" : "height"}
        className="flex-1 px-6 pt-4"
      >
        {/* Illustration/Icon */}
        <View className="items-center mb-8">
          <View className="w-20 h-20 bg-violet-100 rounded-full items-center justify-center mb-6">
            <MaterialCommunityIcons name="shield-lock-outline" size={40} color="#7C3AED" />
          </View>
          <Text className="text-2xl font-bold text-gray-900 text-center">
            Verification Code
          </Text>
          <Text className="text-gray-500 text-center mt-2 leading-5">
            We have sent a 6-digit verification code to your email{"\n"}
            <Text className="font-bold text-gray-800">jo***@example.com</Text>
          </Text>
        </View>

        {/* Hidden Input for handling keyboard */}
        <TextInput
          ref={inputRef}
          value={code}
          onChangeText={(text) => setCode(text.replace(/[^0-9]/g, ""))}
          maxLength={TWO_FA_CODE_LENGTH}
          keyboardType="number-pad"
          className="absolute opacity-0 w-1 h-1"
          autoFocus={true}
        />

        {/* OTP Input UI */}
        <Pressable 
          onPress={() => inputRef.current?.focus()}
          className="flex-row justify-between mb-10"
        >
          {renderOTPBoxes()}
        </Pressable>

        {/* Resend Section */}
        <View className="flex-row justify-center items-center mb-10">
          <Text className="text-gray-500 mr-1">Didn't receive the code?</Text>
          <Pressable onPress={resendCode} disabled={timer > 0}>
            <Text className={`font-bold ${timer > 0 ? "text-gray-300" : "text-violet-600"}`}>
              Resend {timer > 0 ? `(${timer}s)` : ""}
            </Text>
          </Pressable>
        </View>

        {/* Action Button */}
        <Pressable
          onPress={handleVerify}
          disabled={loading || code.length !== TWO_FA_CODE_LENGTH}
          className={`h-14 rounded-2xl items-center justify-center shadow-sm ${
            loading || code.length !== TWO_FA_CODE_LENGTH 
              ? "bg-violet-200" 
              : "bg-violet-600 active:bg-violet-700"
          }`}
        >
          {loading ? (
            <ActivityIndicator color="white" />
          ) : (
            <Text className="text-white font-bold text-lg">Verify & Continue</Text>
          )}
        </Pressable>

        {/* Support Link */}
        <Pressable className="mt-6 self-center">
          <Text className="text-gray-400 text-sm font-medium underline">
            Try another way
          </Text>
        </Pressable>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}