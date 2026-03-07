import { View, Text, TextInput, Image, KeyboardAvoidingView } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from "expo-router";
import MyButton from "../../components/MyButton";
import { Card } from "react-native-paper";

export default function Register() {

  const router = useRouter();
  const handleSignUp = () => {
    console.log("sign up clicked")
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

        <Text style={{ fontSize: 20, fontWeight: 700, marginTop:60}}>Sign-Up</Text>

        <Text style={{ color: "#666", marginBottom: 20 }}>
          Please sign up to continue
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
        <TextInput
          placeholder="Confirm Password"
          secureTextEntry
          style={{
            backgroundColor: "#eee",
            width: "100%",
            padding: 15,
            borderRadius: 10,
            marginBottom: 20
          }}
        />

        <MyButton title="Sign Up"
        style={{ width: "100%" , alignSelf: "center" }}
        onPress={handleSignUp} />

        <Text style={{ marginTop: 15, alignSelf: "center" }}>
          Already have account?{" "}
          <Text
            style={{ color: "#8A4FFF" }}
            onPress={() => router.push("/(auth)/login")}
          >
            Log In
          </Text>
        </Text>

      </Card>
          </SafeAreaView>
    </KeyboardAvoidingView>
    </>
  );
}