import { View, Text,TouchableOpacity } from 'react-native'
import React from 'react'
import { useRouter } from 'expo-router'
import { SafeAreaView } from 'react-native-safe-area-context'
import { Feather } from '@expo/vector-icons'

export const NotFound = ({description}) => {
    const router = useRouter()
  return (
    <SafeAreaView className="flex-1 bg-white items-center justify-center p-6">
            <Feather name="alert-circle" size={50} color="#CBD5E1" />
            <Text className="text-xl font-bold text-slate-900 mt-4">{description}</Text>
            <TouchableOpacity onPress={() => router.back()} className="mt-6 bg-indigo-600 px-8 py-3 rounded-2xl">
              <Text className="text-white font-bold">Go Back</Text>
            </TouchableOpacity>
          </SafeAreaView>
  )
}

