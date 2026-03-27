import React, { useEffect, useState } from 'react';
import { View, Text, ActivityIndicator, Pressable, ScrollView, Share, Image } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { getUserToken } from '@/hooks/useAuth';
import { useAuth } from '@/context/AuthContext';
import { Feather, Ionicons, MaterialCommunityIcons } from '@expo/vector-icons';
import { StatusBar } from 'expo-status-bar';
import { SafeAreaView } from 'react-native-safe-area-context';
import { getUser } from '@/services/api';

const UserInfoScreen = () => {
  const { id } = useLocalSearchParams();
  const userId = Number(id);
  const router = useRouter();
  const { user: currentUser } = useAuth();
  
  const [profile, setProfile] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => { 
    fetchUser(); 
  }, [userId]);

  const fetchUser = async () => {
    try {
      const token = await getUserToken();
      const data = await getUser(userId, token);
      setProfile(data);
    } catch (err) { 
      console.error("Error fetching user profile:", err); 
    } finally { 
      setLoading(false); 
    }
  };

  const onShare = async () => {
    try {
      await Share.share({ 
        message: `Check out ${profile?.name}'s profile on our platform!`,
        url: `https://yourapp.com/user/${userId}` 
      });
    } catch (error) { 
      console.log(error); 
    }
  };

  if (loading) return (
    <View className="flex-1 justify-center items-center bg-white">
      <ActivityIndicator size="large" color="#8A4FFF" />
      <Text className="mt-4 text-slate-400 font-medium">Loading profile...</Text>
    </View>
  );

  const isMe = profile?.id === currentUser?.id;

  return (
    <SafeAreaView className="flex-1 bg-white" edges={['top']}>
      <StatusBar style="dark" />
      
      {/* CUSTOM NAV BAR */}
      <View className="flex-row justify-between items-center px-4 h-14 border-b border-slate-50">
        <Pressable onPress={() => router.back()} className="p-2 active:opacity-50">
          <Feather name="arrow-left" size={24} color="#1E293B" />
        </Pressable>
        <Text className="text-slate-800 font-bold text-lg">Member Profile</Text>
        <Pressable onPress={onShare} className="p-2 active:opacity-50">
          <Feather name="share-2" size={22} color="#1E293B" />
        </Pressable>
      </View>

      <ScrollView showsVerticalScrollIndicator={false} className="flex-1">
        
        {/* PROFILE HEADER CARD */}
        <View className="items-center mt-8 px-6">
          <View className="relative shadow-xl shadow-purple-200">
            <View className="w-32 h-32 rounded-full bg-slate-100 items-center justify-center border-4 border-white overflow-hidden">
              {profile?.image ? (
                <Image 
                  source={{ uri: profile.image }} 
                  className="w-full h-full"
                  resizeMode="cover"
                />
              ) : (
                <Text className="text-purple-600 text-5xl font-bold">
                  {profile?.name?.charAt(0)}
                </Text>
              )}
            </View>
            
            {profile?.is_mentor && (
              <View className="absolute bottom-1 right-1 bg-white rounded-full p-0.5 shadow-sm">
                <MaterialCommunityIcons name="check-decagram" size={32} color="#8A4FFF" />
              </View>
            )}
          </View>

          <Text className="text-3xl font-extrabold text-slate-900 mt-5 text-center">
            {profile?.name}
          </Text>
          <Text className="text-slate-400 font-medium text-base">
            @{profile?.name?.toLowerCase().replace(/\s/g, '')}
          </Text>

          {profile?.is_mentor && (
            <View className="bg-purple-50 px-5 py-2 rounded-2xl mt-4 border border-purple-100">
              <Text className="text-purple-700 font-bold text-xs tracking-widest uppercase">
                Verified Mentor
              </Text>
            </View>
          )}
        </View>

        {/* BIO SECTION */}
        <View className="px-8 mt-8">
          <Text className="text-slate-400 font-bold text-[10px] uppercase tracking-widest mb-3">About</Text>
          <Text className="text-slate-600 leading-6 text-[15px]">
            {profile?.expertise_area || `Not Provided  ${profile?.location || 'Malawi'}.`}
          </Text>
        </View>

        {/* STATS STRIP */}
        <View className="flex-row bg-slate-50/80 mx-6 mt-8 rounded-3xl p-6 justify-between border border-slate-100">
          <StatItem value={profile?.collabs_count || "0"} label="Collabs" />
          <View className="w-[1px] h-8 bg-slate-200 self-center" />
          <StatItem value={profile?.rating || "5.0"} label="Rating" />
          <View className="w-[1px] h-8 bg-slate-200 self-center" />
          <StatItem value={profile?.kudos || "0"} label="Kudos" />
        </View>

        {/* ACTION BUTTONS */}
        {!isMe && (
          <View className="px-6 mt-8 gap-y-4">
            <Pressable
              className="bg-purple-600 h-16 rounded-2xl flex-row justify-center items-center shadow-lg shadow-purple-300 active:bg-purple-700"
              onPress={() => {
                router.push({
                  pathname: `/chat/${userId}`, 
                  params: {
                    isNew: 'true',
                    name: profile?.name
                  }
                });
              }}
            >
              <Ionicons name="chatbubble-ellipses" size={22} color="#FFF" />
              <Text className="text-white font-bold text-lg ml-2">Send Message</Text>
            </Pressable>

            {profile?.is_mentor && (
              <Pressable
                className="bg-white h-16 rounded-2xl flex-row justify-center items-center space-x-3 border-2 border-purple-600 active:bg-purple-50"
                onPress={() => {
                  router.push({
                    pathname: '/mentorship-request',
                    params: {
                      mentorId: profile.id,
                      mentorName: profile.name
                    }
                  });
                }}
              >
                <Feather name="calendar" size={20} color="#8A4FFF" />
                <Text className="text-purple-600 font-bold text-lg">Book Session</Text>
              </Pressable>
            )}
          </View>
        )}

        {/* INFO LIST */}
        <View className="px-6 mt-10 pb-12">
          <Text className="text-slate-400 font-bold text-[10px] uppercase tracking-widest mb-6">Contact & Details</Text>
          
          <InfoRow icon="mail" label="Email Address" value={profile?.email || "Hidden"} />
          <InfoRow icon="phone" label="Phone Number" value={profile?.phone || "Not provided"} />
          <InfoRow icon="map-pin" label="Lives in" value={profile?.location || "Malawi"} />
          <InfoRow icon="calendar" label="Member Since" value={profile?.created_at ? new Date(profile.created_at).toLocaleDateString() : "March 2025"} />
        </View>

      </ScrollView>
    </SafeAreaView>
  );
};

// Sub-components
const StatItem = ({ value, label }: { value: string; label: string }) => (
  <View className="items-center flex-1">
    <Text className="text-slate-900 font-black text-xl">{value}</Text>
    <Text className="text-slate-400 text-[10px] uppercase font-bold mt-1 tracking-tighter">{label}</Text>
  </View>
);

const InfoRow = ({ icon, label, value }: { icon: any; label: string; value: string }) => (
  <View className="flex-row items-center mb-7">
    <View className="w-12 h-12 bg-slate-50 rounded-2xl items-center justify-center border border-slate-100">
      <Feather name={icon} size={20} color="#8A4FFF" />
    </View>
    <View className="flex-1 ml-5">
      <Text className="text-slate-400 text-[11px] font-bold uppercase mb-0.5 tracking-tight">{label}</Text>
      <Text className="text-slate-800 font-semibold text-base" numberOfLines={1}>{value}</Text>
    </View>
  </View>
);

export default UserInfoScreen;