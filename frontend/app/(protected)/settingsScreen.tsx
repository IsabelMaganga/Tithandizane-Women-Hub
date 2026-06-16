import React, { useState } from 'react';
import {
  View, Text, Pressable, Modal, TouchableOpacity,
  FlatList, Image, ScrollView, Switch, useWindowDimensions,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { useAuth } from '../../context/AuthContext';
import { useThemeToggle } from '../../hooks/useTheme';
import {
  FontAwesome, MaterialIcons, MaterialCommunityIcons, AntDesign, Ionicons, Feather,
} from '@expo/vector-icons';
import i18n from 'i18next';
import { useRouter } from 'expo-router';
import { useTranslation } from 'react-i18next';
import { LinearGradient } from 'expo-linear-gradient';

const LANGUAGES = [
  { code: 'en', label: 'English' },
  { code: 'ch', label: 'Chichewa' },
  { code: 'tu', label: 'Tumbuka' },
];

// ─── Setting row ─────────────────────────────────────────────────────────────
// Defined outside the screen component to prevent re-mounting on state change
type SettingItemProps = {
  icon: React.ReactNode;
  label: string;
  value?: string;
  onPress?: () => void;
  isLast?: boolean;
  borderColor: string;
  textPrim: string;
  textSec: string;
  dividerBg: string;
};

const SettingItem = ({
  icon, label, value, onPress, isLast = false,
  borderColor, textPrim, textSec, dividerBg,
}: SettingItemProps) => (
  <Pressable
    onPress={onPress}
    style={({ pressed }) => ({
      flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
      paddingHorizontal: 16, paddingVertical: 14,
      backgroundColor: pressed ? dividerBg : 'transparent',
      borderBottomWidth: isLast ? 0 : 1, borderBottomColor: borderColor,
    })}
  >
    <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
      {icon}
      <Text style={{ fontSize: 15, fontWeight: '500', color: textPrim }}>{label}</Text>
    </View>
    <View style={{ flexDirection: 'row', alignItems: 'center', gap: 6 }}>
      {value && <Text style={{ fontSize: 13, color: textSec }}>{value}</Text>}
      <MaterialIcons name="arrow-forward-ios" size={13} color={textSec} />
    </View>
  </Pressable>
);

// ─── Section label ─────────────────────────────────────────────────────────────
const SectionLabel = ({ label, color }: { label: string; color: string }) => (
  <Text style={{
    fontSize: 11, fontWeight: '700', color, letterSpacing: 1.5,
    textTransform: 'uppercase', marginTop: 28, marginBottom: 8, marginLeft: 4,
  }}>
    {label}
  </Text>
);

export default function SettingsScreen() {
  const { t } = useTranslation('settings');
  const router = useRouter();
  const { user, logout } = useAuth();
  const { colorScheme, toggleTheme } = useThemeToggle();
  const { width } = useWindowDimensions();
  const isDark = colorScheme === 'dark';

  const [modalVisible, setModalVisible] = useState(false);
  const [pushNotifications, setPushNotifications] = useState(true);
  const [selectedLanguage, setSelectedLanguage] = useState(i18n.language || 'en');

  const currentLanguageLabel = LANGUAGES.find(l => l.code === selectedLanguage)?.label || 'English';

  const changeLanguage = (lang: string) => {
    i18n.changeLanguage(lang);
    setSelectedLanguage(lang);
    setModalVisible(false);
  };

  // Theme tokens
  const T = {
    bg:         isDark ? '#0f172a' : '#f8fafc',
    card:       isDark ? '#1e293b' : '#ffffff',
    border:     isDark ? '#334155' : '#e2e8f0',
    divider:    isDark ? '#273344' : '#f1f5f9',
    text:       isDark ? '#f1f5f9' : '#111827',
    subtext:    isDark ? '#94a3b8' : '#6b7280',
    iconBg:     (c: string) => isDark ? `${c}22` : `${c}18`,
  };

  const cardStyle = {
    backgroundColor: T.card, borderRadius: 20,
    borderWidth: 1, borderColor: T.border,
    overflow: 'hidden' as const,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: isDark ? 0.3 : 0.06,
    shadowRadius: 8, elevation: 2,
  };

  return (
    <SafeAreaView style={{ flex: 1, backgroundColor: T.bg }}>
      <StatusBar style={isDark ? 'light' : 'dark'} />

      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingHorizontal: 20, paddingBottom: 48 }}
      >
        {/* Header */}
        <Text style={{ fontSize: 30, fontWeight: '900', color: T.text, marginTop: 20, marginBottom: 20 }}>
          Settings
        </Text>

        {/* Profile card */}
        <Pressable
          onPress={() => router.push('/editProfile')}
          style={({ pressed }) => ({
            ...cardStyle,
            padding: 16,
            flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
            opacity: pressed ? 0.85 : 1,
          })}
        >
          <View style={{ flexDirection: 'row', alignItems: 'center', gap: 14 }}>
            <View style={{ position: 'relative' }}>
              {user?.profile_url ? (
                <Image source={{ uri: user.profile_url }} style={{ width: 64, height: 64, borderRadius: 32 }} />
              ) : (
                <View style={{
                  width: 64, height: 64, borderRadius: 32,
                  backgroundColor: T.iconBg('#7c3aed'),
                  alignItems: 'center', justifyContent: 'center',
                }}>
                  <MaterialCommunityIcons name="face-woman-profile" size={34} color="#7c3aed" />
                </View>
              )}
              <View style={{
                position: 'absolute', bottom: 1, right: 1,
                width: 14, height: 14, borderRadius: 7,
                backgroundColor: '#10b981', borderWidth: 2, borderColor: T.card,
              }} />
            </View>
            <View>
              <Text style={{ fontSize: 17, fontWeight: '800', color: T.text }}>{user?.name || 'User'}</Text>
              <Text style={{ fontSize: 12, color: T.subtext, marginTop: 2 }}>{user?.email || 'Account details'}</Text>
            </View>
          </View>
          <View style={{
            backgroundColor: T.iconBg('#7c3aed'), borderRadius: 10,
            paddingHorizontal: 10, paddingVertical: 6,
          }}>
            <Text style={{ fontSize: 11, fontWeight: '800', color: '#7c3aed', letterSpacing: 0.5 }}>EDIT</Text>
          </View>
        </Pressable>

        {/* Preferences */}
        <SectionLabel label="Preferences" color={T.subtext} />
        <View style={cardStyle}>
          <SettingItem
            label="Language"
            value={currentLanguageLabel}
            icon={
              <View style={{ width: 36, height: 36, borderRadius: 10, backgroundColor: T.iconBg('#3b82f6'), alignItems: 'center', justifyContent: 'center' }}>
                <FontAwesome name="language" size={18} color="#3b82f6" />
              </View>
            }
            onPress={() => setModalVisible(true)}
            borderColor={T.border} textPrim={T.text} textSec={T.subtext} dividerBg={T.divider}
          />

          {/* Notifications toggle */}
          <View style={{
            flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
            paddingHorizontal: 16, paddingVertical: 14,
            borderBottomWidth: 1, borderBottomColor: T.border,
          }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
              <View style={{ width: 36, height: 36, borderRadius: 10, backgroundColor: T.iconBg('#f59e0b'), alignItems: 'center', justifyContent: 'center' }}>
                <Ionicons name="notifications" size={18} color="#f59e0b" />
              </View>
              <Text style={{ fontSize: 15, fontWeight: '500', color: T.text }}>Notifications</Text>
            </View>
            <Switch
              value={pushNotifications}
              onValueChange={setPushNotifications}
              trackColor={{ false: isDark ? '#334155' : '#d1d5db', true: '#7c3aed' }}
              thumbColor="#ffffff"
            />
          </View>

          {/* Dark mode toggle */}
          <View style={{
            flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between',
            paddingHorizontal: 16, paddingVertical: 14,
          }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
              <View style={{ width: 36, height: 36, borderRadius: 10, backgroundColor: T.iconBg('#6b7280'), alignItems: 'center', justifyContent: 'center' }}>
                <Ionicons name={isDark ? 'moon' : 'sunny'} size={18} color={isDark ? '#a78bfa' : '#f59e0b'} />
              </View>
              <Text style={{ fontSize: 15, fontWeight: '500', color: T.text }}>Dark Mode</Text>
            </View>
            <Switch
              value={isDark}
              onValueChange={toggleTheme}
              trackColor={{ false: isDark ? '#334155' : '#d1d5db', true: '#7c3aed' }}
              thumbColor="#ffffff"
            />
          </View>
        </View>

        {/* Security */}
        <SectionLabel label="Security" color={T.subtext} />
        <View style={cardStyle}>
          <SettingItem
            label="Two-Factor Auth"
            value="Enabled"
            icon={
              <View style={{ width: 36, height: 36, borderRadius: 10, backgroundColor: T.iconBg('#10b981'), alignItems: 'center', justifyContent: 'center' }}>
                <MaterialCommunityIcons name="shield-check" size={20} color="#10b981" />
              </View>
            }
            onPress={() => router.push('../twoFactorAuthScreen')}
            borderColor={T.border} textPrim={T.text} textSec={T.subtext} dividerBg={T.divider}
          />
          <SettingItem
            label="Change Password"
            isLast
            icon={
              <View style={{ width: 36, height: 36, borderRadius: 10, backgroundColor: T.iconBg('#6b7280'), alignItems: 'center', justifyContent: 'center' }}>
                <Ionicons name="lock-closed" size={17} color={T.subtext} />
              </View>
            }
            onPress={() => router.push('../changePasswordScreen')}
            borderColor={T.border} textPrim={T.text} textSec={T.subtext} dividerBg={T.divider}
          />
        </View>

        {/* Support */}
        <SectionLabel label="Support" color={T.subtext} />
        <View style={cardStyle}>
          <SettingItem
            label="Help Center"
            icon={
              <View style={{ width: 36, height: 36, borderRadius: 10, backgroundColor: T.iconBg('#6b7280'), alignItems: 'center', justifyContent: 'center' }}>
                <Ionicons name="help-circle-outline" size={20} color={T.subtext} />
              </View>
            }
            onPress={() => {}}
            borderColor={T.border} textPrim={T.text} textSec={T.subtext} dividerBg={T.divider}
          />
          <SettingItem
            label="Privacy Policy"
            icon={
              <View style={{ width: 36, height: 36, borderRadius: 10, backgroundColor: T.iconBg('#6b7280'), alignItems: 'center', justifyContent: 'center' }}>
                <MaterialIcons name="privacy-tip" size={18} color={T.subtext} />
              </View>
            }
            onPress={() => router.push('../privacyPolicyScreen')}
            borderColor={T.border} textPrim={T.text} textSec={T.subtext} dividerBg={T.divider}
          />
          <SettingItem
            label="About"
            isLast
            icon={
              <View style={{ width: 36, height: 36, borderRadius: 10, backgroundColor: T.iconBg('#6b7280'), alignItems: 'center', justifyContent: 'center' }}>
                <Ionicons name="information-circle-outline" size={20} color={T.subtext} />
              </View>
            }
            onPress={() => router.push('../aboutScreen')}
            borderColor={T.border} textPrim={T.text} textSec={T.subtext} dividerBg={T.divider}
          />
        </View>

        {/* Sign out */}
        <Pressable
          onPress={logout}
          style={({ pressed }) => ({
            flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 8,
            backgroundColor: isDark ? '#2d1515' : '#fff1f2',
            borderWidth: 1, borderColor: isDark ? '#7f1d1d' : '#fecdd3',
            paddingVertical: 16, borderRadius: 18, marginTop: 28,
            opacity: pressed ? 0.8 : 1,
          })}
        >
          <AntDesign name="logout" size={17} color="#ef4444" />
          <Text style={{ color: '#ef4444', fontWeight: '700', fontSize: 15 }}>Sign Out</Text>
        </Pressable>

        <Pressable style={{ marginTop: 12, paddingVertical: 8, alignItems: 'center' }}>
          <Text style={{ color: '#ef4444', fontSize: 12, fontWeight: '500', opacity: 0.7 }}>Delete Account</Text>
        </Pressable>

        {/* Footer */}
        <View style={{ alignItems: 'center', marginTop: 24 }}>
          <Image
            source={require('../../assets/images/Ellipse 3.png')}
            style={{ width: 36, height: 36, borderRadius: 18, opacity: 0.4 }}
          />
          <Text style={{ color: T.subtext, fontSize: 10, marginTop: 8, letterSpacing: 1.5, textTransform: 'uppercase' }}>
            Version 1.0.4 (Build 122)
          </Text>
        </View>
      </ScrollView>

      {/* Language Modal */}
      <Modal animationType="fade" transparent visible={modalVisible} onRequestClose={() => setModalVisible(false)}>
        <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.55)', justifyContent: 'center', alignItems: 'center', paddingHorizontal: 24 }}>
          <View style={{ width: '100%', backgroundColor: T.card, borderRadius: 28, padding: 24 }}>
            <Text style={{ fontSize: 20, fontWeight: '800', color: T.text, marginBottom: 16 }}>Select Language</Text>
            <FlatList
              data={LANGUAGES}
              keyExtractor={item => item.code}
              scrollEnabled={false}
              renderItem={({ item }) => {
                const active = item.code === selectedLanguage;
                return (
                  <TouchableOpacity
                    onPress={() => changeLanguage(item.code)}
                    style={{
                      flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center',
                      paddingVertical: 14, paddingHorizontal: 16,
                      borderRadius: 14, marginBottom: 4,
                      backgroundColor: active ? (isDark ? '#2d1b69' : '#f5f3ff') : 'transparent',
                    }}
                  >
                    <Text style={{ fontSize: 15, fontWeight: active ? '700' : '500', color: active ? '#7c3aed' : T.text }}>
                      {item.label}
                    </Text>
                    {active && <MaterialIcons name="check" size={20} color="#7c3aed" />}
                  </TouchableOpacity>
                );
              }}
            />
            <TouchableOpacity
              onPress={() => setModalVisible(false)}
              style={{
                marginTop: 12, paddingVertical: 14,
                backgroundColor: T.divider, borderRadius: 14, alignItems: 'center',
              }}
            >
              <Text style={{ fontWeight: '700', color: T.text }}>Close</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
}
