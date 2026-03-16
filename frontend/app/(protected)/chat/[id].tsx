import React, { useEffect, useState, useRef, useMemo } from 'react';
import {
  View,
  Text,
  TextInput,
  Pressable,
  FlatList,
  KeyboardAvoidingView,
  Keyboard,
  StyleSheet,
  Platform,
  ActivityIndicator
} from 'react-native';
import { useLocalSearchParams } from 'expo-router';
import { MaterialCommunityIcons, Feather } from '@expo/vector-icons';
import { getMessages, sendMessage } from '@/services/api';
import { getUserToken } from '@/hooks/useAuth';
import { useAuth } from '@/context/AuthContext';
import { initializeEcho } from '@/services/echo';

type Message = {
  id: number;
  message: string;
  sender_id: number;
  is_anonymous: boolean;
  sender?: {
    name: string;
  };
};

const ChatScreen = () => {
  const { id: conversationIdParam } = useLocalSearchParams();
  const conversationId = Number(conversationIdParam);

  const { user } = useAuth();
  const [messages, setMessages] = useState<Message[]>([]);
  const [text, setText] = useState('');
  const [isAnonymous, setIsAnonymous] = useState(false);
  const [loading, setLoading] = useState(true);
  const [typingUser, setTypingUser] = useState<string | null>(null);

  const flatListRef = useRef<FlatList>(null);

  // 1. WebSocket Real-time Listener
  useEffect(() => {
    let echoInstance: any;

    const setupWebSockets = async () => {
      echoInstance = await initializeEcho();

      echoInstance.private(`chat.${conversationId}`)
        .listen('MessageSent', (e: { message: Message }) => {
          // Add message if it's from someone else
          if (e.message.sender_id !== user?.id) {
            setMessages((prev) => [...prev, e.message]);
            scrollEnd();
          }
        })
        .listenForWhisper('typing', (e: { name: string }) => {
          setTypingUser(e.name);
          setTimeout(() => setTypingUser(null), 3000);
        });
    };

    if (conversationId && user) setupWebSockets();

    return () => {
      if (echoInstance) echoInstance.leave(`chat.${conversationId}`);
    };
  }, [conversationId, user]);

  // 2. Initial Fetch
  useEffect(() => {
    const fetchInitialData = async () => {
      if (!conversationId) return;
      try {
        const token = await getUserToken();
        const data = await getMessages(conversationId, token);
        setMessages(data);
        scrollEnd();
      } catch (err) {
        console.error("Fetch error:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchInitialData();
  }, [conversationId]);

  const scrollEnd = () => {
    setTimeout(() => flatListRef.current?.scrollToEnd({ animated: true }), 100);
  };

  const handleSendMessage = async () => {
    if (!text.trim() || !conversationId) return;

    try {
      const token = await getUserToken();
      // Pass is_anonymous to your API service
      const newMessage = await sendMessage(conversationId, text, token, isAnonymous);

      setMessages(prev => [...prev, newMessage]);
      setText('');
      scrollEnd();
    } catch (err) {
      console.error("Send error:", err);
    }
  };

  if (loading) {
    return (
      <View style={[styles.container, { justifyContent: 'center' }]}>
        <ActivityIndicator size="large" color="#8A4FFF" />
      </View>
    );
  }

  return (
    <KeyboardAvoidingView
      style={{ flex: 1, backgroundColor: '#f8fafc' }}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      keyboardVerticalOffset={90}
    >
      <View style={styles.container}>
        <FlatList
          ref={flatListRef}
          data={messages}
          keyExtractor={item => item.id.toString()}
          contentContainerStyle={{ paddingVertical: 10 }}
          renderItem={({ item }) => {
            const isMe = item.sender_id === user?.id;
            
            // CRITICAL FIX: Handle anonymous display safely
            const senderDisplayName = item.is_anonymous 
              ? "Anonymous User" 
              : (item.sender?.name || "User");

            return (
              <View style={[styles.messageWrapper, isMe ? { alignItems: 'flex-end' } : { alignItems: 'flex-start' }]}>
                {!isMe && (
                  <Text style={styles.senderName}>{senderDisplayName}</Text>
                )}
                <View style={[styles.bubble, isMe ? styles.myBubble : styles.theirBubble]}>
                  <Text style={[styles.msgText, isMe ? { color: '#fff' } : { color: '#1e293b' }]}>
                    {item.message}
                  </Text>
                </View>
              </View>
            );
          }}
        />

        {typingUser && (
          <Text style={styles.typingText}>{typingUser} is typing...</Text>
        )}

        <View style={styles.inputArea}>
          {/* Toggle Anonymity */}
          <Pressable 
            onPress={() => setIsAnonymous(!isAnonymous)}
            style={[styles.anonToggle, isAnonymous && styles.anonActive]}
          >
            <Feather name="eye-off" size={18} color={isAnonymous ? "#fff" : "#94a3b8"} />
          </Pressable>

          <TextInput
            style={styles.input}
            placeholder={isAnonymous ? "Message anonymously..." : "Type a message..."}
            value={text}
            onChangeText={setText}
            multiline
          />

          <Pressable onPress={handleSendMessage} disabled={!text.trim()}>
            <MaterialCommunityIcons 
              name="send-circle" 
              size={44} 
              color={text.trim() ? "#8A4FFF" : "#cbd5e1"} 
            />
          </Pressable>
        </View>
      </View>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1, paddingHorizontal: 16 },
  messageWrapper: { marginVertical: 4, width: '100%' },
  senderName: { fontSize: 11, color: '#94a3b8', marginBottom: 2, marginLeft: 4 },
  bubble: { padding: 12, borderRadius: 20, maxWidth: '80%' },
  myBubble: { backgroundColor: '#8A4FFF', borderBottomRightRadius: 4 },
  theirBubble: { backgroundColor: '#fff', borderBottomLeftRadius: 4, borderWidth: 1, borderColor: '#e2e8f0' },
  msgText: { fontSize: 15, lineHeight: 20 },
  typingText: { fontSize: 12, color: '#94a3b8', fontStyle: 'italic', marginBottom: 8, marginLeft: 8 },
  inputArea: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    gap: 8
  },
  input: {
    flex: 1,
    backgroundColor: '#fff',
    borderRadius: 24,
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderWidth: 1,
    borderColor: '#e2e8f0',
    maxHeight: 100,
  },
  anonToggle: {
    padding: 10,
    borderRadius: 12,
    backgroundColor: '#f1f5f9'
  },
  anonActive: {
    backgroundColor: '#64748b'
  }
});

export default ChatScreen;