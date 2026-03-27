import React, { useState } from 'react';
import {
  View, Text, TextInput, Pressable, ScrollView, 
  KeyboardAvoidingView, Platform, ActivityIndicator, Alert
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import { useAuth } from '@/context/AuthContext';
import { getUserToken } from '@/hooks/useAuth';
import { sendMentorshipRequest } from '@/services/api';
import { StatusBar } from 'expo-status-bar';
import Toast from 'react-native-toast-message';

const MentorshipRequestScreen = () => {
  const { mentorId, mentorName } = useLocalSearchParams();
  const router = useRouter();
  const { token } = useAuth();

  const [topic, setTopic] = useState('');
  const [message, setMessage] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async () => {
    if (!topic.trim()) {
      Alert.alert("Missing Info", "Please provide a topic for the session.");
      return;
    }

    setLoading(true);
    if (!topic.trim()) {
      Toast.show({
        type: 'error',
        text1: 'Topic Required',
        text2: 'Please tell the mentor what you want to learn. 👋',
        position: 'top',
      });
      return;
    }
    try {
      const userToken = await getUserToken();
      await sendMentorshipRequest({
        mentor_id: Number(mentorId),
        topic: topic,
        message: message
      }, userToken || token);

      Toast.show({
        type: 'success',
        text1: 'Request Sent!',
        text2: `We've notified ${mentorName} for you.`,
        visibilityTime: 3000,
      });
      router.back();
    } catch (error) {
      console.error(error);
      Alert.alert("Error", "Could not send request. Please try again later.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView className="flex-1 bg-white" edges={['top']}>
      <StatusBar style="dark" />
      
      {/* HEADER */}
      <View className="px-6 py-4 flex-row items-center border-b border-slate-50">
        
        <Pressable onPress={() => router.back()} className="mr-4">
          <Feather name="x" size={24} color="#1E293B" />
        </Pressable>
        <Text className="text-xl font-bold text-slate-900">Request Mentorship</Text>
      </View>

      <KeyboardAvoidingView 
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        className="flex-1"
      >
        <ScrollView className="flex-1 px-6 pt-6" showsVerticalScrollIndicator={false}>
          {/* MENTOR INFO CARD */}
          <View className="bg-purple-50 p-5 rounded-3xl mb-8 flex-row items-center border border-purple-100">
            <View className="w-12 h-12 rounded-2xl bg-purple-600 items-center justify-center">
              <Text className="text-white font-bold text-lg">{mentorName?.toString().charAt(0)}</Text>
            </View>
            <View className="ml-4">
              <Text className="text-purple-900 font-bold text-base">Session with {mentorName}</Text>
              <Text className="text-purple-600 text-xs font-medium">Verified Mentor</Text>
            </View>
          </View>

          {/* TOPIC INPUT */}
          <View className="mb-6">
            <Text className="text-slate-900 font-bold mb-2 ml-1">What do you want to learn? *</Text>
            <View className="bg-slate-50 rounded-2xl border border-slate-100 px-4 py-3 flex-row items-center">
              <Feather name="book-open" size={18} color="#8A4FFF" />
              <TextInput
                className="flex-1 ml-3 text-slate-800 font-medium"
                placeholder="Career Advice"
                value={topic}
                onChangeText={setTopic}
                placeholderTextColor="#94A3B8"
              />
            </View>
          </View>

          {/* MESSAGE INPUT */}
          <View className="mb-8">
            <Text className="text-slate-900 font-bold mb-2 ml-1">Add a message (Optional)</Text>
            <View className="bg-slate-50 rounded-3xl border border-slate-100 px-4 py-4 min-h-[150px]">
              <TextInput
                className="flex-1 text-slate-800"
                placeholder="Tell the mentor a bit about your goals and what you hope to achieve in this session..."
                value={message}
                onChangeText={setMessage}
                multiline
                textAlignVertical="top"
                placeholderTextColor="#94A3B8"
              />
            </View>
          </View>

          <View className="bg-slate-50 p-4 rounded-2xl flex-row items-start mb-10">
            <Feather name="info" size={16} color="#64748B" style={{ marginTop: 2 }} />
            <Text className="text-slate-500 text-xs ml-3 leading-4">
              Mentors usually respond within 24-48 hours. Make sure your topic is clear to increase your chances of acceptance.
            </Text>
          </View>
        </ScrollView>

        {/* SUBMIT BUTTON */}
        <View className="pb-10 border-t border-slate-50 bg-white">
          <Pressable
            onPress={handleSubmit}
            disabled={loading}
            className={`h-16 rounded-2xl flex-row items-center justify-center shadow-lg ${
              loading ? 'bg-slate-200' : 'bg-purple-600 shadow-purple-200'
            }`}
          >
            {loading ? (
              <ActivityIndicator color="white" />
            ) : (
              <>
                <Text className="text-white font-bold text-lg mr-2">Send Request</Text>
                <MaterialCommunityIcons name="send" size={20} color="white" />
              </>
            )}
          </Pressable>
        </View>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
};

export default MentorshipRequestScreen;