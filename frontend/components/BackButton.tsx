import { Pressable } from 'react-native'
import React from 'react'
import { SimpleLineIcons } from '@expo/vector-icons'
import { useRouter } from 'expo-router'

export const BackButton = () => {
    const router = useRouter();
  return (
    <Pressable onPress={()=> router.back()}>
          <SimpleLineIcons name="arrow-left" size={18} color="black" />
        </Pressable>
  )
}
