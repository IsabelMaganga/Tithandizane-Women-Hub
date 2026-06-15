import React, { useState, useEffect } from "react";
import {
  View,
  Text,
  Image,
  Pressable,
  ActivityIndicator,
  ScrollView,
  KeyboardAvoidingView,
  Platform,
  Alert,
} from "react-native";
import { TextInput, IconButton } from "react-native-paper";
import { SafeAreaView } from "react-native-safe-area-context";
import { Feather, MaterialCommunityIcons } from "@expo/vector-icons";
import * as ImagePicker from "expo-image-picker";
import { useRouter } from "expo-router";
import { useAuth } from "../../context/AuthContext"; // Assuming your context path

export default function EditProfile() {
  const router = useRouter();
  const { user, updateUser } = useAuth(); // Assuming updateUser exists in context

  // Form State
  const [name, setName] = useState(user?.name || "");
  const [email, setEmail] = useState(user?.email || "");
  const [bio, setBio] = useState(user?.bio || "");
  const [image, setImage] = useState<string | null>(user?.profile_url || null);
  const [loading, setLoading] = useState(false);

  // --- Image Picker Logic ---
  const pickImage = async () => {
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    
    if (status !== "granted") {
      Alert.alert("Permission Denied", "We need access to your photos to change your profile picture.");
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [1, 1],
      quality: 0.7,
    });

    if (!result.canceled) {
      setImage(result.assets[0].uri);
    }
  };

  // --- Save Logic ---
  const handleSave = async () => {
    // Basic Validation
    if (!name.trim() || !email.trim()) {
      Alert.alert("Error", "Name and Email are required.");
      return;
    }

    const emailRegex = /\S+@\S+\.\S+/;
    if (!emailRegex.test(email)) {
      Alert.alert("Error", "Please enter a valid email address.");
      return;
    }

    try {
      setLoading(true);

      // 🔥 Replace with your actual API/Firebase logic
      // e.g., await updateUser({ name, email, bio, image });
      await new Promise((resolve) => setTimeout(resolve, 2000));

      Alert.alert("Success", "Profile updated successfully!", [
        { text: "OK", onPress: () => router.back() }
      ]);
    } catch (error) {
      Alert.alert("Update Failed", "Something went wrong. Please try again.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView className="flex-1 bg-white">
      {/* Custom Header */}
      <View className="flex-row items-center justify-between px-4 py-2 border-b border-gray-100">
        <Pressable 
          onPress={() => router.back()} 
          className="p-2 -ml-2 active:opacity-60"
        >
          <Feather name="chevron-left" size={28} color="#1F2937" />
        </Pressable>
        <Text className="text-lg font-bold text-gray-900">Edit Profile</Text>
        <View className="w-10" /> 
      </View>

      <KeyboardAvoidingView
        behavior={Platform.OS === "ios" ? "padding" : "height"}
        className="flex-1"
      >
        <ScrollView 
          contentContainerStyle={{ paddingBottom: 40 }}
          showsVerticalScrollIndicator={false}
        >
          {/* PROFILE IMAGE SECTION */}
          <View className="items-center my-8">
            <View className="relative">
              <View className="w-32 h-32 rounded-full border-4 border-violet-50 overflow-hidden bg-gray-100 shadow-sm">
                <Image
                  source={{
                    uri: image || `https://ui-avatars.com/api/?name=${name}&background=7C3AED&color=fff`,
                  }}
                  className="w-full h-full"
                />
              </View>
              
              <Pressable
                onPress={pickImage}
                className="absolute bottom-0 right-0 bg-violet-600 w-10 h-10 rounded-full items-center justify-center border-4 border-white shadow-lg active:bg-violet-700"
              >
                <MaterialCommunityIcons name="camera-outline" size={20} color="white" />
              </Pressable>
            </View>
            <Text className="text-violet-600 font-semibold mt-4 text-sm">
              Change Profile Photo
            </Text>
          </View>

          {/* FORM SECTION */}
          <View className="px-6 space-y-5">
            <View>
              <Text className="text-gray-500 font-medium mb-1 ml-1 text-xs uppercase tracking-widest">
                Personal Information
              </Text>
              <TextInput
                label="Full Name"
                value={name}
                onChangeText={setName}
                mode="outlined"
                outlineColor="#F3F4F6"
                activeOutlineColor="#7C3AED"
                className="bg-gray-50 text-base"
                outlineStyle={{ borderRadius: 12 }}
                left={<TextInput.Icon icon="account-outline" color="#9CA3AF" />}
              />
            </View>

            <TextInput
              label="Email Address"
              value={email}
              onChangeText={setEmail}
              mode="outlined"
              keyboardType="email-address"
              autoCapitalize="none"
              outlineColor="#F3F4F6"
              activeOutlineColor="#7C3AED"
              className="bg-gray-50"
              outlineStyle={{ borderRadius: 12 }}
              left={<TextInput.Icon icon="email-outline" color="#9CA3AF" />}
            />

            <TextInput
              label="Bio"
              value={bio}
              onChangeText={setBio}
              mode="outlined"
              multiline
              numberOfLines={4}
              placeholder="Tell us about yourself..."
              outlineColor="#F3F4F6"
              activeOutlineColor="#7C3AED"
              className="bg-gray-50"
              outlineStyle={{ borderRadius: 12 }}
              contentStyle={{ paddingTop: 10 }}
            />

            {/* Privacy Note */}
            <View className="flex-row items-center bg-blue-50 p-4 rounded-xl mt-2">
              <Feather name="info" size={16} color="#3B82F6" />
              <Text className="text-blue-600 text-xs ml-2 flex-1">
                Your email is used for account security and won't be shown publicly.
              </Text>
            </View>
          </View>

          {/* ACTION BUTTONS */}
          <View className="px-6 mt-10">
            <Pressable
              onPress={handleSave}
              disabled={loading}
              className={`h-14 rounded-2xl items-center justify-center shadow-sm ${
                loading ? "bg-violet-300" : "bg-violet-600 active:bg-violet-700"
              }`}
            >
              {loading ? (
                <ActivityIndicator color="white" />
              ) : (
                <Text className="text-white font-bold text-lg">Update Profile</Text>
              )}
            </Pressable>

            <Pressable 
              onPress={() => router.back()}
              className="mt-4 py-2 items-center"
            >
              <Text className="text-gray-400 font-medium">Cancel Changes</Text>
            </Pressable>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}