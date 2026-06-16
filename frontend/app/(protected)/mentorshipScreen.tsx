import React, { useEffect, useState, useMemo } from 'react';
import { View, Text, Pressable, Image, TextInput } from 'react-native';
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
  const [searchQuery, setSearchQuery] = useState('');
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
      const safeData = Array.isArray(data) ? data : [];
      console.log('📊 Received mentors:', safeData.length);
      
      if (safeData.length === 0) {
        console.warn('⚠️ No mentors received');
        Toast.show({
          type: 'info',
          text1: 'No mentors available',
          text2: 'Check back later for expert mentors',
          position: 'top',
        });
      }
      
      setMentors(safeData);
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

  const filteredMentors = useMemo(() => {
    if (!searchQuery.trim()) return mentors;
    
    const query = searchQuery.toLowerCase().trim();
    return mentors.filter((mentor) => {
      if (!mentor) return false;
      const nameMatch = mentor.name?.toLowerCase().includes(query) ?? false;
      const bioMatch = mentor.bio?.toLowerCase().includes(query) ?? false;
      const expertiseMatch = Array.isArray(mentor.expertise) && mentor.expertise.some(exp => 
        exp?.toLowerCase().includes(query)
      );
      
      return nameMatch || bioMatch || expertiseMatch;
    });
  }, [searchQuery, mentors]);

  // Helper component to render 5 purple hearts safely
  const RenderHeartRating = ({ rating }: { rating: number | null | undefined }) => {
    if (!rating) {
      return (
        <View className="bg-purple-50 px-2 py-0.5 rounded-md">
          <Text className="text-purple-600 text-[11px] font-bold">New Mentor</Text>
        </View>
      );
    }

    const roundedRating = Math.round(rating);
    
    return (
      <View className="flex-row items-center space-x-0.5">
        {[1, 2, 3, 4, 5].map((starIndex) => (
          <FontAwesome5
            key={starIndex}
            name="heart"
            size={12}
            color="#8A4FFF"
            solid={starIndex <= roundedRating}
          />
        ))}
        <Text className="text-slate-500 text-xs font-bold ml-1.5">
          ({Number(rating).toFixed(1)})
        </Text>
      </View>
    );
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
      {/* Header Panel */}
      <View className="bg-violet-600 pt-12 pb-8 px-6 rounded-b-[40px] shadow-xl z-10">
        {/* --- Top Utility Bar Row --- */}
        <View className="flex-row justify-between items-center mb-5">
          <BackButton />
          
          <Pressable
            onPress={() => router.push('/(protected)/sessionsDashboard')}
            className="flex-row items-center bg-white/20 px-4 py-2 rounded-xl active:bg-white/30 border border-white/10"
          >
            <MaterialCommunityIcons name="calendar-clock" size={16} color="white" />
            <Text className="text-white font-bold text-xs ml-2">My Sessions</Text>
          </Pressable>
        </View>

        {/* --- Title Block column alignment --- */}
        <View className="mb-2">
          <Text className="text-white text-2xl font-black tracking-tight">{t("Expert Mentors")}</Text>
          <Text className="text-violet-200 text-sm font-medium mt-1">Connect with leaders to guide your journey</Text>
        </View>
        
        {/* Search Architecture Box */}
        <View className="flex-row items-center bg-white/10 mt-4 px-4 py-3 rounded-2xl border border-white/20">
          <Feather name="search" size={18} color="#ddd6fe" />
          <TextInput
            className="flex-1 ml-3 text-white placeholder-violet-200 text-sm h-6 p-0"
            placeholder="Search by specialty, name or experience..."
            placeholderTextColor="#ddd6fe"
            value={searchQuery}
            onChangeText={setSearchQuery}
            autoCapitalize="none"
            autoCorrect={false}
          />
          {searchQuery.length > 0 && (
            <Pressable onPress={() => setSearchQuery('')} className="p-1">
              <Feather name="x" size={16} color="#ddd6fe" />
            </Pressable>
          )}
        </View>
      </View>

      <LegendList
        data={filteredMentors}
        estimatedItemSize={250}
        keyExtractor={(item) => item?.id?.toString() || Math.random().toString()}
        contentContainerStyle={{ padding: 20, paddingTop: 15 }}
        renderItem={({ item }) => {
          if (!item) return null;

          const expertiseArea = Array.isArray(item.expertise) && item.expertise.length > 0 
            ? item.expertise.join(', ') 
            : 'Mentor';
          
          const avatarUrl = item.avatar || item.photo || `https://ui-avatars.com/api/?name=${encodeURIComponent(item.name || 'M')}&background=8b5cf6&color=fff`;

          return (
            <View className="bg-white rounded-3xl mb-5 overflow-hidden shadow-xs border border-slate-100">
              <View className="p-5">
                {/* Identity Frame */}
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
                      
                      {/* Heart Ratings Insertion */}
                      <View className="mt-1">
                        <RenderHeartRating rating={item.rating} />
                      </View>

                      <View className="bg-violet-50 self-start px-2 py-0.5 rounded-md mt-2">
                        <Text className="text-violet-600 text-[10px] font-bold uppercase tracking-wider" numberOfLines={1}>
                          {expertiseArea}
                        </Text>
                      </View>
                    </View>
                  </View>
                  
                  <Pressable className="bg-slate-50 p-2 rounded-full active:bg-slate-100">
                    <Feather name="bookmark" size={18} color="#64748b" />
                  </Pressable>
                </View>

                {/* Profile Bio */}
                {item.bio && (
                  <Text className="text-slate-600 text-sm mt-4 leading-5" numberOfLines={3}>
                    {item.bio}
                  </Text>
                )}

                {/* Calendars */}
                <View className="mt-4 pt-4 border-t border-slate-100">
                  <View className="flex-row items-center mb-3">
                    <MaterialCommunityIcons name="calendar-clock" size={18} color="#8b5cf6" />
                    <Text className="text-slate-500 text-xs ml-2 font-medium">
                      Available: {item.available_time_start || '09:00'} - {item.available_time_end || '17:00'}
                    </Text>
                  </View>

                  {Array.isArray(item.available_days) && item.available_days.length > 0 && (
                    <View className="flex-row flex-wrap gap-2">
                      {item.available_days.map((day, index) => (
                        <View key={index} className="bg-slate-100 px-3 py-1 rounded-lg">
                          <Text className="text-slate-600 text-[10px] font-semibold capitalize">{day}</Text>
                        </View>
                      ))}
                    </View>
                  )}
                </View>

                {/* Action Button */}
                <Pressable
                  className="bg-purple-600 h-14 rounded-2xl flex-row justify-center items-center border border-purple-600 active:opacity-85 mt-4"
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
            <Text className="text-slate-800 font-bold text-lg mt-4">
              {searchQuery ? "No Matches Found" : "No Mentors Yet"}
            </Text>
            <Text className="text-slate-400 text-center mt-2 leading-5">
              {searchQuery 
                ? "Try adjusting your spelling or searching for clear alternative skills." 
                : "We're currently onboarding new experts. Please check back later!"}
            </Text>
            <Pressable 
              onPress={() => {
                setSearchQuery('');
                fetchMentors();
              }}
              className="mt-6 bg-purple-600 px-6 py-3 rounded-full active:opacity-90"
            >
              <Text className="text-white font-semibold">
                {searchQuery ? "Clear Search" : "Refresh"}
              </Text>
            </Pressable>
          </View>
        )}
      />
    </View>
  );
};

export default MentorshipScreen;