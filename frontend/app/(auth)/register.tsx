// app/(auth)/register.tsx

import { View, Text, TextInput, Image, KeyboardAvoidingView, Platform, TouchableOpacity, Alert } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import { useState } from "react";
import { useAuth } from '../../context/AuthContext';
import FontAwesome5 from "@expo/vector-icons/FontAwesome5";
import Entypo from "@expo/vector-icons/Entypo";
import AntDesign from "@expo/vector-icons/AntDesign";
import MaterialIcons from "@expo/vector-icons/MaterialIcons";
import FontAwesome from "@expo/vector-icons/FontAwesome";

interface ValidationErrors {
  name?: string[];
  email?: string[];
  password?: string[];
  phone?: string[];
}

export default function Register() {
  const router = useRouter();
  const { register } = useAuth();

  // Form state
  const [name, setName] = useState<string>("");
  const [email, setEmail] = useState<string>("");
  const [phone, setPhone] = useState<string>("");
  const [password, setPassword] = useState<string>("");
  const [confirmPassword, setConfirmPassword] = useState<string>("");

  // UI state
  const [error, setError] = useState<string>("");
  const [loading, setLoading] = useState<boolean>(false);
  const [showPassword, setShowPassword] = useState<boolean>(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState<boolean>(false);

  const handleSignUp = async () => {
    // Clear previous errors
    setError("");

    // Validation
    if (!name.trim()) {
      setError("Name is required!");
      return;
    }
    
    if (!email.trim()) {
      setError("Email is required!");
      return;
    }
    
    // Email format validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      setError("Please enter a valid email address!");
      return;
    }
    
    if (!password) {
      setError("Password is required!");
      return;
    }
    
    if (password.length < 8) {
      setError("Password must be at least 8 characters!");
      return;
    }
    
    if (password !== confirmPassword) {
      setError("Passwords do not match!");
      return;
    }

    setLoading(true);

    try {
      await register(name, email, phone, password, confirmPassword);
      router.replace("/(auth)/login");
    } catch (err: any) {
      // Handle Laravel validation errors
      if (err.response?.data?.errors) {
        const errors: ValidationErrors = err.response.data.errors;
        const firstError = Object.values(errors)[0]?.[0];
        setError(firstError || "Validation failed");
      } else if (err.response?.data?.message) {
        setError(err.response.data.message);
      } else {
        setError(err.message || "Registration failed. Please try again.");
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <>
      <Image
        source={require('../../assets/images/shape.png')}
        style={{ width: 136, height: 141 }}
        className="absolute top-0 left-0"
      />

      <KeyboardAvoidingView
        behavior={Platform.OS === "ios" ? "padding" : "height"}
        className="flex-1 bg-gray-100 items-center justify-center"
      >
        <SafeAreaView className="flex-1 justify-center">
          <View className="bg-white w-[350px] p-5 rounded-lg shadow-lg">
            <Image
              source={require("../../assets/images/Ellipse 3.png")}
              className="absolute self-center top-[-50px] w-[100px] h-[100px]"
            />

            <Text className="text-2xl font-bold mt-[60px] text-center">
              Create Account
            </Text>

            <Text className="text-gray-500 mb-5 text-center">
              Please sign up to continue
            </Text>

            {/* Error Message */}
            {error ? (
              <View className="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                <Text className="text-red-600 text-sm text-center">{error}</Text>
              </View>
            ) : null}

            {/* Name Input */}
            <View className="flex-row items-center bg-gray-100 rounded-xl p-3 mb-3">
              <FontAwesome5 name="user" size={20} color="#6B7280" />
              <TextInput
                placeholder="Full Name"
                className="flex-1 text-gray-900 ml-3"
                value={name}
                onChangeText={setName}
                autoCapitalize="words"
                editable={!loading}
              />
            </View>

            {/* Email Input */}
            <View className="flex-row items-center bg-gray-100 rounded-xl p-3 mb-3">
              <MaterialIcons name="email" size={24} color="#6B7280" />
              <TextInput
                placeholder="Email Address"
                className="flex-1 text-gray-900 ml-3"
                value={email}
                onChangeText={setEmail}
                autoCapitalize="none"
                keyboardType="email-address"
                editable={!loading}
              />
            </View>

            {/* Phone Input (Optional) */}
            <View className="flex-row items-center bg-gray-100 rounded-xl p-3 mb-3">
              <FontAwesome name="phone-square" size={24} color="#6B7280" />
              <TextInput
                placeholder="Phone Number (Optional)"
                className="flex-1 text-gray-900 ml-3"
                value={phone}
                onChangeText={setPhone}
                keyboardType="phone-pad"
                editable={!loading}
              />
            </View>

            {/* Password Input */}
            <View className="flex-row items-center bg-gray-100 rounded-xl p-3 mb-3">
              <Entypo name="lock" size={20} color="#6B7280" />
              <TextInput
                placeholder="Password"
                secureTextEntry={!showPassword}
                className="flex-1 text-gray-900 ml-3"
                value={password}
                onChangeText={setPassword}
                editable={!loading}
              />
              <TouchableOpacity 
                onPress={() => setShowPassword(!showPassword)}
                disabled={loading}
              >
                <FontAwesome5
                  name={showPassword ? "eye" : "eye-slash"}
                  size={24}
                  color="#6B7280"
                />
              </TouchableOpacity>
            </View>

            {/* Confirm Password Input */}
            <View className="flex-row items-center bg-gray-100 rounded-xl p-3 mb-5">
              <Entypo name="lock" size={20} color="#6B7280" />
              <TextInput
                placeholder="Confirm Password"
                secureTextEntry={!showConfirmPassword}
                className="flex-1 text-gray-900 ml-3"
                value={confirmPassword}
                onChangeText={setConfirmPassword}
                editable={!loading}
              />
              <TouchableOpacity 
                onPress={() => setShowConfirmPassword(!showConfirmPassword)}
                disabled={loading}
              >
                <FontAwesome5
                  name={showConfirmPassword ? "eye" : "eye-slash"}
                  size={24}
                  color="#6B7280"
                />
              </TouchableOpacity>
            </View>

            {/* Sign Up Button */}
            <TouchableOpacity
              className={`bg-purple-600 rounded-xl py-3 ${loading ? 'opacity-50' : ''}`}
              onPress={handleSignUp}
              disabled={loading}
            >
              <Text className="text-white text-center font-semibold text-base">
                {loading ? "Creating Account..." : "Sign Up"}
              </Text>
            </TouchableOpacity>

            {/* Login Link */}
            <View className="mt-4 flex-row justify-center">
              <Text className="text-gray-600">
                Already have an account?{" "}
              </Text>
              <TouchableOpacity onPress={() => router.push("/(auth)/login")}>
                <Text className="text-purple-600 font-semibold">
                  Login
                </Text>
              </TouchableOpacity>
            </View>
          </View>
        </SafeAreaView>
      </KeyboardAvoidingView>
    </>
  );
}