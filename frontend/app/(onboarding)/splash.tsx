import { View, Text, Pressable,Image } from "react-native";
import { useRouter } from "expo-router";
import Animated, {
  useSharedValue,
  useAnimatedStyle,
  withTiming,
  withSpring
} from "react-native-reanimated";
import { useEffect } from "react";
import { SafeAreaView } from "react-native-safe-area-context";
import MyButton from "../../components/MyButton";

export default function Splash() {

  const router = useRouter();

  const logoScale = useSharedValue(0);
  const textOpacity = useSharedValue(0);
  const buttonTranslate = useSharedValue(100);

  useEffect(() => {

    logoScale.value = withSpring(1);

    textOpacity.value = withTiming(1, { duration: 1500 });

    buttonTranslate.value = withTiming(0, { duration: 2000 });

  }, []);

  const logoStyle = useAnimatedStyle(() => ({
    transform: [{ scale: logoScale.value }]
  }));

  const textStyle = useAnimatedStyle(() => ({
    opacity: textOpacity.value
  }));

  const buttonStyle = useAnimatedStyle(() => ({
    transform: [{ translateY: buttonTranslate.value }]
  }));

  return (
    <>


    <Animated.View style={logoStyle}>
        <Image
      source={require('../../assets/images/shape.png')}
      style={{ width:"136", height: "141" }}
    />
      </Animated.View>
    <SafeAreaView className="flex-1 flex-col bg-white py-0 mt-2">

      <View className="flex-1 items-center">
        {/* Logo */}
      <Image
      source={require('../../assets/images/welcome.png')}
      style={{zIndex: 0, width: "341", height: "235" }}
      />


      <Animated.View style={logoStyle}>
        <Text className="text-black text-3xl  px-4font-semibold text-center">
          welcome to
        </Text>
      </Animated.View>

      {/* Subtitle */}
      <Animated.View style={textStyle}>
        <Text className="text-black mt-4 text-4xl text-center">
          Tithandizane Womem Hub
        </Text>
      </Animated.View>

      <Animated.View style={textStyle}>
        <Text className="text-black mt-4 px-8 text-center text-sm">
          Giving women and girls the support,
      knowledge and protection
                  they deserve
        </Text>
      </Animated.View>
      </View>

      {/* Button */}
      <Animated.View style={buttonStyle} className="mb-16">

      <MyButton
          title="Get Started"
          style={{ width: "80%" }}
          onPress={() => router.push("/(auth)/login")}
          disabled={false}
        />

      </Animated.View>

    </SafeAreaView>

    </>
  );
}