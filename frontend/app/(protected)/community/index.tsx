import React, { useEffect } from 'react';
import { useRouter } from 'expo-router';
import { View, ActivityIndicator } from 'react-native';

export default function ProtectedCommunityRedirect() {
  const router = useRouter();
  useEffect(() => { router.replace('/community'); }, []);
  return (
    <View style={{flex:1,alignItems:'center',justifyContent:'center'}}>
      <ActivityIndicator />
    </View>
  );
}
