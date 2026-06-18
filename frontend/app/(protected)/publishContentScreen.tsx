import React, { useEffect, useState } from 'react';
import {
  View,
  Text,
  TextInput,
  Pressable,
  ScrollView,
  KeyboardAvoidingView,
  Platform,
  ActivityIndicator,
  Alert,
  Image,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { Feather } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import Toast from 'react-native-toast-message';
import {
  getMentorGuidanceContent,
  publishGuidanceContent,
  updateGuidanceContent,
  GuidanceContentPayload,
} from '../../services/api';

const PURPLE = '#7c3aed';
const PURPLE_DARK = '#6d28d9';

type Category = 'menstrual_hygiene' | 'general';

const CATEGORIES: { value: Category; label: string }[] = [
  { value: 'menstrual_hygiene', label: 'Menstrual Hygiene' },
  { value: 'general', label: 'General Issues' },
];

const PublishContentScreen = () => {
  const router = useRouter();
  const { contentId } = useLocalSearchParams<{ contentId?: string }>();
  const isEditing = Boolean(contentId);

  const [title, setTitle] = useState('');
  const [body, setBody] = useState('');
  const [category, setCategory] = useState<Category>('general');
  const [photoUri, setPhotoUri] = useState<string | null>(null);
  const [existingPhotoUrl, setExistingPhotoUrl] = useState<string | null>(null);
  const [removePhoto, setRemovePhoto] = useState(false);
  const [loading, setLoading] = useState(false);
  const [loadingContent, setLoadingContent] = useState(isEditing);

  useEffect(() => {
    if (contentId) {
      loadExistingContent();
    }
  }, [contentId]);

  const loadExistingContent = async () => {
    try {
      setLoadingContent(true);
      const items = await getMentorGuidanceContent();
      const existing = items.find((item) => item.id === Number(contentId));
      if (!existing) {
        Toast.show({ type: 'error', text1: 'Content not found', position: 'top' });
        router.back();
        return;
      }
      setTitle(existing.title);
      setBody(existing.body);
      setCategory(existing.category);
      setExistingPhotoUrl(existing.photo_url ?? null);
    } catch {
      Toast.show({ type: 'error', text1: 'Failed to load content', position: 'top' });
      router.back();
    } finally {
      setLoadingContent(false);
    }
  };

  const pickPhoto = async () => {
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permission needed', 'Allow photo access to add a cover image.');
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true,
      aspect: [16, 9],
      quality: 0.8,
    });

    if (!result.canceled) {
      setPhotoUri(result.assets[0].uri);
      setRemovePhoto(false);
    }
  };

  const clearPhoto = () => {
    if (photoUri) {
      setPhotoUri(null);
      return;
    }
    if (existingPhotoUrl) {
      setRemovePhoto(true);
      setExistingPhotoUrl(null);
    }
  };

  const displayPhoto = photoUri ?? (removePhoto ? null : existingPhotoUrl);

  const handleSubmit = async () => {
    if (!title.trim()) {
      Alert.alert('Missing title', 'Please enter a title for your content.');
      return;
    }
    if (!body.trim()) {
      Alert.alert('Missing content', 'Please write the body of your content.');
      return;
    }

    const payload: GuidanceContentPayload = {
      title: title.trim(),
      body: body.trim(),
      category,
      status: 'published',
    };

    if (photoUri) {
      const ext = photoUri.split('.').pop()?.toLowerCase() ?? 'jpg';
      payload.photo = {
        uri: photoUri,
        type: ext === 'png' ? 'image/png' : 'image/jpeg',
        name: `cover.${ext}`,
      };
    } else if (removePhoto) {
      payload.remove_photo = true;
    }

    setLoading(true);
    try {
      if (isEditing) {
        await updateGuidanceContent(Number(contentId), payload);
        Toast.show({ type: 'success', text1: 'Content updated', position: 'top' });
      } else {
        await publishGuidanceContent(payload);
        Toast.show({ type: 'success', text1: 'Content published', position: 'top' });
      }
      router.back();
    } catch (error: any) {
      Toast.show({
        type: 'error',
        text1: isEditing ? 'Update failed' : 'Publish failed',
        text2: error?.response?.data?.message ?? 'Please try again.',
        position: 'top',
      });
    } finally {
      setLoading(false);
    }
  };

  if (loadingContent) {
    return (
      <View className="flex-1 justify-center items-center bg-white">
        <ActivityIndicator size="large" color={PURPLE} />
      </View>
    );
  }

  return (
    <SafeAreaView className="flex-1 bg-slate-50" edges={['top']}>
      <StatusBar style="light" />

      <View className="bg-[#7c3aed] px-5 pt-2 pb-5">
        <View className="flex-row items-center">
          <Pressable onPress={() => router.back()} className="p-2 -ml-2 mr-2">
            <Feather name="arrow-left" size={22} color="white" />
          </Pressable>
          <Text className="text-xl font-bold text-white flex-1">
            {isEditing ? 'Edit Content' : 'Publish Content'}
          </Text>
        </View>
        <Text className="text-violet-200 text-sm mt-1 ml-10">
          Share educational guidance with users
        </Text>
      </View>

      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        className="flex-1"
      >
        <ScrollView className="flex-1 px-5 pt-5" showsVerticalScrollIndicator={false}>
          <View className="mb-5">
            <Text className="text-slate-900 font-bold mb-2 ml-1">Title *</Text>
            <TextInput
              className="bg-white rounded-2xl border border-slate-100 px-4 py-4 text-slate-800 font-medium"
              placeholder="Enter a clear, descriptive title"
              placeholderTextColor="#94a3b8"
              value={title}
              onChangeText={setTitle}
              maxLength={255}
            />
          </View>

          <View className="mb-5">
            <Text className="text-slate-900 font-bold mb-2 ml-1">Content *</Text>
            <TextInput
              className="bg-white rounded-2xl border border-slate-100 px-4 py-4 text-slate-800 font-medium min-h-[180px]"
              placeholder="Write your educational content here..."
              placeholderTextColor="#94a3b8"
              value={body}
              onChangeText={setBody}
              multiline
              textAlignVertical="top"
            />
          </View>

          <View className="mb-5">
            <Text className="text-slate-900 font-bold mb-2 ml-1">
              Cover Photo <Text className="text-slate-400 font-normal">(optional)</Text>
            </Text>

            {displayPhoto ? (
              <View className="relative rounded-2xl overflow-hidden border border-slate-100">
                <Image
                  source={{ uri: displayPhoto }}
                  className="w-full h-44"
                  resizeMode="cover"
                />
                <Pressable
                  onPress={clearPhoto}
                  className="absolute top-3 right-3 bg-black/50 w-8 h-8 rounded-full items-center justify-center"
                >
                  <Feather name="x" size={16} color="white" />
                </Pressable>
              </View>
            ) : (
              <Pressable
                onPress={pickPhoto}
                className="bg-white rounded-2xl border-2 border-dashed border-violet-200 py-8 items-center"
              >
                <View className="bg-violet-50 w-12 h-12 rounded-full items-center justify-center mb-2">
                  <Feather name="image" size={22} color={PURPLE} />
                </View>
                <Text className="text-[#7c3aed] font-bold text-sm">Add cover photo</Text>
                <Text className="text-slate-400 text-xs mt-1">Tap to choose from gallery</Text>
              </Pressable>
            )}
          </View>

          <View className="mb-5">
            <Text className="text-slate-900 font-bold mb-2 ml-1">Category *</Text>
            <View className="flex-row gap-2">
              {CATEGORIES.map((cat) => (
                <Pressable
                  key={cat.value}
                  onPress={() => setCategory(cat.value)}
                  className={`flex-1 py-3 rounded-2xl items-center border ${
                    category === cat.value
                      ? 'bg-[#7c3aed] border-[#7c3aed]'
                      : 'bg-white border-slate-100'
                  }`}
                >
                  <Text
                    className={`text-xs font-bold text-center px-1 ${
                      category === cat.value ? 'text-white' : 'text-slate-500'
                    }`}
                  >
                    {cat.label}
                  </Text>
                </Pressable>
              ))}
            </View>
          </View>

          <Pressable
            onPress={handleSubmit}
            disabled={loading}
            className={`bg-[#6d28d9] py-4 rounded-2xl items-center mb-10 ${
              loading ? 'opacity-70' : ''
            }`}
          >
            {loading ? (
              <ActivityIndicator color="white" />
            ) : (
              <Text className="text-white font-bold text-base">
                {isEditing ? 'Save Changes' : 'Publish'}
              </Text>
            )}
          </Pressable>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
};

export default PublishContentScreen;
