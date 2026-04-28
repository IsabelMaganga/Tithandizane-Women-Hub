import React, { useEffect, useState } from 'react';
import { View, Text, Pressable, Image } from 'react-native';
import { getActiveMentors } from '@/services/api';
import { LegendList } from '@legendapp/list';
import { FontAwesome5, Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import Toast from 'react-native-toast-message';
import { useTranslation } from "react-i18next";
import LottieView from 'lottie-react-native';
import { useRouter } from 'expo-router';
import { BackButton } from '@/components/BackButton';

type Mentor = {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  location: string | null;
  photo: string | null;
  avatar?: string;
  expertise: string[];
  bio: string;
  availability: string | null;
  available_days: string[];
  available_time_start: string | null;
  available_time_end: string | null;
  linkedin_url: string | null;
  twitter_url: string | null;
  website_url: string | null;
  rating?: number | null;
  total_sessions?: number;
  status?: string;
};

const MentorshipScreen = () => {
  const [mentors, setMentors] = useState<Mentor[]>([]);
  const [loading, setLoading] = useState(true);
  const { t } = useTranslation();
  const router = useRouter();

  useEffect(() => {
    fetchMentors();
  }, []);

  const fetchMentors = async () => {
    try {
      setLoading(true);
      console.log('🔄 Fetching mentors...');
      
      const data = await getActiveMentors();
      
      console.log('📊 Received mentors:', data.length);
      
      if (data.length === 0) {
        console.warn('⚠️ No mentors received');
        Toast.show({
          type: 'info',
          text1: 'No mentors available',
          text2: 'Check back later for expert mentors',
          position: 'top',
        });
      } else {
        console.log('✅ First mentor:', data[0].name, 'Expertise:', data[0].expertise);
      }
      
      setMentors(data);
    } catch (error: any) {
      console.error("❌ fetchMentors error:", error?.message || error);
      Toast.show({
        type: 'error',
        text1: 'Error',
        text2: error?.message || 'Failed to load mentors',
        position: 'top',
      });
      setMentors([]);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <LottieView
          source={require("../../assets/animations/loading.json")}
          autoPlay
          loop
          style={{ width: 150, height: 150 }}
        />
        <Text className="text-gray-400 font-medium mt-2">Finding experts...</Text>
      </View>
    );
  }

  return (
    <View className="flex-1 bg-slate-50">
      {/* Header Info */}
      <View className="bg-violet-600 pt-14 pb-10 px-6 rounded-b-[40px] shadow-xl">
        <View className='text-blue-50 color-white'><BackButton /></View>
        <Text className="text-white text-2xl font-bold">{t("Expert Mentors")}</Text>
        <Text className="text-violet-100 text-sm mt-1">Connect with leaders to guide your journey</Text>
        <Pressable
            onPress={() => router.push('/(protected)/sessionsDashboard')}
            className="flex-row items-center bg-purple-50 px-3 py-2 mt-2 rounded-xl border border-purple-100 active:bg-purple-100"
          >
            <MaterialCommunityIcons name="calendar-clock" size={18} color="#8A4FFF" />
            <Text className="text-purple-600 font-bold text-xs ml-2">My Sessions</Text>
          </Pressable>
      </View>

      <LegendList
        data={mentors}
        estimatedItemSize={250}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={{ padding: 20, paddingTop: 10 }}
        renderItem={({ item }) => {
          // Get expertise area
          const expertiseArea = item.expertise && item.expertise.length > 0 
            ? item.expertise.join(', ') 
            : 'Mentor';
          
          // Get avatar URL
          const avatarUrl = item.avatar || item.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=8b5cf6&color=fff`;

          return (
            <View className="bg-white rounded-3xl mb-5 overflow-hidden shadow-sm border border-slate-100">
              <View className="p-5">
                {/* Header: Avatar & Info */}
                <View className="flex-row items-start justify-between">
                  <View className="flex-row flex-1 items-center">
                    <View className="relative">
                      <Image
                        source={{ uri: avatarUrl }}
                        className="w-16 h-16 rounded-2xl"
                        style={{ backgroundColor: '#f0f0f0' }}
                      />
                      <View className="absolute -bottom-1 -right-1 bg-green-500 w-4 h-4 rounded-full border-2 border-white" />
                    </View>
                    
                    <View className="ml-4 flex-1">
                      <Text className="text-slate-900 text-lg font-bold" numberOfLines={1}>
                        {item.name}
                      </Text>
                      <View className="bg-violet-50 self-start px-2 py-0.5 rounded-md mt-1">
                        <Text className="text-violet-600 text-[10px] font-bold uppercase tracking-wider">
                          {expertiseArea}
                        </Text>
                      </View>
                    </View>
                  </View>
                  
                  <Pressable className="bg-slate-50 p-2 rounded-full">
                    <Feather name="bookmark" size={18} color="#64748b" />
                  </Pressable>
                </View>

                {/* Bio */}
                {item.bio && (
                  <Text className="text-slate-600 text-sm mt-4 leading-5" numberOfLines={3}>
                    {item.bio}
                  </Text>
                )}

                {/* Schedule & Days */}
                <View className="mt-4 pt-4 border-t border-slate-50">
                  <View className="flex-row items-center mb-3">
                    <MaterialCommunityIcons name="calendar-clock" size={18} color="#8b5cf6" />
                    <Text className="text-slate-500 text-xs ml-2 font-medium">
                      Available: {item.available_time_start || '09:00'} - {item.available_time_end || '17:00'}
                    </Text>
                  </View>

                  {item.available_days && item.available_days.length > 0 && (
                    <View className="flex-row flex-wrap gap-2">
                      {item.available_days.map((day, index) => (
                        <View key={index} className="bg-slate-100 px-3 py-1 rounded-lg">
                          <Text className="text-slate-600 text-[10px] font-semibold capitalize">{day}</Text>
                        </View>
                      ))}
                    </View>
                  )}
                </View>

                {/* Action */}
                <Pressable
                    className="bg-purple-600 h-16 rounded-2xl flex-row justify-center items-center space-x-3 border-2 border-purple-600 active:bg-purple-50 mt-4"
                    onPress={() => {
                      router.push({
                        pathname: '/mentorship-request',
                        params: {
                          mentorId: item.id.toString(),
                          mentorName: item.name
                        }
                      });
                    }}
                  >
                  <Text className="text-white text-center font-bold text-base">Book Free Session</Text>
                </Pressable>
              </View>
            </View>
          );
        }}
        ListEmptyComponent={() => (
          <View className="items-center mt-20 px-10">
            <View className="bg-slate-100 p-6 rounded-full">
              <FontAwesome5 name="user-slash" size={40} color="#cbd5e1" />
            </View>
            <Text className="text-slate-800 font-bold text-lg mt-4">No Mentors Yet</Text>
            <Text className="text-slate-400 text-center mt-2 leading-5">
              We're currently onboarding new experts. Please check back later!
            </Text>
            <Pressable 
              onPress={fetchMentors}
              className="mt-6 bg-purple-600 px-6 py-3 rounded-full"
            >
              <Text className="text-white font-semibold">Refresh</Text>
            </Pressable>
          </View>
        )}
      />
    </View>
  );
};

export default MentorshipScreen;