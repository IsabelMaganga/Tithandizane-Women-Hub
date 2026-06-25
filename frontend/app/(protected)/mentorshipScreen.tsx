import React, { useEffect, useState, useMemo } from 'react';
import { View, Text, Pressable, TextInput } from 'react-native';
import { getActiveMentors } from '@/services/api';
import { LegendList } from '@legendapp/list';
import { Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import { FontAwesome5 } from '@expo/vector-icons';
import Toast from 'react-native-toast-message';
import { useTranslation } from 'react-i18next';
import LottieView from 'lottie-react-native';
import { useRouter } from 'expo-router';
import { BackButton } from '@/components/BackButton';
import MentorCard, { Mentor } from '@/components/MentorCard'; // ← reusable card

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
      const data = await getActiveMentors();
      const safeData = Array.isArray(data) ? data : [];

      if (safeData.length === 0) {
        Toast.show({
          type: 'info',
          text1: 'No mentors available',
          text2: 'Check back later for expert mentors',
          position: 'top',
        });
      }

      setMentors(safeData);
    } catch (error: any) {
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
      const expertiseMatch =
        Array.isArray(mentor.expertise) &&
        mentor.expertise.some((exp) => exp?.toLowerCase().includes(query));
      return nameMatch || bioMatch || expertiseMatch;
    });
  }, [searchQuery, mentors]);

  if (loading) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <LottieView
          source={require('../../assets/animations/loading.json')}
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

        <View className="mb-2">
          <Text className="text-white text-2xl font-black tracking-tight">
            {t('Expert Mentors')}
          </Text>
          <Text className="text-violet-200 text-sm font-medium mt-1">
            Connect with leaders to guide your journey
          </Text>
        </View>

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
          return <MentorCard mentor={item} />; 
        }}
        ListEmptyComponent={() => (
          <View className="items-center mt-20 px-10">
            <View className="bg-slate-100 p-6 rounded-full">
              <FontAwesome5 name="user-slash" size={40} color="#cbd5e1" />
            </View>
            <Text className="text-slate-800 font-bold text-lg mt-4">
              {searchQuery ? 'No Matches Found' : 'No Mentors Yet'}
            </Text>
            <Text className="text-slate-400 text-center mt-2 leading-5">
              {searchQuery
                ? 'Try adjusting your spelling or searching for clear alternative skills.'
                : "We're currently onboarding new experts. Please check back later!"}
            </Text>
            <Pressable
              onPress={() => { setSearchQuery(''); fetchMentors(); }}
              className="mt-6 bg-purple-600 px-6 py-3 rounded-full active:opacity-90"
            >
              <Text className="text-white font-semibold">
                {searchQuery ? 'Clear Search' : 'Refresh'}
              </Text>
            </Pressable>
          </View>
        )}
      />
    </View>
  );
};

export default MentorshipScreen;