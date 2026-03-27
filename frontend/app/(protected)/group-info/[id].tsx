import React, { useEffect, useState } from 'react';
import { View, Text, ActivityIndicator, Pressable, ScrollView, Alert } from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { getConversation } from '@/services/api';
import { getUserToken } from '@/hooks/useAuth';
import { useAuth } from '@/context/AuthContext';
import { Feather, MaterialIcons } from '@expo/vector-icons';
import { StatusBar } from 'expo-status-bar';
import { styled } from 'nativewind';

const GroupInfoScreen = () => {
  const { id } = useLocalSearchParams();
  const conversationId = Number(id);
  const router = useRouter();
  const { user: currentUser } = useAuth();

  const [group, setGroup] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => { fetchGroup(); }, [conversationId]);

  const fetchGroup = async () => {
    try {
      const token = await getUserToken();
      const data = await getConversation(conversationId, token);
      setGroup(data);
    } catch (err) { console.error(err); } 
    finally { setLoading(false); }
  };

  if (loading) return (
    <View className="flex-1 justify-center items-center bg-slate-50">
      <ActivityIndicator size="large" color="#8A4FFF" />
    </View>
  );

  return (
    <View className="flex-1 bg-slate-50">
      <StatusBar style="dark" />
      <ScrollView showsVerticalScrollIndicator={false}>
        
        {/* HEADER CARD */}
        <View className="bg-white items-center pb-8 pt-16 rounded-b-[40px] shadow-sm border-b border-slate-100">
          <Pressable className="absolute top-12 left-5 p-2" onPress={() => router.back()}>
            <Feather name="arrow-left" size={24} color="#1E293B" />
          </Pressable>
          
          <View className="w-24 h-24 rounded-full bg-purple-600 justify-center items-center mb-4 shadow-lg shadow-purple-400">
            <Text className="text-white text-4xl font-bold">{group?.name?.charAt(0).toUpperCase()}</Text>
          </View>
          
          <Text className="text-2xl font-bold text-slate-800">{group?.name}</Text>
          <Text className="text-slate-500 text-sm mt-1">Group • {group?.participants?.length} Members</Text>
          
          <View className="flex-row mt-6 space-x-8">
            <ActionButton icon="search" label="Search" />
            <ActionButton icon="bell" label="Mute" />
            <ActionButton icon="user-plus" label="Add" onPress={() => {}} />
          </View>
        </View>

        {/* DESCRIPTION */}
        <View className="bg-white mx-4 mt-6 p-5 rounded-3xl shadow-sm">
          <Text className="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Description</Text>
          <Text className="text-slate-600 leading-5">
            {group?.description || "Welcome to the group! Keep discussions professional and helpful."}
          </Text>
        </View>

        {/* PARTICIPANTS LIST */}
        <View className="bg-white mx-4 mt-4 mb-8 rounded-3xl shadow-sm overflow-hidden">
          <View className="flex-row justify-between items-center p-5 border-b border-slate-50">
            <Text className="text-slate-800 font-bold text-lg">Participants</Text>
            <View className="bg-purple-100 px-3 py-1 rounded-full">
              <Text className="text-purple-600 text-xs font-bold">{group?.participants?.length}</Text>
            </View>
          </View>

          {group?.participants?.map((member: any) => (
            <Pressable 
              key={member.id} 
              className="flex-row items-center p-4 active:bg-slate-50 border-b border-slate-50 last:border-0"
              onPress={() => member.id !== currentUser?.id && router.push(`/user-info/${member.id}`)}
            >
              <View className="w-11 h-11 rounded-full bg-slate-200 justify-center items-center mr-4">
                <Text className="text-slate-600 font-bold">{member.name.charAt(0)}</Text>
              </View>
              <View className="flex-1">
                <Text className="text-slate-800 font-semibold">{member.id === currentUser?.id ? "You" : member.name}</Text>
                {member.is_admin && <Text className="text-purple-600 text-[10px] font-bold uppercase">Admin</Text>}
              </View>
              <Feather name="chevron-right" size={16} color="#CBD5E1" />
            </Pressable>
          ))}
        </View>

        <Pressable className="flex-row items-center justify-center mx-4 p-5 bg-red-50 rounded-2xl mb-10 border border-red-100 active:bg-red-100">
           <MaterialIcons name="logout" size={20} color="#EF4444" />
           <Text className="text-red-500 font-bold ml-2">Leave Group</Text>
        </Pressable>
      </ScrollView>
    </View>
  );
};

const ActionButton = ({ icon, label, onPress }: any) => (
  <Pressable onPress={onPress} className="items-center space-y-1">
    <View className="w-12 h-12 bg-slate-50 rounded-full justify-center items-center border border-slate-100">
      <Feather name={icon} size={20} color="#64748B" />
    </View>
    <Text className="text-slate-500 text-[10px] font-medium">{label}</Text>
  </Pressable>
);

export default GroupInfoScreen;