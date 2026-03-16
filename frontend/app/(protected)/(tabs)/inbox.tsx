import React, { useEffect, useState, useMemo } from "react";
import { View, Text, Pressable, Image } from "react-native";
import { LegendList } from "@legendapp/list";
import { Ionicons, Feather } from "@expo/vector-icons";
import { getChatList } from "@/services/api";
import { getUserToken } from "@/hooks/useAuth";
import { useRouter, useSegments } from "expo-router";
import LottieView from "lottie-react-native";

export default function ChatListScreen() {
  const [activeTab, setActiveTab] = useState<"chats" | "groups">("chats");
  const [chats, setChats] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  const router = useRouter();
  const segments = useSegments();

  useEffect(() => {
    fetchChats();
  }, []);

  const fetchChats = async () => {
    try {
      setLoading(true);
      const token = await getUserToken();
      if (!token) return;

      const chatData = await getChatList(token);

      setChats(Array.isArray(chatData) ? chatData : []);
    } catch (error) {
      console.error("Error fetching chats:", error);
    } finally {
      setLoading(false);
    }
  };

  //filtering chats
  const filteredChats = useMemo(() => {
    return chats.filter(chat =>
      activeTab === "chats" ? !chat.is_group : chat.is_group
    );
  }, [chats, activeTab]);


  if (!segments) return null;

  if (loading) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <LottieView
          source={require("../../../assets/animations/loading.json")}
          autoPlay
          loop
          style={{ width: 150, height: 150 }}
        />
      </View>
    );
  }

  return (
    <View className="flex-1 bg-slate-50">
      {/* Header */}
      <View className="bg-violet-600 pt-14 pb-6 px-6 rounded-b-[32px] shadow-sm">
        <View className="flex-row justify-between items-center mb-6">
          <Text className="text-white text-2xl font-bold">Messages</Text>
          <Pressable className="bg-white/20 p-2 rounded-full">
            <Feather name="search" size={20} color="white" />
          </Pressable>
        </View>

        {/* Tab Toggle */}
        <View className="flex-row bg-violet-700/50 p-1 rounded-2xl">
          <Pressable
            onPress={() => setActiveTab("chats")}
            className={`flex-1 py-2.5 rounded-xl ${activeTab === "chats" ? 'bg-white' : ''}`}
          >
            <Text className={`text-center font-bold text-sm ${activeTab === "chats" ? 'text-violet-600' : 'text-violet-200'}`}>
              Personal
            </Text>
          </Pressable>
          <Pressable
            onPress={() => setActiveTab("groups")}
            className={`flex-1 py-2.5 rounded-xl ${activeTab === "groups" ? 'bg-white' : ''}`}
          >
            <Text className={`text-center font-bold text-sm ${activeTab === "groups" ? 'text-violet-600' : 'text-violet-200'}`}>
              Groups
            </Text>
          </Pressable>
        </View>
      </View>

      {/* Chat List */}
      <View className="flex-1 px-4 pt-4">
        <LegendList
          data={filteredChats}
          estimatedItemSize={80}

          keyExtractor={(item) => `chat-${item.id}-${activeTab}`}
          renderItem={({ item }) => {

            const displayName = item.is_group 
              ? (item.name || "Unnamed Group") 
              : (item.participants?.find((p: any) => p.name)?.name || "Chat");

            const lastMsg = item.messages?.[0]?.message || "No messages yet";
            const time = item.messages?.[0]?.created_at 
                ? new Date(item.messages[0].created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                : "";

            return (
              <Pressable 
                onPress={() => router.push(`/chat/${item.id}`)}

                className="flex-row items-center bg-white p-4 mb-3 rounded-2xl shadow-sm border border-slate-100"
              >
                {/* Avatar */}
                <View className="relative">
                  <Image
                    source={{ uri: item.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName)}&background=8b5cf6&color=fff` }}
                    className="w-14 h-14 rounded-full"
                  />
                  {!item.is_group && item.is_online && (
                    <View className="absolute bottom-0 right-0 w-4 h-4 bg-green-500 rounded-full border-2 border-white" />
                  )}
                </View>

                {/* Message Content */}
                <View className="flex-1 ml-4">
                  <View className="flex-row justify-between items-center">
                    <Text className="text-slate-900 font-bold text-base" numberOfLines={1}>
                      {displayName}
                    </Text>
                    <Text className="text-slate-400 text-[10px]">
                      {time}
                    </Text>
                  </View>
                  
                  <View className="flex-row justify-between items-center mt-1">
                    <Text className="text-slate-500 text-sm flex-1 mr-2" numberOfLines={1}>
                      {lastMsg}
                    </Text>
                    {item.unread_count > 0 && (
                      <View className="bg-violet-600 px-2 py-0.5 rounded-full">
                        <Text className="text-white text-[10px] font-bold">{item.unread_count}</Text>
                      </View>
                    )}
                  </View>
                </View>
              </Pressable>
            );
          }}
          ListEmptyComponent={() => (
            <View className="items-center mt-20">
              <View className="bg-slate-100 p-6 rounded-full mb-4">
                <Feather name="message-square" size={40} color="#cbd5e1" />
              </View>
              <Text className="text-slate-400 text-base font-medium">
                No {activeTab} found
              </Text>
            </View>
          )}
        />
      </View>

      {/* Floating Action Button */}
      <Pressable
        onPress={() => router.push("/mentorshipScreen")}
        className="absolute bottom-8 right-8 bg-violet-600 w-16 h-16 rounded-full items-center justify-center shadow-xl shadow-violet-400"
      >
        <Ionicons name="add" size={32} color="white" />
      </Pressable>
    </View>
  );
}