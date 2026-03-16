import React, { useEffect, useState } from 'react';
import { View, Text, Pressable, Image, ScrollView } from 'react-native';
import { getMentor } from '@/services/api';
import { LegendList } from '@legendapp/list';
import { FontAwesome5, Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import Toast from 'react-native-toast-message';
import { useTranslation } from "react-i18next";
import LottieView from 'lottie-react-native';

type Mentor = {
  id: number;
  name: string;
  bio: string;
  available_days: string;
  expertise_area: string;
  available_time_start: string;
  available_time_end: string;
  avatar?: string;
};

const MentorshipScreen = () => {
  const [mentors, setMentors] = useState<Mentor[]>([]);
  const [loading, setLoading] = useState(true);
  const { t } = useTranslation();

  useEffect(() => {
    fetchMentors();
  }, []);

  const fetchMentors = async () => {
    try {
      const data = await getMentor();
      setMentors(data ?? []);
    } catch (error) {
      console.log("Failed to fetch mentors:", error);
    } finally {
      setLoading(false);
    }
  };

  const handleBooking = () => {
    Toast.show({
      type: 'success',
      text1: t("Session Booked"),
      text2: t("Check your email for details"),
      position: 'top',
    });
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
        <Text className="text-white text-2xl font-bold">{t("Expert Mentors")}</Text>
        <Text className="text-violet-100 text-sm mt-1">Connect with leaders to guide your journey</Text>
      </View>

      <LegendList
        data={mentors}
        estimatedItemSize={250}
        keyExtractor={(item) => item.id.toString()}
        contentContainerStyle={{ padding: 20, paddingTop: 10 }}
        renderItem={({ item }) => {
          let days: string[] = [];
          try {
            days = item.available_days ? JSON.parse(item.available_days) : [];
          } catch {
            days = [];
          }

          return (
            <View className="bg-white rounded-3xl mb-5 overflow-hidden shadow-sm border border-slate-100">
              <View className="p-5">
                {/* Header: Avatar & Info */}
                <View className="flex-row items-start justify-between">
                  <View className="flex-row flex-1 items-center">
                    <View className="relative">
                      <Image
                        source={{
                          uri: item.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name)}&background=8b5cf6&color=fff`,
                        }}
                        className="w-16 h-16 rounded-2xl"
                      />
                      <View className="absolute -bottom-1 -right-1 bg-green-500 w-4 h-4 rounded-full border-2 border-white" />
                    </View>
                    
                    <View className="ml-4 flex-1">
                      <Text className="text-slate-900 text-lg font-bold" numberOfLines={1}>{item.name}</Text>
                      <View className="bg-violet-50 self-start px-2 py-0.5 rounded-md mt-1">
                        <Text className="text-violet-600 text-[10px] font-bold uppercase tracking-wider">
                          {item.expertise_area}
                        </Text>
                      </View>
                    </View>
                  </View>
                  
                  <Pressable className="bg-slate-50 p-2 rounded-full">
                    <Feather name="bookmark" size={18} color="#64748b" />
                  </Pressable>
                </View>

                {/* Bio */}
                <Text className="text-slate-600 text-sm mt-4 leading-5" numberOfLines={3}>
                  {item.bio}
                </Text>

                {/* Schedule & Days */}
                <View className="mt-4 pt-4 border-t border-slate-50">
                  <View className="flex-row items-center mb-3">
                    <MaterialCommunityIcons name="calendar-clock" size={18} color="#8b5cf6" />
                    <Text className="text-slate-500 text-xs ml-2 font-medium">
                      Available: {item.available_time_start} - {item.available_time_end}
                    </Text>
                  </View>

                  <View className="flex-row flex-wrap gap-2">
                    {days.map((day, index) => (
                      <View key={index} className="bg-slate-100 px-3 py-1 rounded-lg">
                        <Text className="text-slate-600 text-[10px] font-semibold">{day}</Text>
                      </View>
                    ))}
                  </View>
                </View>

                {/* Action */}
                <Pressable
                  onPress={handleBooking}
                  className="mt-5 bg-violet-600 py-3 rounded-2xl shadow-md active:bg-violet-700"
                >
                  <Text className="text-white text-center font-bold">Book Free Session</Text>
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
          </View>
        )}
      />
    </View>
  );
};

export default MentorshipScreen;