

import { StatusBar } from 'expo-status-bar';
import { StyleSheet, View } from 'react-native';
import { Button } from "react-native-paper";
import { useRouter } from 'expo-router';
import { SafeAreaView } from "react-native-safe-area-context";

export default function App() {
  const router = useRouter();

  const handle=()=>{
      router.replace("./(protected)/loginScreen");
  }
  return (
    <SafeAreaView style={{flex:1}}>
      <View style={styles.container}>
        
        <View style={{flex:1}} />

        <Button mode="contained" onPress={handle}>
          Get Started
        </Button>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    padding: 20,
    justifyContent: "flex-end"
  },
});