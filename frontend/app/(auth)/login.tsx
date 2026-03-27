import { View, Text, TextInput, Image, KeyboardAvoidingView, Platform, TouchableOpacity } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import MyButton from "../../components/MyButton";
import { Card } from "react-native-paper";
import { useTranslation } from "react-i18next";
import FontAwesome5 from "@expo/vector-icons/FontAwesome5";
import Entypo from "@expo/vector-icons/Entypo";
import AntDesign from "@expo/vector-icons/AntDesign";
import { useState } from "react";
import { useAuth } from "../../context/AuthContext";

export default function Login() {

  const router = useRouter();
  const { t } = useTranslation("auth");
  const { login } = useAuth();

  // form states
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const [showPassword, setShowPassword] = useState(false);

  // messages
  const [error, setError] = useState("");
  const [successMessage, setSuccessMessage] = useState("");

  const [loading, setLoading] = useState(false);

  const handleLogIn = async () => {

    if (!email || !password) {
      setError("Please fill in all fields");
      return;
    }

    setLoading(true);
    setError("");
    setSuccessMessage("");

    try {

      await login(email, password);

      // success message
      setSuccessMessage("Login successful, getting things ready...");
      setTimeout(() => {
        setSuccessMessage("");
      }, 8000);

    } catch (err: any) {


      if (err?.response?.data?.message) {
        setError(err.response.data.message);
      }

      
      else if (err?.message === "Network Error") {
        setError("You are not connected to internet");
      }

      // fallback
      else {
        setError("Login failed. Please try again.");
      }

    } finally {

      setLoading(false);

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

          {/* avatar image */}
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

          <Text className="text-2xl font-bold mt-12">
            {t("login")}
          </Text>

          <Text className="text-gray-600 mb-5">
            {t("please_enter_account_details")}
          </Text>

          {/* error message */}
          {error ? (
            <Text className="text-red-500 mb-2">{error}</Text>
          ) : null}

          {/* username input */}
          <View className="flex-row items-center bg-gray-200 rounded-xl p-2 mb-3">

            <FontAwesome5
              name="user"
              size={20}
              color="black"
              style={{ marginRight: 10 }}
            />

            <TextInput
              placeholder={t("username")}
              className="flex-1 text-black"
              value={email}
              onChangeText={setEmail}
            />

          </View>

          {/* password input */}
          <View className="flex-row items-center bg-gray-200 rounded-xl p-2 mb-5">

            <Entypo
              name="lock"
              size={20}
              color="black"
              style={{ marginRight: 10 }}
            />

            <TextInput
              placeholder={t("password")}
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

          <Text className="text-purple-400 text-sm mb-2 text-right">
            {t("forgot_password")}
          </Text>

          {/* login button */}
          <MyButton
            title={loading ?  "Logging in..." : t("login")}
            style={{ width: "100%", alignSelf: "center" }}
            onPress={handleLogIn}
            disabled={loading}

          />

          {/* register link */}
          <Text className="mt-4 text-center text-gray-700">

            {t("don_t_have_an_account")}{" "}

            <Text
              className="text-purple-600"
              onPress={() => router.push("/(auth)/register")}
            >
              {t("signup")}
            </Text>

          </Text>

          {/* success message */}
          {successMessage ? (
            <Text className="text-green-500 mt-3 p-4 rounded shadow-sm bg-green-100 text-center">
              {successMessage}
            </Text>
          ) : null}

        </Card>

      </SafeAreaView>

    </KeyboardAvoidingView>
  );
}