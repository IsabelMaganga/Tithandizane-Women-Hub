import { StatusBar } from 'expo-status-bar';
import { View, ImageBackground, Image,Text,Pressable } from 'react-native';
import { SafeAreaView } from "react-native-safe-area-context";
import { useRouter } from 'expo-router';
import { useAuth } from '../../../hooks/useAuth';
import Profile from '../../../components/Profile';

export default function App() {
  const router = useRouter();
  const { user } = useAuth();

  return (
    <ImageBackground
      source={require('../../../assets/images/Ellipse 4.png')}
      style={{ flex: 1, width: "100%", height: 305 }}
    >
      {/* Second image pinned to top-left corner */}
      <Image
        source={require('../../../assets/images/shape (1).png')}
        style={{
          width: 136,
          height: 141,
          
          position: 'absolute',
          top: 0,
          left: 0,
        }}
      />

      <SafeAreaView style={{ flex: 1 }}>
        <View className='mt-6 flex-row items-center justify-between px-4'>
          <Text className='text-xl font-bold text-center text-black'>Welcome {user.email.split('@')[0]} </Text>
          
          {/* navigating to profile screen */}
          <Pressable>
            <Profile />
          </Pressable>
        </View>
      </SafeAreaView>
    </ImageBackground>
  );
}