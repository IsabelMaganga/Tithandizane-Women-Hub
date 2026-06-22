import React, { useEffect, useState, useRef } from "react";
import {
  View,
  Text,
  TextInput,
  Pressable,
  KeyboardAvoidingView,
  Platform,
  ActivityIndicator,
} from "react-native";
import { useLocalSearchParams, useRouter } from "expo-router";
import { Feather, Ionicons } from "@expo/vector-icons";
import { LegendList } from "@legendapp/list";
import {
  getMessages,
  sendMessage,
  getConversation,
  createConversation,
} from "@/services/api";
import { getUserToken } from "@/hooks/useAuth";
import { useAuth } from "@/context/AuthContext";
import { subscribeToChatChannel } from "@/services/echo";
import { StatusBar } from "expo-status-bar";
import { SafeAreaView } from "react-native-safe-area-context";

// ─── Types ────────────────────────────────────────────────────────────────────

interface Message {
  id: number;
  sender_id: number;
  message: string;
  sender?: { name: string };
  created_at?: string;
}

interface Conversation {
  id: number;
  name: string;
  is_group: boolean;
}

// ─── Component ────────────────────────────────────────────────────────────────

const ChatScreen = () => {
  const { id, isNew, name } = useLocalSearchParams<{
    id: string;
    isNew?: string;
    name?: string;
  }>();
  const { user } = useAuth();
  const router = useRouter();

  const [activeId, setActiveId] = useState<number | null>(
    isNew === "true" ? null : Number(id)
  );
  const [messages, setMessages] = useState<Message[]>([]);
  const [conversation, setConversation] = useState<Conversation | null>(null);
  const [text, setText] = useState("");
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const [typingUser, setTypingUser] = useState<string | null>(null);

  const unsubscribeRef = useRef<(() => void) | null>(null);
  // Track optimistic IDs so WebSocket knows to replace, not duplicate
  const optimisticIdsRef = useRef<Set<number>>(new Set());
  // Track confirmed server IDs to avoid WS duplicates
  const confirmedIdsRef = useRef<Set<number>>(new Set());

  // ─── Deduplicate by ID only ────────────────────────────────────────────────
  const deduplicateMessages = (arr: Message[]): Message[] => {
    return Array.from(new Map(arr.map((m) => [m.id, m])).values());
  };

  // ─── WebSocket Sub / Unsub ─────────────────────────────────────────────────
  const setupWebsocket = (convId: number, token: string) => {
    if (unsubscribeRef.current) {
      unsubscribeRef.current();
      unsubscribeRef.current = null;
    }

    unsubscribeRef.current = subscribeToChatChannel(
      token,
      convId,
      (e: any) => {
        const incoming: Message = e.message;

        // Skip if we already have this confirmed server ID
        if (confirmedIdsRef.current.has(incoming.id)) {
          return;
        }

        confirmedIdsRef.current.add(incoming.id);

        setMessages((prev) => {
          // Replace optimistic message from same sender if exists
          const optimisticIndex = prev.findIndex(
            (m) =>
              optimisticIdsRef.current.has(m.id) &&
              m.sender_id === incoming.sender_id &&
              m.message === incoming.message
          );

          if (optimisticIndex !== -1) {
            // Replace optimistic with real message
            optimisticIdsRef.current.delete(prev[optimisticIndex].id);
            const updated = [...prev];
            updated[optimisticIndex] = incoming;
            return updated;
          }

          // Check if already in list by ID
          if (prev.some((m) => m.id === incoming.id)) {
            return prev;
          }

          // New message from other user — add directly
          return [...prev, incoming];
        });
      }
    );

    console.log(`📡 Subscribed to chat.${convId}`);
  };

  // ─── Global Cleanup ────────────────────────────────────────────────────────
  useEffect(() => {
    return () => {
      if (unsubscribeRef.current) {
        unsubscribeRef.current();
      }
    };
  }, []);

  // ─── Chat Initialization ───────────────────────────────────────────────────
  useEffect(() => {
    let isMounted = true;

    const initChat = async () => {
      try {
        const token = await getUserToken();

        if (isNew === "true" && !activeId) {
          if (isMounted) {
            setConversation({ id: 0, name: name ?? "", is_group: false });
            setLoading(false);
          }
          return;
        }

        const targetId = activeId || Number(id);

        const [msgs, convo] = await Promise.all([
          getMessages(targetId, token),
          getConversation(targetId, token),
        ]);

        if (!isMounted) return;

        const deduped = deduplicateMessages(msgs);

        // Seed confirmed IDs from history
        deduped.forEach((m) => confirmedIdsRef.current.add(m.id));

        setMessages(deduped);
        setConversation(convo);

        setupWebsocket(targetId, token);
      } catch (err) {
        console.error("Init chat error:", err);
      } finally {
        if (isMounted) setLoading(false);
      }
    };

    initChat();

    return () => {
      isMounted = false;
    };
  }, [id, activeId]);

  // ─── Send Message ──────────────────────────────────────────────────────────
  const handleSend = async () => {
    const messageText = text.trim();
    if (!messageText || sending) return;
    setSending(true);
    setText(""); // Clear immediately for better UX

    // Use negative ID to clearly mark as optimistic
    const optimisticId = -Date.now();
    optimisticIdsRef.current.add(optimisticId);

    const optimisticMsg: Message = {
      id: optimisticId,
      sender_id: user?.id ?? 0,
      message: messageText,
    };

    // Show optimistic message immediately
    setMessages((prev) => [...prev, optimisticMsg]);

    try {
      const token = await getUserToken();
      let currentConvId = activeId;

      if (!currentConvId) {
        const newConvo = await createConversation(
          { receiver_id: Number(id) },
          token
        );
        currentConvId = newConvo.id;
        setConversation(newConvo);
        setActiveId(currentConvId);
      }

      const confirmed = await sendMessage(
        currentConvId!,
        messageText,
        token,
        false
      );

      // Add confirmed ID so WS won't duplicate it
      confirmedIdsRef.current.add(confirmed.id);

      // Replace optimistic with confirmed
      setMessages((prev) => {
        const updated = prev.map((m) =>
          m.id === optimisticId ? confirmed : m
        );
        return deduplicateMessages(updated);
      });

      optimisticIdsRef.current.delete(optimisticId);
    } catch (err) {
      console.error("Send message error:", err);
      // Remove optimistic on failure
      setMessages((prev) => prev.filter((m) => m.id !== optimisticId));
      optimisticIdsRef.current.delete(optimisticId);
      setText(messageText); // Restore text on failure
    } finally {
      setSending(false);
    }
  };

  const getTitle = (): string => {
    if (!conversation) return "";
    return conversation.is_group
      ? conversation.name
      : conversation.name || name || "";
  };

  if (loading) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <ActivityIndicator size="large" color="#8A4FFF" />
      </View>
    );
  }

  return (
    <View className="flex-1 bg-[#F8FAFC]">
      <StatusBar style="dark" />

      {/* HEADER */}
      <View className="pt-14 pb-3 px-4 bg-white border-b border-slate-100 flex-row items-center shadow-sm">
        <Pressable
          onPress={() => router.back()}
          className="pr-3 active:opacity-50"
        >
          <Feather name="chevron-left" size={28} color="#1E293B" />
        </Pressable>

        <View className="w-10 h-10 rounded-full bg-purple-100 items-center justify-center">
          <Text className="text-purple-600 font-bold">
            {getTitle()?.charAt(0)?.toUpperCase()}
          </Text>
        </View>

        <View className="ml-3 flex-1">
          <Text
            className="text-slate-900 font-bold text-base"
            numberOfLines={1}
          >
            {getTitle()}
          </Text>
          <Text className="text-slate-400 text-[11px] font-medium">
            {typingUser ? `${typingUser} is typing...` : "Online"}
          </Text>
        </View>
      </View>

      {/* MESSAGES LIST */}
      <KeyboardAvoidingView
        className="flex-1"
        behavior={Platform.OS === "ios" ? "padding" : undefined}
      >
        <LegendList
          data={messages}
          estimatedItemSize={70}
          contentContainerStyle={{
            paddingHorizontal: 16,
            paddingTop: 16,
            paddingBottom: 20,
          }}
          // ← Use only the message ID as key, no index
          keyExtractor={(item) => String(item.id)}
          renderItem={({ item }) => {
            const isMe = item.sender_id === user?.id;
            return (
              <View
                className={`mb-4 max-w-[85%] ${isMe ? "self-end" : "self-start"}`}
              >
                <View
                  className={`p-4 rounded-3xl ${
                    isMe
                      ? "bg-purple-600 rounded-tr-none shadow-md shadow-purple-200"
                      : "bg-white border border-slate-100 rounded-tl-none shadow-sm"
                  }`}
                >
                  <Text
                    className={`${isMe ? "text-white" : "text-slate-800"} text-[15px] leading-5`}
                  >
                    {item.message}
                  </Text>
                </View>
                <Text
                  className={`text-[10px] text-slate-400 mt-1 px-1 ${isMe ? "text-right" : "text-left"}`}
                >
                  {isMe ? "Sent" : item.sender?.name}
                </Text>
              </View>
            );
          }}
          ListEmptyComponent={
            <View className="items-center mt-10 px-10">
              <Text className="text-slate-300 text-center font-medium">
                Your conversation starts here. Messages are encrypted and
                private.
              </Text>
            </View>
          }
        />

        {/* INPUT BAR */}
        <SafeAreaView
          edges={["bottom"]}
          className="bg-white border-t border-slate-100"
        >
          <View className="p-3 flex-row items-end space-x-2">
            <View className="flex-1 bg-slate-50 rounded-[24px] px-4 py-2 border border-slate-200 flex-row items-end">
              <TextInput
                className="flex-1 text-slate-800 text-[15px] max-h-32 py-1"
                placeholder="Type a message..."
                value={text}
                onChangeText={setText}
                multiline
                placeholderTextColor="#94A3B8"
              />
              <Pressable className="pb-1 pl-2">
                <Feather name="smile" size={20} color="#94A3B8" />
              </Pressable>
            </View>

            <Pressable
              onPress={handleSend}
              disabled={!text.trim() || sending}
              className={`w-12 h-12 rounded-full items-center justify-center shadow-lg ${
                text.trim()
                  ? "bg-purple-600 shadow-purple-300"
                  : "bg-slate-200 shadow-none"
              }`}
            >
              {sending ? (
                <ActivityIndicator size="small" color="#fff" />
              ) : (
                <Ionicons
                  name="send"
                  size={20}
                  color="white"
                  style={{ marginLeft: 3 }}
                />
              )}
            </Pressable>
          </View>
        </SafeAreaView>
      </KeyboardAvoidingView>
    </View>
  );
};

export default ChatScreen;