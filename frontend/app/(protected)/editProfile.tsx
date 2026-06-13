import React, { useState } from 'react';
import {
  View, Text, Image, Pressable, ActivityIndicator,
  ScrollView, KeyboardAvoidingView, Platform, Alert, useWindowDimensions,
} from 'react-native';
import { TextInput } from 'react-native-paper';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { Feather, MaterialCommunityIcons, Ionicons } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import { useRouter } from 'expo-router';
import { useAuth } from '../../context/AuthContext';
import { useThemeToggle } from '../../hooks/useTheme';
import { LinearGradient } from 'expo-linear-gradient';

export default function EditProfile() {
  const router = useRouter();
  const { user, updateUser } = useAuth() as any;
  const { colorScheme } = useThemeToggle();
  const { width } = useWindowDimensions();
  const isDark  = colorScheme === 'dark';
  const isTablet = width >= 768;

  const [name,    setName]    = useState(user?.name  || '');
  const [email,   setEmail]   = useState(user?.email || '');
  const [bio,     setBio]     = useState(user?.bio   || '');
  const [image,   setImage]   = useState<string | null>(user?.profile_url || null);
  const [loading, setLoading] = useState(false);

  const T = {
    bg:       isDark ? '#0f172a' : '#f8fafc',
    card:     isDark ? '#1e293b' : '#ffffff',
    border:   isDark ? '#334155' : '#e2e8f0',
    text:     isDark ? '#f1f5f9' : '#111827',
    subtext:  isDark ? '#94a3b8' : '#6b7280',
    inputBg:  isDark ? '#0f172a'  : '#f9fafb',
    inputOut: isDark ? '#334155' : '#e5e7eb',
  };

  const pickImage = async () => {
    const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (status !== 'granted') {
      Alert.alert('Permission Denied', 'We need access to your photos.');
      return;
    }
    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ImagePicker.MediaTypeOptions.Images,
      allowsEditing: true, aspect: [1, 1], quality: 0.7,
    });
    if (!result.canceled) setImage(result.assets[0].uri);
  };

  const handleSave = async () => {
    if (!name.trim() || !email.trim()) {
      Alert.alert('Error', 'Name and Email are required.');
      return;
    }
    if (!/\S+@\S+\.\S+/.test(email)) {
      Alert.alert('Error', 'Please enter a valid email address.');
      return;
    }
    try {
      setLoading(true);
      await new Promise(r => setTimeout(r, 1600));
      Alert.alert('Success', 'Profile updated successfully!', [
        { text: 'OK', onPress: () => router.back() },
      ]);
    } catch {
      Alert.alert('Update Failed', 'Something went wrong. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={{ flex: 1, backgroundColor: T.bg }}>
      <StatusBar style={isDark ? 'light' : 'dark'} />

      {/* Header */}
      <View style={{
        flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
        paddingHorizontal: 16, paddingVertical: 14,
        borderBottomWidth: 1, borderBottomColor: T.border,
        backgroundColor: T.card,
      }}>
        <Pressable
          onPress={() => router.back()}
          style={({ pressed }) => ({
            width: 38, height: 38, borderRadius: 12,
            backgroundColor: isDark ? '#334155' : '#f1f5f9',
            alignItems: 'center', justifyContent: 'center', opacity: pressed ? 0.7 : 1,
          })}
        >
          <Feather name="chevron-left" size={22} color={T.text} />
        </Pressable>
        <Text style={{ fontSize: 17, fontWeight: '800', color: T.text }}>Edit Profile</Text>
        <View style={{ width: 38 }} />
      </View>

      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{ flex: 1 }}>
        <ScrollView
          contentContainerStyle={{ paddingBottom: 48 }}
          showsVerticalScrollIndicator={false}
        >
          {/* Avatar section */}
          <LinearGradient
            colors={isDark ? ['#2d1b69', '#1e293b'] : ['#f5f3ff', '#f8fafc']}
            style={{ alignItems: 'center', paddingVertical: isTablet ? 48 : 36 }}
          >
            <View style={{ position: 'relative' }}>
              <View style={{
                width: 120, height: 120, borderRadius: 60,
                borderWidth: 4, borderColor: isDark ? '#4c1d95' : '#ede9fe',
                overflow: 'hidden', backgroundColor: isDark ? '#334155' : '#e5e7eb',
                shadowColor: '#7c3aed', shadowOffset: { width: 0, height: 6 },
                shadowOpacity: 0.25, shadowRadius: 12, elevation: 6,
              }}>
                <Image
                  source={{ uri: image || `https://ui-avatars.com/api/?name=${name}&background=7C3AED&color=fff` }}
                  style={{ width: '100%', height: '100%' }}
                />
              </View>
              <Pressable
                onPress={pickImage}
                style={({ pressed }) => ({
                  position: 'absolute', bottom: 0, right: 0,
                  backgroundColor: '#7c3aed', width: 38, height: 38, borderRadius: 19,
                  alignItems: 'center', justifyContent: 'center',
                  borderWidth: 3, borderColor: isDark ? '#0f172a' : '#fff',
                  opacity: pressed ? 0.8 : 1,
                })}
              >
                <MaterialCommunityIcons name="camera-outline" size={18} color="white" />
              </Pressable>
            </View>
            <Pressable onPress={pickImage} style={{ marginTop: 12 }}>
              <Text style={{ color: '#7c3aed', fontWeight: '600', fontSize: 13 }}>Change Profile Photo</Text>
            </Pressable>
          </LinearGradient>

          {/* Form */}
          <View style={{ paddingHorizontal: isTablet ? 48 : 20, marginTop: 24 }}>
            <Text style={{
              fontSize: 11, fontWeight: '700', color: T.subtext,
              letterSpacing: 1.5, textTransform: 'uppercase', marginBottom: 14, marginLeft: 4,
            }}>
              Personal Information
            </Text>

            <View style={{ gap: 14 }}>
              <TextInput
                label="Full Name"
                value={name}
                onChangeText={setName}
                mode="outlined"
                outlineColor={T.inputOut}
                activeOutlineColor="#7c3aed"
                textColor={T.text}
                style={{ backgroundColor: T.inputBg, fontSize: 15 }}
                outlineStyle={{ borderRadius: 14 }}
                left={<TextInput.Icon icon="account-outline" color={T.subtext} />}
                theme={{ colors: { onSurfaceVariant: T.subtext } }}
              />

              <TextInput
                label="Email Address"
                value={email}
                onChangeText={setEmail}
                mode="outlined"
                keyboardType="email-address"
                autoCapitalize="none"
                outlineColor={T.inputOut}
                activeOutlineColor="#7c3aed"
                textColor={T.text}
                style={{ backgroundColor: T.inputBg }}
                outlineStyle={{ borderRadius: 14 }}
                left={<TextInput.Icon icon="email-outline" color={T.subtext} />}
                theme={{ colors: { onSurfaceVariant: T.subtext } }}
              />

              <TextInput
                label="Bio"
                value={bio}
                onChangeText={setBio}
                mode="outlined"
                multiline
                numberOfLines={4}
                placeholder="Tell us about yourself…"
                outlineColor={T.inputOut}
                activeOutlineColor="#7c3aed"
                textColor={T.text}
                style={{ backgroundColor: T.inputBg }}
                outlineStyle={{ borderRadius: 14 }}
                contentStyle={{ paddingTop: 10 }}
                theme={{ colors: { onSurfaceVariant: T.subtext } }}
              />
            </View>

            {/* Privacy note */}
            <View style={{
              flexDirection: 'row', alignItems: 'center', gap: 10,
              backgroundColor: isDark ? '#1e2d3d' : '#eff6ff',
              padding: 14, borderRadius: 14, marginTop: 6,
              borderWidth: 1, borderColor: isDark ? '#1e40af' : '#bfdbfe',
            }}>
              <Ionicons name="information-circle-outline" size={18} color="#3b82f6" />
              <Text style={{ color: isDark ? '#93c5fd' : '#2563eb', fontSize: 12, flex: 1, lineHeight: 18 }}>
                Your email is used for account security and won't be shown publicly.
              </Text>
            </View>

            {/* Save button */}
            <Pressable
              onPress={handleSave}
              disabled={loading}
              style={({ pressed }) => ({
                marginTop: 28, height: 56, borderRadius: 20,
                backgroundColor: loading ? '#a78bfa' : '#7c3aed',
                alignItems: 'center', justifyContent: 'center',
                opacity: pressed ? 0.85 : 1,
                shadowColor: '#7c3aed', shadowOffset: { width: 0, height: 6 },
                shadowOpacity: 0.35, shadowRadius: 12, elevation: 6,
              })}
            >
              {loading
                ? <ActivityIndicator color="white" />
                : <Text style={{ color: '#fff', fontWeight: '800', fontSize: 16 }}>Update Profile</Text>
              }
            </Pressable>

            <Pressable onPress={() => router.back()} style={{ marginTop: 14, alignItems: 'center', paddingVertical: 8 }}>
              <Text style={{ color: T.subtext, fontWeight: '500', fontSize: 14 }}>Cancel Changes</Text>
            </Pressable>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}
