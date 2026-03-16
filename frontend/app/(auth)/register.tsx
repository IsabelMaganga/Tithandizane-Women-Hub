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
import MaterialIcons from "@expo/vector-icons/build/MaterialIcons";
import FontAwesome from "@expo/vector-icons/build/FontAwesome";
//import { TextInput  } from 'react-native-paper'
import { useAuth } from '../../context/AuthContext'


export default function Register() {

  const router = useRouter();
  const { t } = useTranslation("auth");
  const { register } = useAuth();

   //grabing inputs from the user
  const [ username,setUsername] = useState("");
  const [ email,setEmail] = useState("");
  const [ phone,setPhone ] = useState("");
  const [ password,setPassword] = useState("");
  const [ confirmPassword,setConfirmPassword] = useState("");

  const [ error,setError] =useState("");
  const [showPassword, setShowPassword] = useState(false);

  const handleSignUp = () => {
    if(!username &&  !email && !password && !confirmPassword){
      setError("Please Fill all required fields")
      return;
    }else if ( !username){
      setError("Username is requred!");
      return

    } else if(!email){
      setError("Email is required!");
      return;
    } else{
register(username,email,phone,password,confirmPassword)? router.replace("/(auth)/login") : null;
    }

    

    
  }

  return (
    <>
      <Image
      source={require('../../assets/images/shape.png')}
      style={{ width: 136, height: 141 }}
    />

    <KeyboardAvoidingView
      behavior={Platform.OS === "ios" ? "padding" : "height"}
      style={{
        flex: 1,
        backgroundColor: "#F2F2F2",
        alignItems: "center",
        justifyContent: "center"

    }}>
      <SafeAreaView>

      <Card
        style={{
          backgroundColor: "#fff",
          width: 350,
          padding: 20,
          elevation: 4,
          overflow: "visible"
        }}
      >

        <Image
          source={require("../../assets/images/Ellipse 3.png")}
          style={{
            position: "absolute",
            alignSelf: "center",
            top: -50,
            width: 100,
            height: 100
          }}
        />

        <Text style={{ fontSize: 20, fontWeight: 700, marginTop:60}}>{t("register")}</Text>

        <Text style={{ color: "#666", marginBottom: 20 }}>
          {t("please_signup_details")}
        </Text>
        { error? <Text className="text-red-700 mb-2">{error}</Text>: null}

        {/* Username Input */}
          <View className="flex-row items-center bg-gray-200 rounded-xl p-2 mb-3">
            <FontAwesome5 name="user" size={20} color="black" className="mr-3" />
            <TextInput
              placeholder={t("username")}
              className="flex-1 text-black"
              value={username}
              onChangeText={setUsername}

            />
          </View>

          {/* Email Input */}
          <View className="flex-row items-center bg-gray-200 rounded-xl p-2 mb-3">
            <MaterialIcons name="email" size={24} color="black"  className="mr-3" />
            <TextInput
              placeholder={t("email")}
              className="flex-1 text-black"
              value={email}
              onChangeText={setEmail}

            />
          </View>
          {/*Phone Input */}
          <View className="flex-row items-center bg-gray-200 rounded-xl p-2 mb-3">
            <FontAwesome name="phone-square" size={24} color="black"   className="mr-3" />
            <TextInput
              placeholder={t("(optional)")}
              className="flex-1 text-black"
              value={phone}
              onChangeText={setPhone}

            />
          </View>

       {/* Password Input */}
          <View className="flex-row items-center bg-gray-200 rounded-xl p-2 mb-5">
            <Entypo name="lock" size={20} color="black" className="mr-3" />
            <TextInput
              placeholder={t("password")}
              secureTextEntry={!showPassword} // toggle secure text
              className="flex-1 text-black"
              value={password}
              onChangeText={setPassword}
            />
            <TouchableOpacity onPress={() => setShowPassword(!showPassword)} className="absolute right-3">
              <AntDesign
                name={showPassword ? "eye-invisible" : "eye"}
                size={24}
                color="black"
              />
            </TouchableOpacity>
          </View>

          {/* Password Input */}
          <View className="flex-row items-center bg-gray-200 rounded-xl p-2 mb-5">
            <Entypo name="lock" size={20} color="black" className="mr-3" />
            <TextInput
              placeholder={t("password")}
              secureTextEntry={!showPassword} // toggle secure text
              className="flex-1 text-black"
              value={confirmPassword}
              onChangeText={setConfirmPassword}
            />
            <TouchableOpacity onPress={() => setShowPassword(!showPassword)} className="absolute right-3">
              <AntDesign
                name={showPassword ? "eye-invisible" : "eye"}
                size={24}
                color="black"
              />
            </TouchableOpacity>
          </View>

        <MyButton title={t("signup")}
        style={{ width: "100%" , alignSelf: "center" }}
        onPress={handleSignUp} />

        <Text style={{ marginTop: 15, alignSelf: "center" }}>
          {t("already_have_an_account")}{" "}
          <Text
            style={{ color: "#8A4FFF" }}
            onPress={() => router.push("/(auth)/login")}
          >
            {t("login")}
          </Text>
        </Text>

      </Card>
          </SafeAreaView>
    </KeyboardAvoidingView>
    </>
  );
}