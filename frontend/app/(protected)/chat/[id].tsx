import React, { useEffect, useState, useRef } from 'react';
import { 
  View, Text, TextInput, Pressable, KeyboardAvoidingView, 
  Platform, ActivityIndicator 
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { MaterialCommunityIcons, Feather, Ionicons } from '@expo/vector-icons';
import { LegendList } from '@legendapp/list';
import { getMessages, sendMessage, getConversation, createConversation } from '@/services/api';
import { getUserToken } from '@/hooks/useAuth';
import { useAuth } from '@/context/AuthContext';
import { initializeEcho } from '@/services/echo';
import { StatusBar } from 'expo-status-bar';
import { SafeAreaView } from 'react-native-safe-area-context';

const ChatScreen = () => {
  const { id, isNew, name } = useLocalSearchParams();
  const { user } = useAuth();
  const router = useRouter();

  const [activeId, setActiveId] = useState<number | null>(isNew === 'true' ? null : Number(id));
  const [messages, setMessages] = useState<any[]>([]);
  const [conversation, setConversation] = useState<any>(null);
  const [text, setText] = useState('');
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const [typingUser, setTypingUser] = useState<string | null>(null);

  const echoRef = useRef<any>(null);

  // Setup WebSocket for conversation
  const setupWebsocket = async (convId: number) => {
    if (echoRef.current) return;
    const echo = await initializeEcho();
    echoRef.current = echo;

    echo.private(`chat.${convId}`)
      .listen('MessageSent', (e: any) => {
        setMessages(prev => prev.some(m => m.id === e.message.id) ? prev : [...prev, e.message]);
      })
      .listenForWhisper('typing', (e: { name: string }) => {
        setTypingUser(e.name);
        setTimeout(() => setTypingUser(null), 2000);
      });
  };

  // Initialize chat
  useEffect(() => {
    const initChat = async () => {
      try {
        if (isNew === 'true') {
          setConversation({ name: name, is_group: false });
          setLoading(false);
        } else {
          const token = await getUserToken();
          const [msgs, convo] = await Promise.all([
            getMessages(Number(id), token),
            getConversation(Number(id), token)
          ]);
          setMessages(msgs);
          setConversation(convo);
          setActiveId(Number(id));
          setupWebsocket(Number(id));
          setLoading(false);
        }
      } catch (err) {
        console.error('Init chat error:', err);
        setLoading(false);
      }
    };

    initChat();
    return () => echoRef.current?.leave(`chat.${activeId}`);
  }, [id]);

  // Handle sending a message
  const handleSend = async () => {
    if (!text.trim() || sending) return;
    setSending(true);

    try {
      const token = await getUserToken();
      let currentConvId = activeId;

      // Create conversation if first message
      if (!currentConvId) {
        const newConvo = await createConversation({ receiver_id: Number(id) }, token);
        currentConvId = newConvo.id;
        setActiveId(currentConvId);
        setConversation(newConvo);
        setupWebsocket(currentConvId);
      }

      // Send message
      const newMsg = await sendMessage(currentConvId, text, token, false);
      setMessages(prev => [...prev, newMsg]);
      setText('');
    } catch (err) {
      console.error('Send message error:', err);
    } finally {
      setSending(false);
    }
  };

  if (loading) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <ActivityIndicator size="large" color="#8A4FFF" />
      </View>
    );
  }

  const getTitle = () => {
    if (!conversation) return '';
    return conversation.is_group ? conversation.name : conversation.name || name;
  };

  return (
    <View className="flex-1 bg-[#F8FAFC]">
      <StatusBar style="dark" />
      
      {/* HEADER */}
      <View className="pt-14 pb-3 px-4 bg-white border-b border-slate-100 flex-row items-center shadow-sm">
        <Pressable onPress={() => router.back()} className="pr-3 active:opacity-50">
          <Feather name="chevron-left" size={28} color="#1E293B" />
        </Pressable>
        
        <View className="w-10 h-10 rounded-full bg-purple-100 items-center justify-center">
          <Text className="text-purple-600 font-bold">{getTitle()?.charAt(0)}</Text>
        </View>
        
        <View className="ml-3 flex-1">
          <Text className="text-slate-900 font-bold text-base" numberOfLines={1}>{getTitle()}</Text>
          <Text className="text-slate-400 text-[11px] font-medium">
            {typingUser ? `${typingUser} is typing...` : 'Online'}
          </Text>
        </View>
      </View>

      {/* MESSAGES */}
      <KeyboardAvoidingView
        className="flex-1"
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      >
        <LegendList
          data={messages}
          estimatedItemSize={70}
          contentContainerStyle={{ paddingHorizontal: 16, paddingTop: 16, paddingBottom: 20 }}
          keyExtractor={(item) => item.id.toString()}
          renderItem={({ item }) => {
            const isMe = item.sender_id === user?.id;
            return (
              <View className={`mb-4 max-w-[85%] ${isMe ? 'self-end' : 'self-start'}`}>
                <View className={`p-4 rounded-3xl ${isMe ? 'bg-purple-600 rounded-tr-none shadow-md shadow-purple-200' : 'bg-white border border-slate-100 rounded-tl-none shadow-sm'}`}>
                  <Text className={`${isMe ? 'text-white' : 'text-slate-800'} text-[15px] leading-5`}>
                    {item.message}
                  </Text>
                </View>
                <Text className={`text-[10px] text-slate-400 mt-1 px-1 ${isMe ? 'text-right' : 'text-left'}`}>
                  {isMe ? 'Sent' : item.sender?.name}
                </Text>
              </View>
            );
          }}
          ListEmptyComponent={
            <View className="items-center mt-10 px-10">
              <Text className="text-slate-300 text-center font-medium">
                Your conversation starts here. Messages are encrypted and private.
              </Text>
            </View>
          }
        />

        {/* INPUT */}
        <SafeAreaView edges={['bottom']} className="bg-white border-t border-slate-100">
          <View className="p-3 flex-row items-end space-x-2">
            <View className="flex-1 bg-slate-50 rounded-[24px] px-4 py-2 border border-slate-200 flex-row items-end">
              <TextInput
                className="flex-1 text-slate-800 text-[15px] max-h-32 py-1"
                placeholder="Type a message..."
                value={text}
                onChangeText={(v) => setText(v)}
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
              className={`w-12 h-12 rounded-full items-center justify-center shadow-lg ${text.trim() ? 'bg-purple-600 shadow-purple-300' : 'bg-slate-200 shadow-none'}`}
            >
              {sending ? (
                <ActivityIndicator size="small" color="#fff" />
              ) : (
                <Ionicons name="send" size={20} color="white" style={{ marginLeft: 3 }} />
              )}
            </Pressable>
          </View>
        </SafeAreaView>
      </KeyboardAvoidingView>
    </View>
  );
};

export default ChatScreen;