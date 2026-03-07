import { View, Text, TextInput, Image, KeyboardAvoidingView } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import MyButton from "../../components/MyButton";
import { Card } from "react-native-paper";

export default function Login() {

  const router = useRouter();


    const handleLogIn = () => {
    console.log("Log in clicked")
    }

  return (
    <>
      <Image
      source={require('../../assets/images/shape.png')}
      style={{ width: 136, height: 141 }}
    />

    <KeyboardAvoidingView
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

        <Text style={{ fontSize: 20, fontWeight: 700, marginTop:40}}>Login</Text>

        <Text style={{ color: "#666", marginBottom: 20 }}>
          Please log in your account
        </Text>

        <TextInput
          placeholder="Username"
          style={{
            backgroundColor: "#eee",
            width: "100%",
            padding: 15,
            borderRadius: 10,
            marginBottom: 10
          }}
        />

        <TextInput
          placeholder="Password"
          secureTextEntry
          style={{
            backgroundColor: "#eee",
            width: "100%",
            padding: 15,
            borderRadius: 10,
            marginBottom: 20
          }}
        />

        <MyButton title="Login"
        style={{ width: "100%" , alignSelf: "center" }}
        onPress={handleLogIn} />

        <Text style={{ marginTop: 15, alignSelf: "center" }}>
          Don't have account?{" "}
          <Text
            style={{ color: "#8A4FFF" }}
            onPress={() => router.push("/(auth)/register")}
          >
            Sign Up
          </Text>
        </Text>

      </Card>
          </SafeAreaView>
    </KeyboardAvoidingView>
    </>
  );
}