import {
  View,
  Text,
  TextInput,
  Image,
  KeyboardAvoidingView,
  Platform,
  TouchableOpacity,
  Pressable
} from "react-native";

import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import MyButton from "../../components/MyButton";
import { Card } from "react-native-paper";
import { useTranslation } from "react-i18next";
import FontAwesome5 from "@expo/vector-icons/FontAwesome5";
import Entypo from "@expo/vector-icons/Entypo";
import AntDesign from "@expo/vector-icons/AntDesign";
import { useState, useRef } from "react";
import { useAuth } from "../../context/AuthContext";

export default function Login() {

  const router = useRouter();
  const { t } = useTranslation("auth");
  const { login } = useAuth();

  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  // Prevent double press / double login call
  const isLoggingIn = useRef(false);

  const safeText = (value: any) =>
    typeof value === "string" ? value : "";

  const handleLogIn = async () => {
    // Block if already in progress
    if (isLoggingIn.current) return;
    isLoggingIn.current = true;

    if (!email || !password) {
      setError("Please fill in all fields");
      isLoggingIn.current = false;
      return;
    }

    setLoading(true);
    setError("");

    try {
      await login(email, password);
      router.replace("/(protected)/(tabs)");

    } catch (err: any) {
      

      if (err?.message === "Admin accounts cannot log in here") {
        
        setError("Admin accounts cannot log in here");

      } else if (err?.response?.data?.message) {
        
        setError(err.response.data.message);

      } else if (err?.response?.data?.errors) {
        
        const errors = err.response.data.errors;
        const firstKey = Object.keys(errors)[0];
        setError(errors[firstKey][0]);

      } else if (
        err?.message === "Network Error" ||
        err?.code === "ERR_NETWORK" ||
        err?.message?.includes("Unable to connect")
      ) {
        setError("You are not connected to the internet");

      } else {
       
        setError(err?.message || "Login failed. Please try again.");
      }

    } finally {
      setLoading(false);
      isLoggingIn.current = false;
    }
  };

  return (
    <KeyboardAvoidingView
      behavior={Platform.OS === "ios" ? "padding" : "height"}
      className="flex-1 bg-gray-200 justify-center"
    >
      <Image
        source={require('../../assets/images/shape.png')}
        style={{ width: 136, height: 141 }}
      />

      <SafeAreaView className="items-center w-full flex-1 justify-center">
        <Card
          style={{
            backgroundColor: "#fff",
            width: 350,
            padding: 20,
            elevation: 4,
            overflow: "visible",
            borderRadius: 12,
            marginTop: -30,
          }}
        >
          {/* Avatar */}
          <Image
            source={require("../../assets/images/Ellipse 3.png")}
            style={{
              position: "absolute",
              alignSelf: "center",
              top: -50,
              width: 100,
              height: 100,
            }}
          />

          {/* Title */}
          <Text className="text-2xl font-bold mt-12">
            {safeText(t("login"))}
          </Text>
          <Text className="text-gray-600 mb-5">
            {safeText(t("please_enter_account_details"))}
          </Text>

          {/* Error */}
          {error ? (
            <Text className="text-red-500 mb-2">{error}</Text>
          ) : null}

          {/* Email */}
          <View className="flex-row items-center bg-gray-200 rounded-xl p-2 mb-3">
            <FontAwesome5
              name="user"
              size={20}
              color="black"
              style={{ marginRight: 10 }}
            />
            <TextInput
              placeholder={safeText(t("username"))}
              className="flex-1 text-black"
              value={email}
              onChangeText={setEmail}
              autoCapitalize="none"
              keyboardType="email-address"
            />
          </View>

          {/* Password */}
          <View className="flex-row items-center bg-gray-200 rounded-xl p-2 mb-5">
            <Entypo
              name="lock"
              size={20}
              color="black"
              style={{ marginRight: 10 }}
            />
            <TextInput
              placeholder={safeText(t("password"))}
              secureTextEntry={!showPassword}
              className="flex-1 text-black"
              value={password}
              onChangeText={setPassword}
            />
            <TouchableOpacity
              onPress={() => setShowPassword(!showPassword)}
              style={{ position: "absolute", right: 10 }}
            >
              <AntDesign
                name={showPassword ? "eye-invisible" : "eye"}
                size={24}
                color="black"
              />
            </TouchableOpacity>
          </View>

          {/* Forgot */}
          <Pressable onPress={() => router.push("/(auth)/forgotPasswordScreen")}>
            <Text className="text-purple-400 text-sm mb-2 text-right">
              {safeText(t("forgot_password"))}
            </Text>
          </Pressable>

          {/* Login Button */}
          <MyButton
            title={loading ? "Logging in..." : safeText(t("login"))}
            style={{ width: "100%", alignSelf: "center" }}
            onPress={handleLogIn}
            disabled={loading}
          />

          {/* Signup */}
          <Text className="mt-4 text-center text-gray-700">
            {safeText(t("don_t_have_an_account"))}{" "}
            <Text
              className="text-purple-600"
              onPress={() => router.push("/(auth)/register")}
            >
              {safeText(t("signup"))}
            </Text>
          </Text>

        </Card>
      </SafeAreaView>
    </KeyboardAvoidingView>
  );
}