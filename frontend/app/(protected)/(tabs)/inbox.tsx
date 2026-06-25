import React, { useState, useMemo, useCallback, useRef } from "react";
import { View, Text, Pressable, Image, RefreshControl } from "react-native";
import { LegendList } from "@legendapp/list";
import { Ionicons, Feather, MaterialCommunityIcons } from "@expo/vector-icons";
import { getChatList, ChatListItem } from "@/services/api";
import { useAuth } from "@/context/AuthContext";
import { useRouter, useSegments, useFocusEffect } from "expo-router";
import { subscribeToChatChannel } from "@/services/echo";
import LottieView from "lottie-react-native";

export default function ChatListScreen() {
  const [activeTab, setActiveTab] = useState<"chats" | "groups">("chats");
  const [chats, setChats] = useState<ChatListItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const { user, token } = useAuth();
  const router = useRouter();
  const segments = useSegments();

  // Track all WebSocket unsubscribe functions
  const unsubscribeRefs = useRef<Map<number, () => void>>(new Map());

  // ─── Cleanup all WS subscriptions ─────────────────────────────────────────
  const cleanupAllSubscriptions = () => {
    unsubscribeRefs.current.forEach((unsub) => unsub());
    unsubscribeRefs.current.clear();
  };

  // ─── Subscribe to all conversations for real-time updates ─────────────────
  const subscribeToAllConversations = useCallback((chatList: ChatListItem[]) => {
    cleanupAllSubscriptions();

    if (!token) return;

    chatList.forEach((chat) => {
      if (unsubscribeRefs.current.has(chat.id)) return;

      const unsub = subscribeToChatChannel(token, chat.id, (e: any) => {
        const incoming = e.message;
        if (!incoming) return;

        setChats((prev) =>
          prev.map((c) => {
            if (c.id !== chat.id) return c;
            const isFromMe = incoming.sender_id === user?.id;
            return {
              ...c,
              messages: [incoming],
              unread_count: isFromMe
                ? c.unread_count || 0
                : (c.unread_count || 0) + 1,
            };
          })
        );
      });

      unsubscribeRefs.current.set(chat.id, unsub);
    });
  }, [token, user?.id]);

  // ─── Fetch Chats ───────────────────────────────────────────────────────────
  const fetchChats = useCallback(async (silent = false) => {
    try {
      if (!silent) setLoading(true);
      const chatData = await getChatList();
      const list = Array.isArray(chatData) ? chatData : [];
      setChats(list);
      subscribeToAllConversations(list);
    } catch (error) {
      console.error("Error fetching chats:", error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  }, [subscribeToAllConversations]);

  // Refresh when screen comes into focus
  useFocusEffect(
    useCallback(() => {
      fetchChats(true);
      return () => {
        cleanupAllSubscriptions();
      };
    }, [fetchChats])
  );

  const onRefresh = () => {
    setRefreshing(true);
    fetchChats(true);
  };

  // ─── Last Message Preview ──────────────────────────────────────────────────
  const getLastMessagePreview = (item: ChatListItem, isMe: boolean): string => {
    const msg = item.messages?.[0];
    if (!msg) return "No messages yet";
    const prefix = isMe ? "You: " : "";
    if (msg.image || msg.attachment_type === "image" || msg.type === "image") return `${prefix}📷 Photo`;
    if (msg.file || msg.attachment_type === "file" || msg.type === "file") return `${prefix}📎 File`;
    if (msg.audio || msg.type === "audio") return `${prefix}🎵 Audio`;
    if (msg.video || msg.type === "video") return `${prefix}🎥 Video`;
    return `${prefix}${msg.message || ""}`;
  };

  // ─── Time Formatter ────────────────────────────────────────────────────────
  const formatTime = (dateStr?: string): string => {
    if (!dateStr) return "";
    const date = new Date(dateStr);
    const now = new Date();
    const isToday = date.toDateString() === now.toDateString();
    const isThisWeek = now.getTime() - date.getTime() < 7 * 24 * 60 * 60 * 1000;
    if (isToday) return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    if (isThisWeek) return date.toLocaleDateString([], { weekday: "short" });
    return date.toLocaleDateString([], { day: "2-digit", month: "short" });
  };

  // ─── Filter & Sort ─────────────────────────────────────────────────────────
  const filteredChats = useMemo(() => {
    return chats
      .filter((chat) => activeTab === "chats" ? !chat.is_group : !!chat.is_group)
      .sort((a, b) => {
        const aTime = a.messages?.[0]?.created_at ? new Date(a.messages[0].created_at).getTime() : 0;
        const bTime = b.messages?.[0]?.created_at ? new Date(b.messages[0].created_at).getTime() : 0;
        return bTime - aTime;
      });
  }, [chats, activeTab]);

  const personalUnread = useMemo(() =>
    chats.filter((c) => !c.is_group).reduce((s, c) => s + (c.unread_count || 0), 0),
    [chats]
  );

  const groupUnread = useMemo(() =>
    chats.filter((c) => !!c.is_group).reduce((s, c) => s + (c.unread_count || 0), 0),
    [chats]
  );

  const totalUnread = personalUnread + groupUnread;

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
          <View>
            <Text className="text-white text-2xl font-bold">Messages</Text>
            {totalUnread > 0 && (
              <Text className="text-violet-200 text-xs mt-0.5">
                {totalUnread} unread message{totalUnread > 1 ? "s" : ""}
              </Text>
            )}
          </View>
          <Pressable
            className="bg-white/20 p-2 rounded-full"
            onPress={() => fetchChats(true)}
          >
            <Feather name="refresh-cw" size={20} color="white" />
          </Pressable>
        </View>

        {/* Tab Toggle */}
        <View className="flex-row bg-violet-700/50 p-1 rounded-2xl">
          {(["chats", "groups"] as const).map((tab) => {
            const isActive = activeTab === tab;
            const unread = tab === "chats" ? personalUnread : groupUnread;
            return (
              <Pressable
                key={tab}
                onPress={() => setActiveTab(tab)}
                className={`flex-1 py-2.5 rounded-xl flex-row items-center justify-center gap-2 ${
                  isActive ? "bg-white" : ""
                }`}
              >
                <Text
                  className={`text-center font-bold text-sm ${
                    isActive ? "text-violet-600" : "text-violet-200"
                  }`}
                >
                  {tab === "chats" ? "Personal" : "Groups"}
                </Text>
                {!isActive && unread > 0 && (
                  <View className="bg-red-500 w-2 h-2 rounded-full" />
                )}
              </Pressable>
            );
          })}
        </View>
      </View>

      {/* Chat List */}
      <View className="flex-1 px-4 pt-4">
        <LegendList
          data={filteredChats}
          estimatedItemSize={80}
          keyExtractor={(item) => `chat-${item.id}`}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={onRefresh}
              colors={["#7c3aed"]}
              tintColor="#7c3aed"
            />
          }
          renderItem={({ item }) => {
            const otherParticipant = item.participants?.find(
              (p: any) => p.id !== user?.id
            );

            const displayName = item.is_group
              ? item.name || "Unnamed Group"
              : otherParticipant?.name || "Chat User";

            const isLastMsgMe = item.messages?.[0]?.sender_id === user?.id;
            const lastMsg = getLastMessagePreview(item, isLastMsgMe);
            const time = formatTime(item.messages?.[0]?.created_at);
            const unreadCount = item.unread_count || 0;
            const hasUnread = unreadCount > 0;

            const avatarUri = item.is_group
              ? item.avatar
              : otherParticipant?.photo ||
                otherParticipant?.photo_url ||
                `https://ui-avatars.com/api/?name=${encodeURIComponent(displayName)}&background=8b5cf6&color=fff`;

            return (
              <Pressable
                onPress={() => {
                  
                  setChats((prev) =>
                    prev.map((c) =>
                      c.id === item.id ? { ...c, unread_count: 0 } : c
                    )
                  );

                  
                  router.push({
                    pathname: `/chat/${item.id}`,
                    params: {
                      name: displayName,
                      
                      sessionId: item.session_id ?? item.active_session_id ?? "",
                      
                      isMentor: user?.role === "mentor" ? "true" : "false",
                      //sessionStatus: session.status 
                    },
                  });
                }}
                className="flex-row items-center bg-white p-4 mb-3 rounded-2xl shadow-sm border border-slate-100 active:opacity-80"
              >
                {/* Avatar */}
                <View className="relative">
                  <Image
                    source={{ uri: avatarUri }}
                    className="w-14 h-14 rounded-full"
                  />
                  {!item.is_group && otherParticipant?.is_online && (
                    <View className="absolute bottom-0 right-0 w-4 h-4 bg-green-500 rounded-full border-2 border-white" />
                  )}
                </View>

                {/* Content */}
                <View className="flex-1 ml-4">
                  <View className="flex-row justify-between items-center">
                    <Text
                      className={`text-slate-900 text-base flex-1 mr-2 ${
                        hasUnread ? "font-bold" : "font-semibold"
                      }`}
                      numberOfLines={1}
                    >
                      {displayName}
                    </Text>
                    <Text
                      className={`text-[10px] ${
                        hasUnread ? "text-violet-600 font-bold" : "text-slate-400"
                      }`}
                    >
                      {time}
                    </Text>
                  </View>

                  <View className="flex-row justify-between items-center mt-1">
                    <Text
                      className={`text-sm flex-1 mr-2 ${
                        hasUnread ? "text-slate-800 font-semibold" : "text-slate-400"
                      }`}
                      numberOfLines={1}
                    >
                      {lastMsg}
                    </Text>

                    {hasUnread && (
                      <View className="bg-violet-600 min-w-[20px] h-5 px-1.5 rounded-full items-center justify-center">
                        <Text className="text-white text-[10px] font-bold">
                          {unreadCount > 99 ? "99+" : unreadCount}
                        </Text>
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
                No {activeTab === "chats" ? "personal chats" : "groups"} yet
              </Text>
              <Text className="text-slate-300 text-sm mt-1">
                Start a new conversation below
              </Text>
            </View>
          )}
        />
      </View>

      {/* FABs */}
      <View className="absolute bottom-6 right-6 flex-col gap-4">
        <Pressable
          onPress={() => router.push("../usersScreen")}
          className="bg-violet-600 w-16 h-16 rounded-full items-center justify-center shadow-xl shadow-violet-400"
        >
          <MaterialCommunityIcons name="chat-plus-outline" size={24} color="white" />
        </Pressable>
        <Pressable
          onPress={() => router.push("/mentorshipScreen")}
          className="bg-violet-600 w-16 h-16 rounded-full items-center justify-center shadow-xl shadow-violet-400"
        >
          <Ionicons name="add" size={32} color="white" />
        </Pressable>
      </View>
    </View>
  );
}