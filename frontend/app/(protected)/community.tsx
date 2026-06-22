import React, { useState, useEffect } from "react";
import {
  View, Text, TextInput, TouchableOpacity,
  FlatList, Modal, KeyboardAvoidingView, Platform,
  StatusBar, ScrollView
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import * as ScreenCapture from "expo-screen-capture";

// ✅ IMPORT REAL API
import {
  getCommunityPosts,
  createCommunityPost,
  likeCommunityPost,
  commentCommunityPost
} from "@/services/api";

const CATEGORY_LEGEND = [
  { id: 1, label: "Education", color: "bg-blue-500", icon: "book" },
  { id: 2, label: "Health", color: "bg-emerald-500", icon: "medkit" },
  { id: 3, label: "Safety", color: "bg-rose-500", icon: "shield-checkmark" },
];

export default function Community() {

  const [posts, setPosts] = useState([]);
  const [postText, setPostText] = useState("");
  const [selectedPostId, setSelectedPostId] = useState(null);
  const [commentText, setCommentText] = useState("");
  const [modalVisible, setModalVisible] = useState(false);

  const selectedPost = posts.find(p => p.id === selectedPostId);

  // 🔒 Security
  useEffect(() => {
    ScreenCapture.preventScreenCaptureAsync();
    return () => ScreenCapture.allowScreenCaptureAsync();
  }, []);

  // 📥 Load posts
  useEffect(() => {
    loadPosts();
  }, []);

  const loadPosts = async () => {
    try {
      const data = await getCommunityPosts();
      setPosts(data);
    } catch (err) {
      console.log("Load posts error:", err);
    }
  };

  // 📝 Create post
  const handlePost = async () => {
    if (!postText.trim()) return;

    try {
      await createCommunityPost({
        text: postText,
        category: "General",
      });

      setPostText("");
      loadPosts();
    } catch (err) {
      console.log(err);
    }
  };

  // 💬 Comment
  const handleComment = async () => {
    if (!commentText.trim() || !selectedPostId) return;

    try {
      await commentCommunityPost(selectedPostId, commentText);

      setCommentText("");
      loadPosts();
    } catch (err) {
      console.log(err);
    }
  };

  return (
    <SafeAreaView className="flex-1 bg-slate-50">
      <StatusBar barStyle="dark-content" />

      {/* HEADER */}
      <View className="bg-white px-6 py-4 border-b border-slate-100 flex-row justify-between items-center">
        <View>
          <Text className="text-2xl font-bold text-purple-600">Tithandizane</Text>
          <Text className="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
            Safe Community
          </Text>
        </View>
        <Ionicons name="lock-closed" size={18} color="#94a3b8" />
      </View>

      {/* LEGEND */}
      <View className="bg-white py-3 border-b border-slate-100">
        <ScrollView horizontal showsHorizontalScrollIndicator={false} className="px-4">
          {CATEGORY_LEGEND.map(item => (
            <View key={item.id} className="flex-row items-center mr-6 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
              <View className={`${item.color} w-2 h-2 rounded-full mr-2`} />
              <Text className="text-slate-600 text-xs font-semibold">{item.label}</Text>
            </View>
          ))}
        </ScrollView>
      </View>

      {/* POSTS */}
      <FlatList
        data={posts}
        keyExtractor={(item) => item.id.toString()}

        ListHeaderComponent={
          <View className="bg-white p-5 mb-3">
            <TextInput
              value={postText}
              onChangeText={setPostText}
              placeholder="What would you like to share today?"
              multiline
              className="text-slate-800 text-base mb-4"
            />

            <View className="flex-row justify-between items-center border-t border-slate-50 pt-3">
              <Text className="text-xs text-slate-400 italic">
                Privacy protected
              </Text>

              <TouchableOpacity
                onPress={handlePost}
                className={`${postText.trim() ? "bg-purple-500" : "bg-purple-200"} px-6 py-2 rounded-full`}
              >
                <Text className="text-white font-bold">Post</Text>
              </TouchableOpacity>
            </View>
          </View>
        }

        renderItem={({ item }) => (
          <View className="bg-white mb-2 border-b border-slate-100 p-4">

            {/* HEADER */}
            <View className="flex-row justify-between items-center mb-3">

              <View className="flex-row items-center">
                <View className="w-10 h-10 rounded-full mr-3 bg-purple-500 items-center justify-center">
                  <Text className="text-white font-bold">
                    {item.user?.name?.[0]}
                  </Text>
                </View>

                <View>
                  <Text className="font-bold text-slate-900">
                    {item.user?.name}
                  </Text>

                  <Text className="text-[10px] text-rose-500 font-bold uppercase">
                    {item.category}
                  </Text>
                </View>
              </View>

            </View>

            {/* TEXT */}
            <Text className="text-slate-700 mb-4">
              {item.text}
            </Text>

            {/* ACTIONS */}
            <View className="flex-row items-center space-x-6">

              {/* LIKE */}
              <TouchableOpacity
                onPress={async () => {
                  await likeCommunityPost(item.id);
                  loadPosts();
                }}
                className="flex-row items-center"
              >
                <Ionicons name="heart-outline" size={20} color="#64748b" />
                <Text className="ml-1 text-xs text-slate-500">
                  {item.likes_count}
                </Text>
              </TouchableOpacity>

              {/* COMMENT */}
              <TouchableOpacity
                onPress={() => {
                  setSelectedPostId(item.id);
                  setModalVisible(true);
                }}
                className="flex-row items-center"
              >
                <Ionicons name="chatbubble-outline" size={18} color="#64748b" />
                <Text className="ml-1 text-xs text-slate-500">
                  {item.comments_count} Comments
                </Text>
              </TouchableOpacity>

            </View>

          </View>
        )}
      />

      {/* COMMENTS MODAL */}
      <Modal visible={modalVisible} animationType="slide">

        <KeyboardAvoidingView
          behavior={Platform.OS === "ios" ? "padding" : "height"}
          className="flex-1 bg-white"
        >

          {/* HEADER */}
          <View className="px-5 py-4 border-b flex-row justify-between items-center">
            <Text className="text-lg font-bold">Discussion</Text>

            <TouchableOpacity onPress={() => setModalVisible(false)}>
              <Text className="text-rose-500 font-bold">Close</Text>
            </TouchableOpacity>
          </View>

          {/* COMMENTS */}
          <FlatList
            data={selectedPost?.comments || []}
            keyExtractor={(item, index) => index.toString()}
            contentContainerStyle={{ padding: 20 }}

            renderItem={({ item }) => (
              <View className="mb-3 bg-slate-50 p-3 rounded-xl">
                <Text className="text-slate-700 text-sm">
                  {item.user?.name}: {item.comment}
                </Text>
              </View>
            )}
          />

          {/* INPUT */}
          <View className="p-4 border-t">
            <View className="flex-row items-center bg-slate-100 rounded-full px-4">

              <TextInput
                value={commentText}
                onChangeText={setCommentText}
                placeholder="Write a comment..."
                className="flex-1 h-10"
              />

              <TouchableOpacity
                onPress={handleComment}
                disabled={!commentText.trim()}
              >
                <Ionicons
                  name="send"
                  size={20}
                  color={commentText.trim() ? "#f43f5e" : "#cbd5e1"}
                />
              </TouchableOpacity>

            </View>
          </View>

        </KeyboardAvoidingView>

      </Modal>

    </SafeAreaView>
  );
}