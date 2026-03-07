import { View, Text, Pressable } from "react-native";
import { useRouter } from "expo-router";
import Animated, {
  useSharedValue,
  useAnimatedStyle,
  withTiming,
  withSpring
} from "react-native-reanimated";
import { useEffect } from "react";
import { SafeAreaView } from "react-native-safe-area-context";

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
    <SafeAreaView className="flex-1 items-center justify-center bg-white-500">

      {/* Logo */}
      <Animated.View style={logoStyle}>
        <Text className="text-black text-5xl font-bold">
          WomenHub
        </Text>
      </Animated.View>

      {/* Subtitle */}
      <Animated.View style={textStyle}>
        <Text className="text-black mt-4 text-lg">
          Empowering Women Together
        </Text>
      </Animated.View>

      {/* Button */}
      <Animated.View style={buttonStyle} className="mt-16">
        <Pressable
          onPress={() => router.replace("/(auth)/login")}
          className="bg-violet-500 px-10 py-4 rounded-full"
        >
          <Text className="text-white font-bold text-lg">
            Get Started
          </Text>
        </Pressable>
      </Animated.View>

    </SafeAreaView>
  );
}