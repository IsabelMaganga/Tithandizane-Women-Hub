import React, { useEffect, useState, useMemo } from 'react';
import {
  View,
  Text,
  Image,
  ActivityIndicator,
  Pressable
} from 'react-native';
import { LegendList } from '@legendapp/list';
import { TextInput } from 'react-native-paper';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { getAllUsers } from '@/services/api';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Feather, MaterialCommunityIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { getUserToken } from '@/hooks/useAuth';

const CACHE_KEY = 'users_cache';
const CACHE_TIME_KEY = 'users_cache_time';
const CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

const UsersScreen = () => {
  const [users, setUsers] = useState<any[]>([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [token, setToken] = useState<string | null>(null);

  const router = useRouter();


  useEffect(() => {
    const loadToken = async () => {
      const t = await getUserToken();
      console.log("TOKEN:", t);
      setToken(t);
    };
    loadToken();
  }, []);

  // Load users AFTER token is ready
  useEffect(() => {
    if (token) {
      loadUsers();
    }
  }, [token]);

  // 🔍 Search filter
  const filteredUsers = useMemo(() => {
    if (!searchQuery) return users;
    return users.filter(user =>
      user.name.toLowerCase().includes(searchQuery.toLowerCase())
    );
  }, [users, searchQuery]);

  //Load from cache first
  const loadUsers = async () => {
    try {
      const now = Date.now();
      const cachedUsers = await AsyncStorage.getItem(CACHE_KEY);
      const cachedTime = await AsyncStorage.getItem(CACHE_TIME_KEY);

      if (cachedUsers && cachedTime) {
        const isExpired = now - parseInt(cachedTime) > CACHE_DURATION;

        if (!isExpired) {
          setUsers(JSON.parse(cachedUsers));
          setLoading(false);
          return;
        }
      }

      await fetchUsersFromAPI();
    } catch (error) {
      await fetchUsersFromAPI();
    } finally {
      setLoading(false);
    }
  };

  //Fetch from API
  const fetchUsersFromAPI = async () => {
    if (!token) return;

    try {
      setRefreshing(true);

      const data = await getAllUsers(token);

      setUsers(data);

      // Cache
      await AsyncStorage.setItem(CACHE_KEY, JSON.stringify(data));
      await AsyncStorage.setItem(CACHE_TIME_KEY, Date.now().toString());

    } catch (error) {
      console.error('Fetch error:', error);
    } finally {
      setRefreshing(false);
      setLoading(false);
    }
  };

  //Loading state
  if (loading && !refreshing) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <ActivityIndicator size="large" color="#8A4FFF" />
        <Text className="mt-4 text-slate-400 font-medium">
          Finding people...
        </Text>
      </View>
    );
  }

  return (
    <SafeAreaView className="flex-1 bg-white" edges={['top']}>

      {/* HEADER */}
      <View className="px-5 pt-2 pb-4">
        <Text className="text-3xl font-black text-slate-900 tracking-tight">
          Discover
        </Text>
        <Text className="text-slate-400 text-sm font-medium mb-5">
          Connect with mentors and peers
        </Text>

        <TextInput
          placeholder="Search by name..."
          value={searchQuery}
          onChangeText={setSearchQuery}
          mode="outlined"
          outlineColor="#F1F5F9"
          activeOutlineColor="#8A4FFF"
          placeholderTextColor="#94A3B8"
          style={{ backgroundColor: '#F8FAFC', height: 48 }}
          right={<TextInput.Icon icon={() => <Feather name="search" size={18} color="#94A3B8" />} />}
          className="rounded-2xl"
        />
      </View>

      {/* USERS LIST */}
      <LegendList
        data={filteredUsers}
        keyExtractor={(item) => item.id.toString()}
        estimatedItemSize={85}
        contentContainerStyle={{ paddingHorizontal: 20, paddingBottom: 40 }}
        onRefresh={fetchUsersFromAPI}
        refreshing={refreshing}

        renderItem={({ item }) => (
          <Pressable
            className="flex-row items-center py-4 border-b border-slate-50 active:bg-slate-50 rounded-xl px-2 mb-1"
            onPress={() => router.push(`/user-info/${item.id}`)}
          >
            {/* Avatar */}
            <View className="relative">
              <Image
                source={{
                  uri: item.image ||
                    `https://ui-avatars.com/api/?name=${item.name}&background=8A4FFF&color=fff`
                }}
                className="w-14 h-14 rounded-2xl bg-slate-100"
              />

              {item.is_mentor && (
                <View className="absolute -top-1 -right-1 bg-white rounded-full p-0.5 shadow-sm">
                  <MaterialCommunityIcons name="check-decagram" size={18} color="#8A4FFF" />
                </View>
              )}
            </View>

            {/* Info */}
            <View className="ml-4 flex-1">
              <Text className="text-slate-900 font-bold text-base">
                {item.name}
              </Text>
              <Text className="text-slate-400 text-xs mt-0.5 font-medium">
                {item.is_mentor ? "🌟 Verified Mentor" : "Community Member"}
              </Text>
            </View>

            {/* Arrow */}
            <View className="bg-slate-50 p-2 rounded-xl">
              <Feather name="chevron-right" size={18} color="#CBD5E1" />
            </View>
          </Pressable>
        )}

        ListEmptyComponent={
          <View className="items-center mt-20 px-10">
            <View className="bg-slate-50 p-6 rounded-full">
              <Feather name="users" size={40} color="#CBD5E1" />
            </View>
            <Text className="text-slate-900 font-bold text-lg mt-4 text-center">
              No users found
            </Text>
            <Text className="text-slate-400 text-center mt-2">
              Try searching for a different name or refresh the list.
            </Text>
          </View>
        }
      />
    </SafeAreaView>
  );
};

export default UsersScreen;