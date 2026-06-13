import React, { useState, useEffect } from "react";
import { 
  View, Text, TextInput, TouchableOpacity, 
  FlatList, Modal, KeyboardAvoidingView, Platform, 
  StatusBar, ScrollView
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons"; 
import * as ScreenCapture from 'expo-screen-capture'; // Import Security Module

// --- LEGEND DATA ---
const CATEGORY_LEGEND = [
  { id: 1, label: "Education", color: "bg-blue-500", icon: "book" },
  { id: 2, label: "Health", color: "bg-emerald-500", icon: "medkit" },
  { id: 3, label: "Safety", color: "bg-rose-500", icon: "shield-checkmark" },
];

const INITIAL_POSTS = [
  {
    id: "1",
    author: "Zione Banda",
    role: "Student",
    category: "Education",
    avatarColor: "#3b82f6", 
    text: "Does anyone know where I can find resources for secondary school bursaries? I really want to finish my MSCE. #EducationForAll",
    comments: ["Check out CAMFED!", "I will share a link soon."],
    timestamp: "1h ago",
    likes: 15,
  },
  {
    id: "2",
    author: "Grace Phiri",
    role: "Health Advocate",
    category: "Health",
    avatarColor: "#10b981", 
    text: "Mental health is just as important as physical health. If you are feeling overwhelmed, please speak out. ❤️",
    comments: ["Thank you Grace."],
    timestamp: "3h ago",
    likes: 42,
  }
];

export default function Community() {
  const [posts, setPosts] = useState(INITIAL_POSTS);
  const [postText, setPostText] = useState("");
  const [selectedPostId, setSelectedPostId] = useState(null);
  const [commentText, setCommentText] = useState("");
  const [modalVisible, setModalVisible] = useState(false);

  // --- SECURITY: PREVENT SCREENSHOTS ---
  useEffect(() => {
    if (Platform.OS === 'web') return;
    ScreenCapture.preventScreenCaptureAsync();
    return () => {
      ScreenCapture.allowScreenCaptureAsync();
    };
  }, []);

  const selectedPost = posts.find(p => p.id === selectedPostId);

  const handlePost = () => {
    if (!postText.trim()) return;
    const newPost = {
      id: Date.now().toString(),
      author: "New Member", 
      role: "Community Member",
      category: "General",
      avatarColor: "#64748b",
      text: postText,
      comments: [],
      timestamp: "Just now",
      likes: 0,
    };
    setPosts([newPost, ...posts]);
    setPostText("");
  };

  const handleComment = () => {
    if (!commentText.trim() || !selectedPostId) return;
    setPosts(prev => prev.map(p => 
      p.id === selectedPostId ? { ...p, comments: [...p.comments, commentText] } : p
    ));
    setCommentText("");
  };

  return (
    <SafeAreaView className="flex-1 bg-slate-50">
      <StatusBar barStyle="dark-content" />
      
      {/* Header */}
      <View className="bg-white px-6 py-4 border-b border-slate-100 flex-row justify-between items-center">
        <View>
          <Text className="text-2xl font-bold text-purple-600">Tithandizane</Text>
          <Text className="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Safe Community</Text>
        </View>
        <Ionicons name="lock-closed" size={18} color="#94a3b8" />
      </View>

      {/* Legend List */}
      <View className="bg-white py-3 border-b border-slate-100">
        <ScrollView horizontal showsHorizontalScrollIndicator={false} className="px-4">
          {CATEGORY_LEGEND.map((item) => (
            <View key={item.id} className="flex-row items-center mr-6 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
              <View className={`${item.color} w-2 h-2 rounded-full mr-2`} />
              <Text className="text-slate-600 text-xs font-semibold">{item.label}</Text>
            </View>
          ))}
        </ScrollView>
      </View>

      <FlatList
        data={posts}
        keyExtractor={(item) => item.id}
        showsVerticalScrollIndicator={false}
        ListHeaderComponent={
          /* Create Post Card */
          <View className="bg-white p-5 mb-3 shadow-sm">
            <TextInput
              value={postText}
              onChangeText={setPostText}
              placeholder="What would you like to share today?"
              className="text-slate-800 text-base mb-4"
              multiline
            />
            <View className="flex-row justify-between items-center border-t border-slate-50 pt-3">
              <Text className="text-xs text-slate-400 font-medium italic">Privacy protection active</Text>
              <TouchableOpacity 
                onPress={handlePost}
                disabled={!postText.trim()}
                className={`${postText.trim() ? 'bg-purple-500' : 'bg-purple-200'} px-6 py-2 rounded-full`}
              >
                <Text className="text-white font-bold">Post</Text>
              </TouchableOpacity>
            </View>
          </View>
        }
        renderItem={({ item }) => (
          /* Post Card */
          <View className="bg-white mb-2 border-b border-slate-100 p-4">
            <View className="flex-row justify-between items-center mb-3">
              <View className="flex-row items-center">
                <View style={{ backgroundColor: item.avatarColor }} className="w-10 h-10 rounded-full mr-3 items-center justify-center">
                  <Text className="text-white font-bold">{item.author[0]}</Text>
                </View>
                <View>
                  <Text className="font-bold text-slate-900">{item.author}</Text>
                  <Text className="text-[10px] text-rose-500 font-bold uppercase">{item.category}</Text>
                </View>
              </View>
              <Text className="text-[10px] text-slate-400 italic">{item.timestamp}</Text>
            </View>
            <Text className="text-slate-700 leading-6 mb-4">{item.text}</Text>
            
            <View className="flex-row items-center space-x-6">
              <TouchableOpacity className="flex-row items-center">
                <Ionicons name="heart-outline" size={20} color="#64748b" />
                <Text className="text-slate-500 ml-1 text-xs">{item.likes}</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={() => { setSelectedPostId(item.id); setModalVisible(true); }} className="flex-row items-center">
                <Ionicons name="chatbubble-outline" size={18} color="#64748b" />
                <Text className="text-slate-500 ml-1 text-xs">{item.comments.length} Comments</Text>
              </TouchableOpacity>
            </View>
          </View>
        )}
      />

      {/* Discussion Modal */}
      <Modal visible={modalVisible} animationType="slide" presentationStyle="pageSheet">
        <KeyboardAvoidingView behavior={Platform.OS === "ios" ? "padding" : "height"} className="flex-1 bg-white">
          <View className="flex-1">
            <View className="h-1 w-10 bg-slate-200 rounded-full self-center mt-3" />
            <View className="px-5 py-4 border-b border-slate-50 flex-row justify-between items-center">
              <Text className="text-lg font-bold">Safe Discussion</Text>
              <TouchableOpacity onPress={() => setModalVisible(false)}>
                <Text className="text-rose-500 font-bold">Close</Text>
              </TouchableOpacity>
            </View>

            <FlatList
              data={selectedPost?.comments || []}
              keyExtractor={(_, i) => i.toString()}
              contentContainerStyle={{ padding: 20 }}
              renderItem={({ item }) => (
                <View className="flex-row mb-4">
                  <View className="bg-slate-50 p-3 rounded-2xl rounded-tl-none flex-1 border border-slate-100">
                    <Text className="text-slate-700 text-sm">{item}</Text>
                  </View>
                </View>
              )}
            />

            <View className="p-4 border-t border-slate-100">
              <View className="flex-row items-center bg-slate-100 rounded-full px-4 py-1">
                <TextInput
                  value={commentText}
                  onChangeText={setCommentText}
                  placeholder="Offer support..."
                  className="flex-1 h-10"
                />
                <TouchableOpacity onPress={handleComment} disabled={!commentText.trim()}>
                  <Ionicons name="send" size={20} color={commentText.trim() ? "#f43f5e" : "#e2e8f0"} />
                </TouchableOpacity>
              </View>
            </View>
          </View>
        </KeyboardAvoidingView>
      </Modal>
    </SafeAreaView>
  );
}