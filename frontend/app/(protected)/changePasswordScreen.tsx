import React, { useState } from "react";
import {
  View,
  Text,
  Pressable,
  ScrollView,
  KeyboardAvoidingView,
  Platform,
  ActivityIndicator,
  Alert,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { TextInput } from "react-native-paper";
import { Feather, MaterialCommunityIcons } from "@expo/vector-icons";
import { useRouter } from "expo-router";
import { changePassword } from "../../services/api";

export default function ChangePassword() {
  const router = useRouter();

  // States
  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  
  const [showCurrent, setShowCurrent] = useState(false);
  const [showNew, setShowNew] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);
  const [loading, setLoading] = useState(false);

  // Password Validation Logic
  const hasMinLength = newPassword.length >= 8;
  const hasNumber = /\d/.test(newPassword);
  const hasSpecialChar = /[!@#$%^&*]/.test(newPassword);
  const passwordsMatch = newPassword === confirmPassword && newPassword !== "";

  const handleUpdate = async () => {
    if (!hasMinLength || !hasNumber || !hasSpecialChar) {
      Alert.alert("Security Error", "Please meet all password requirements.");
      return;
    }

    if (!passwordsMatch) {
      Alert.alert("Mismatch", "New passwords do not match.");
      return;
    }

    try {
      setLoading(true);
      await changePassword(currentPassword, newPassword, confirmPassword);

      setCurrentPassword("");
      setNewPassword("");
      setConfirmPassword("");

      Alert.alert("Success", "Your password has been updated.", [
        { text: "Done", onPress: () => router.back() }
      ]);
    } catch (error: any) {
      const message = error?.response?.data?.message ||
                      error?.response?.data?.errors?.current_password?.[0] ||
                      error?.message ||
                      "Could not update password. Try again.";
      Alert.alert("Error", message.toString());
    } finally {
      setLoading(false);
    }
  };

  // Helper component for validation checks
  const ValidationItem = ({ label, met }: { label: string; met: boolean }) => (
    <View className="flex-row items-center mb-1">
      <MaterialCommunityIcons
        name={met ? "check-circle" : "circle-outline"}
        size={14}
        color={met ? "#10B981" : "#9CA3AF"}
      />
      <Text className={`ml-2 text-xs ${met ? "text-green-600" : "text-gray-400"}`}>
        {label}
      </Text>
    </View>
  );

  return (
    <SafeAreaView className="flex-1 bg-white">
      {/* Header */}
      <View className="flex-row items-center px-4 py-2 border-b border-gray-50">
        <Pressable onPress={() => router.back()} className="p-2 -ml-2">
          <Feather name="chevron-left" size={28} color="#1F2937" />
        </Pressable>
        <Text className="text-lg font-bold text-gray-900 ml-2">Security</Text>
      </View>

      <KeyboardAvoidingView
        behavior={Platform.OS === "ios" ? "padding" : "height"}
        className="flex-1"
      >
        <ScrollView className="px-6" showsVerticalScrollIndicator={false}>
          <View className="mt-8 mb-6">
            <Text className="text-2xl font-bold text-gray-900">Change Password</Text>
            <Text className="text-gray-500 mt-2">
              Create a strong password to protect your account.
            </Text>
          </View>

          <View className="space-y-4">
            {/* Current Password */}
            <TextInput
              label="Current Password"
              value={currentPassword}
              onChangeText={setCurrentPassword}
              mode="outlined"
              secureTextEntry={!showCurrent}
              outlineColor="#F3F4F6"
              activeOutlineColor="#7C3AED"
              className="bg-gray-50"
              outlineStyle={{ borderRadius: 12 }}
              right={
                <TextInput.Icon
                  icon={showCurrent ? "eye-off" : "eye"}
                  onPress={() => setShowCurrent(!showCurrent)}
                  color="#9CA3AF"
                />
              }
            />

            <View className="h-4" />

            {/* New Password */}
            <TextInput
              label="New Password"
              value={newPassword}
              onChangeText={setNewPassword}
              mode="outlined"
              secureTextEntry={!showNew}
              outlineColor="#F3F4F6"
              activeOutlineColor="#7C3AED"
              className="bg-gray-50"
              outlineStyle={{ borderRadius: 12 }}
              right={
                <TextInput.Icon
                  icon={showNew ? "eye-off" : "eye"}
                  onPress={() => setShowNew(!showNew)}
                  color="#9CA3AF"
                />
              }
            />

            {/* Validation Feedback */}
            <View className="px-1 py-2">
              <ValidationItem label="At least 8 characters" met={hasMinLength} />
              <ValidationItem label="Contains a number" met={hasNumber} />
              <ValidationItem label="Contains a special character (!@#$)" met={hasSpecialChar} />
            </View>

            {/* Confirm Password */}
            <TextInput
              label="Confirm New Password"
              value={confirmPassword}
              onChangeText={setConfirmPassword}
              mode="outlined"
              secureTextEntry={!showConfirm}
              outlineColor="#F3F4F6"
              activeOutlineColor="#7C3AED"
              className="bg-gray-50"
              outlineStyle={{ borderRadius: 12 }}
              error={confirmPassword.length > 0 && !passwordsMatch}
              right={
                <TextInput.Icon
                  icon={showConfirm ? "eye-off" : "eye"}
                  onPress={() => setShowConfirm(!showConfirm)}
                  color="#9CA3AF"
                />
              }
            />
          </View>

          {/* Action Button */}
          <Pressable
            onPress={handleUpdate}
            disabled={loading || !passwordsMatch}
            className={`h-14 rounded-2xl items-center justify-center mt-10 shadow-sm ${
              loading || !passwordsMatch 
                ? "bg-violet-200" 
                : "bg-violet-600 active:bg-violet-700"
            }`}
          >
            {loading ? (
              <ActivityIndicator color="white" />
            ) : (
              <Text className="text-white font-bold text-lg">Update Password</Text>
            )}
          </Pressable>

          <Pressable 
            onPress={() => Alert.alert("Reset Password", "We will send a link to your email.")}
            className="mt-6 self-center"
          >
            <Text className="text-violet-600 font-semibold">Forgot your password?</Text>
          </Pressable>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}